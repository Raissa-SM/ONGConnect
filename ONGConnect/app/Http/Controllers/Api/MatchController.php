<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DemandaResource;
use App\Models\Demanda;
use App\Models\Voluntario;
use App\Support\Geo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MatchController extends Controller
{
    // ── Pesos do score ────────────────────────────────────────────────────────
    private const PESO_CATEGORIA   = 0.6;
    private const PESO_PROXIMIDADE = 0.4;
    private const RAIO_PADRAO_KM   = 50.0;

    // ─────────────────────────────────────────────────────────────────────────
    // SUGESTÕES
    // ─────────────────────────────────────────────────────────────────────────

    #[OA\Get(
        path: '/api/match/sugestoes',
        summary: 'Sugere demandas abertas rankeadas por score de match para o voluntário autenticado',
        security: [['sanctum' => []]],
        tags: ['Match'],
        parameters: [
            new OA\Parameter(
                name: 'raio_km', in: 'query', required: false,
                description: 'Raio máximo de busca em km (padrão: 50)',
                schema: new OA\Schema(type: 'number', example: 50)
            ),
            new OA\Parameter(
                name: 'page', in: 'query', required: false,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista paginada de sugestões ordenadas por score',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'demanda', type: 'object'),
                                new OA\Property(property: 'score',   type: 'object',
                                    properties: [
                                        new OA\Property(property: 'total',        type: 'number', example: 0.78),
                                        new OA\Property(property: 'categoria',    type: 'number', example: 0.80),
                                        new OA\Property(property: 'proximidade',  type: 'number', example: 0.75),
                                        new OA\Property(property: 'distancia_km', type: 'number', example: 12.5),
                                    ]
                                ),
                            ]
                        )),
                        new OA\Property(property: 'meta', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Apenas voluntários recebem sugestões'),
        ]
    )]
    public function sugestoes(Request $request): JsonResponse
    {
        if (!$request->user()->isVoluntario()) {
            return response()->json(['message' => 'Sugestões de match disponíveis apenas para voluntários.'], 403);
        }

        $voluntario = $request->user()->voluntario->load('categorias');

        if (!$voluntario->aptoParaMatch()) {
            return response()->json([
                'message' => 'Para receber sugestões, complete seu perfil com localização e ao menos uma categoria de interesse.',
                'data'    => [],
                'meta'    => ['total' => 0, 'raio_km' => self::RAIO_PADRAO_KM],
            ]);
        }

        $raioKm = max(1.0, min(500.0, (float) $request->input('raio_km', self::RAIO_PADRAO_KM)));

        // IDs de demandas em que o voluntário já está inscrito (qualquer status)
        $jaInscritas = $voluntario->inscricoes()->pluck('demanda_id')->toArray();

        // Demandas abertas que ainda têm vagas, fora do conjunto já inscrito
        $demandas = Demanda::with(['ong', 'categorias'])
            ->aberta()
            ->whereNotIn('id', $jaInscritas)
            ->get()
            ->filter(fn ($d) => $d->vagasDisponiveis() > 0);

        // Calcula score e aplica filtro de raio para demandas com localização
        $sugestoes = $demandas
            ->map(fn ($demanda) => [
                'demanda' => $demanda,
                'score'   => $this->calcularScore($voluntario, $demanda),
            ])
            ->filter(function ($item) use ($voluntario, $raioKm) {
                $d = $item['demanda'];
                if ($d->latitude && $d->longitude) {
                    return Geo::dentroDe(
                        $voluntario->latitude, $voluntario->longitude,
                        $d->latitude,          $d->longitude,
                        $raioKm
                    );
                }
                return true; // demanda sem localização sempre entra
            })
            ->filter(fn ($item) => $item['score']['total'] > 0)
            ->sortByDesc(fn ($item) => $item['score']['total'])
            ->values();

        // Paginação manual (scoring ocorre em PHP após eager-load)
        $perPage   = 10;
        $page      = max(1, (int) $request->input('page', 1));
        $total     = $sugestoes->count();
        $paginated = $sugestoes->forPage($page, $perPage)->values();

        return response()->json([
            'data' => $paginated->map(fn ($item) => [
                'demanda' => new DemandaResource($item['demanda']),
                'score'   => $item['score'],
            ]),
            'meta' => [
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => (int) ceil($total / max(1, $perPage)),
                'raio_km'      => $raioKm,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SCORE DETALHADO
    // ─────────────────────────────────────────────────────────────────────────

    #[OA\Get(
        path: '/api/match/score',
        summary: 'Retorna o score de match entre o voluntário autenticado e uma demanda específica. ONGs veem os scores de todos os seus inscritos.',
        security: [['sanctum' => []]],
        tags: ['Match'],
        parameters: [
            new OA\Parameter(
                name: 'demanda_id', in: 'query', required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200,  description: 'Score detalhado'),
            new OA\Response(response: 401,  description: 'Não autenticado'),
            new OA\Response(response: 403,  description: 'Sem permissão'),
            new OA\Response(response: 404,  description: 'Demanda não encontrada'),
            new OA\Response(response: 422,  description: 'Parâmetro demanda_id ausente'),
        ]
    )]
    public function score(Request $request): JsonResponse
    {
        $demandaId = $request->input('demanda_id');

        if (!$demandaId) {
            return response()->json(['message' => 'Informe o parâmetro demanda_id.'], 422);
        }

        $demanda = Demanda::with(['categorias', 'ong'])->findOrFail($demandaId);

        // ── Voluntário: score contra si mesmo ────────────────────────────────
        if ($request->user()->isVoluntario()) {
            $voluntario = $request->user()->voluntario->load('categorias');
            $score      = $this->calcularScore($voluntario, $demanda);

            $catsDemandaIds    = $demanda->categorias->pluck('id')->toArray();
            $catsVoluntarioIds = $voluntario->categorias->pluck('id')->toArray();
            $idsEmComum        = array_intersect($catsDemandaIds, $catsVoluntarioIds);

            $categoriesEmComum = $demanda->categorias
                ->filter(fn ($c) => in_array($c->id, $idsEmComum))
                ->pluck('nome')
                ->values()
                ->toArray();

            return response()->json([
                'voluntario_id' => $voluntario->id,
                'demanda_id'    => $demanda->id,
                'demanda'       => [
                    'titulo' => $demanda->titulo,
                    'ong'    => $demanda->ong->razao_social,
                    'status' => $demanda->status->value,
                ],
                'score' => array_merge($score, [
                    'categorias_em_comum' => $categoriesEmComum,
                    'apto_para_match'     => $voluntario->aptoParaMatch(),
                ]),
            ]);
        }

        // ── ONG: scores de todos os inscritos na sua demanda ─────────────────
        if ($request->user()->isOng()) {
            if ($request->user()->ong?->id !== $demanda->ong_id) {
                return response()->json(['message' => 'Sem permissão para consultar scores desta demanda.'], 403);
            }

            $demanda->load('inscricoes.voluntario.categorias');

            $scores = $demanda->inscricoes
                ->map(function ($inscricao) use ($demanda) {
                    $vol   = $inscricao->voluntario->load('categorias');
                    $score = $this->calcularScore($vol, $demanda);

                    return [
                        'inscricao_id'  => $inscricao->id,
                        'voluntario_id' => $vol->id,
                        'nome'          => $vol->user?->name,
                        'status'        => $inscricao->status->value,
                        'score'         => $score,
                    ];
                })
                ->sortByDesc(fn ($s) => $s['score']['total'])
                ->values();

            return response()->json([
                'demanda_id' => $demanda->id,
                'titulo'     => $demanda->titulo,
                'scores'     => $scores,
            ]);
        }

        return response()->json(['message' => 'Sem permissão.'], 403);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ALGORITMO DE SCORE
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Score = 60 % categoria + 40 % proximidade
     *
     * Categoria: sobreposição entre categorias do voluntário e da demanda.
     *   - Sem categorias na demanda → neutro (0.5)
     *   - Sem categorias no voluntário → 0.0
     *   - Caso normal → |intersecção| / |cats da demanda|
     *
     * Proximidade (Haversine, raio 50 km):
     *   - Algum dos dois sem localização → neutro (0.5)
     *   - Caso normal → Geo::fatorProximidade()
     */
    private function calcularScore(Voluntario $voluntario, Demanda $demanda): array
    {
        // ── Categoria ─────────────────────────────────────────────────────────
        $catsDemanda    = $demanda->categorias->pluck('id')->toArray();
        $catsVoluntario = $voluntario->categorias->pluck('id')->toArray();

        if (empty($catsDemanda)) {
            $scoreCategoria = 0.5;
        } elseif (empty($catsVoluntario)) {
            $scoreCategoria = 0.0;
        } else {
            $intersecao     = count(array_intersect($catsDemanda, $catsVoluntario));
            $scoreCategoria = $intersecao / count($catsDemanda);
        }

        // ── Proximidade ───────────────────────────────────────────────────────
        $distanciaKm = null;

        if ($voluntario->possuiLocalizacao() && $demanda->latitude && $demanda->longitude) {
            $distanciaKm      = Geo::distanciaKm(
                $voluntario->latitude, $voluntario->longitude,
                $demanda->latitude,    $demanda->longitude
            );
            $scoreProximidade = Geo::fatorProximidade(
                $voluntario->latitude, $voluntario->longitude,
                $demanda->latitude,    $demanda->longitude
            );
        } else {
            $scoreProximidade = 0.5; // sem localização → neutro
        }

        // ── Total ─────────────────────────────────────────────────────────────
        $scoreTotal = (self::PESO_CATEGORIA * $scoreCategoria)
                    + (self::PESO_PROXIMIDADE * $scoreProximidade);

        return [
            'total'        => round($scoreTotal, 4),
            'categoria'    => round($scoreCategoria, 4),
            'proximidade'  => round($scoreProximidade, 4),
            'distancia_km' => $distanciaKm !== null ? round($distanciaKm, 2) : null,
        ];
    }
}
