<?php

namespace App\Http\Controllers\Api;

use App\Enums\AutorAvaliacao;
use App\Enums\StatusInscricao;
use App\Http\Controllers\Controller;
use App\Http\Resources\DemandaResource;
use App\Models\Avaliacao;
use App\Models\Demanda;
use App\Models\Inscricao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DashboardController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // DASHBOARD DO VOLUNTÁRIO
    // ─────────────────────────────────────────────────────────────────────────

    #[OA\Get(
        path: '/api/dashboard/voluntario',
        summary: 'Resumo e estatísticas do voluntário autenticado',
        security: [['sanctum' => []]],
        tags: ['Dashboard'],
        responses: [
            new OA\Response(response: 200,  description: 'Dados do dashboard'),
            new OA\Response(response: 401,  description: 'Não autenticado'),
            new OA\Response(response: 403,  description: 'Apenas voluntários'),
        ]
    )]
    public function voluntario(Request $request): JsonResponse
    {
        if (!$request->user()->isVoluntario()) {
            return response()->json(['message' => 'Acesso exclusivo para voluntários.'], 403);
        }

        $voluntario = $request->user()->voluntario->load('categorias');

        // ── Inscrições ────────────────────────────────────────────────────────
        $inscricoes = Inscricao::with(['demanda.ong'])
            ->where('voluntario_id', $voluntario->id)
            ->orderByDesc('created_at')
            ->get();

        $porStatus = collect(StatusInscricao::cases())
            ->mapWithKeys(fn ($s) => [
                $s->value => $inscricoes->filter(fn ($i) => $i->status === $s)->count(),
            ]);

        // ── Próximas atividades (aceitas com data_inicio futura) ──────────────
        $proximas = $inscricoes
            ->filter(fn ($i) =>
                $i->status === StatusInscricao::Aceita
                && $i->demanda->data_inicio
                && $i->demanda->data_inicio->isFuture()
            )
            ->sortBy(fn ($i) => $i->demanda->data_inicio)
            ->take(5)
            ->values()
            ->map(fn ($i) => [
                'inscricao_id' => $i->id,
                'status'       => $i->status->value,
                'demanda'      => [
                    'id'          => $i->demanda->id,
                    'titulo'      => $i->demanda->titulo,
                    'ong'         => $i->demanda->ong->razao_social,
                    'data_inicio' => $i->demanda->data_inicio->format('d/m/Y'),
                    'cidade'      => $i->demanda->cidade,
                ],
            ]);

        // ── Últimas inscrições ────────────────────────────────────────────────
        $ultimas = $inscricoes->take(5)->map(fn ($i) => [
            'inscricao_id' => $i->id,
            'status'       => $i->status->value,
            'status_label' => $i->status->label(),
            'demanda'      => [
                'id'     => $i->demanda->id,
                'titulo' => $i->demanda->titulo,
                'ong'    => $i->demanda->ong->razao_social,
            ],
            'created_at'   => $i->created_at->format('d/m/Y'),
        ]);

        // ── Avaliações recebidas ──────────────────────────────────────────────
        $avaliacoes = Avaliacao::where('autor_tipo', AutorAvaliacao::ONG->value)
            ->whereHas('inscricao', fn ($q) => $q->where('voluntario_id', $voluntario->id))
            ->orderByDesc('created_at')
            ->get();

        $mediaAvaliacoes = $avaliacoes->count() >= 3
            ? round($avaliacoes->avg('nota'), 2)
            : null;

        $ultimasAvaliacoes = $avaliacoes->take(3)->map(fn ($a) => [
            'nota'       => $a->nota,
            'comentario' => $a->comentario,
            'created_at' => $a->created_at->format('d/m/Y'),
        ]);

        return response()->json([
            'voluntario' => [
                'id'              => $voluntario->id,
                'nome'            => $request->user()->name,
                'cidade'          => $voluntario->cidade,
                'categorias'      => $voluntario->categorias->pluck('nome'),
                'media_avaliacoes' => $mediaAvaliacoes,
                'apto_para_match' => $voluntario->aptoParaMatch(),
            ],
            'resumo' => [
                'total_inscricoes'      => $inscricoes->count(),
                'inscricoes_por_status' => $porStatus,
            ],
            'proximas_atividades'        => $proximas,
            'ultimas_inscricoes'         => $ultimas,
            'ultimas_avaliacoes_recebidas' => $ultimasAvaliacoes,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DASHBOARD DA ONG
    // ─────────────────────────────────────────────────────────────────────────

    #[OA\Get(
        path: '/api/dashboard/ong',
        summary: 'Resumo e estatísticas da ONG autenticada',
        security: [['sanctum' => []]],
        tags: ['Dashboard'],
        responses: [
            new OA\Response(response: 200, description: 'Dados do dashboard'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Apenas ONGs'),
        ]
    )]
    public function ong(Request $request): JsonResponse
    {
        if (!$request->user()->isOng()) {
            return response()->json(['message' => 'Acesso exclusivo para ONGs.'], 403);
        }

        $ong = $request->user()->ong;

        // ── Demandas ──────────────────────────────────────────────────────────
        $demandas = Demanda::where('ong_id', $ong->id)->get();

        $demandasPorStatus = collect(\App\Enums\StatusDemanda::cases())
            ->mapWithKeys(fn ($s) => [
                $s->value => $demandas->filter(fn ($d) => $d->status === $s)->count(),
            ]);

        // ── Inscrições (de todas as demandas desta ONG) ───────────────────────
        $inscricoes = Inscricao::whereHas('demanda', fn ($q) => $q->where('ong_id', $ong->id))
            ->with(['demanda', 'voluntario.user'])
            ->orderByDesc('created_at')
            ->get();

        $inscricoesPorStatus = collect(StatusInscricao::cases())
            ->mapWithKeys(fn ($s) => [
                $s->value => $inscricoes->filter(fn ($i) => $i->status === $s)->count(),
            ]);

        // ── Avaliações recebidas (feitas por voluntários) ─────────────────────
        $avaliacoes = Avaliacao::where('autor_tipo', AutorAvaliacao::Voluntario->value)
            ->whereHas('inscricao.demanda', fn ($q) => $q->where('ong_id', $ong->id))
            ->orderByDesc('created_at')
            ->get();

        $mediaAvaliacoes = $avaliacoes->count() >= 3
            ? round($avaliacoes->avg('nota'), 2)
            : null;

        // ── Demandas abertas com vagas ────────────────────────────────────────
        $demandasAbertas = Demanda::with('categorias')
            ->aberta()
            ->where('ong_id', $ong->id)
            ->get()
            ->filter(fn ($d) => $d->vagasDisponiveis() > 0)
            ->sortByDesc(fn ($d) => $d->vagasDisponiveis())
            ->values();

        // ── Últimas inscrições pendentes ──────────────────────────────────────
        $inscricoesPendentes = $inscricoes
            ->filter(fn ($i) => $i->status === StatusInscricao::Pendente)
            ->take(5)
            ->values()
            ->map(fn ($i) => [
                'inscricao_id' => $i->id,
                'voluntario'   => [
                    'id'   => $i->voluntario->id,
                    'nome' => $i->voluntario->user?->name,
                ],
                'demanda'      => [
                    'id'     => $i->demanda->id,
                    'titulo' => $i->demanda->titulo,
                ],
                'mensagem'     => $i->mensagem,
                'created_at'   => $i->created_at->format('d/m/Y'),
            ]);

        return response()->json([
            'ong' => [
                'id'              => $ong->id,
                'razao_social'    => $ong->razao_social,
                'cidade'          => $ong->cidade,
                'media_avaliacoes' => $mediaAvaliacoes,
            ],
            'resumo' => [
                'total_demandas'        => $demandas->count(),
                'demandas_por_status'   => $demandasPorStatus,
                'total_inscricoes'      => $inscricoes->count(),
                'inscricoes_por_status' => $inscricoesPorStatus,
                'inscricoes_pendentes'  => $inscricoesPorStatus[StatusInscricao::Pendente->value] ?? 0,
            ],
            'demandas_abertas'       => DemandaResource::collection($demandasAbertas),
            'inscricoes_pendentes'   => $inscricoesPendentes,
        ]);
    }
}
