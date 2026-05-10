<?php

namespace App\Http\Controllers\Api;

use App\Enums\AutorAvaliacao;
use App\Http\Controllers\Controller;
use App\Http\Requests\Avaliacao\StoreAvaliacaoRequest;
use App\Http\Resources\AvaliacaoResource;
use App\Models\Avaliacao;
use App\Models\Inscricao;
use App\Models\ONG;
use App\Models\Voluntario;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class AvaliacaoController extends Controller
{
    #[OA\Post(
        path: '/api/inscricoes/{id}/avaliacoes',
        summary: 'Registra uma avaliação mútua após a conclusão da inscrição',
        description: 'ONG avalia o voluntário (nota 1–5); voluntário avalia a ONG. Cada lado pode avaliar uma única vez por inscrição.',
        security: [['sanctum' => []]],
        tags: ['Avaliacoes'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true,
                description: 'ID da inscrição concluída',
                schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nota'],
                properties: [
                    new OA\Property(property: 'nota',       type: 'integer', minimum: 1, maximum: 5, example: 5),
                    new OA\Property(property: 'comentario', type: 'string',  example: 'Excelente voluntário, pontual e dedicado.'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Avaliação registrada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Inscrição não concluída ou sem permissão'),
            new OA\Response(response: 404, description: 'Inscrição não encontrada'),
            new OA\Response(response: 422, description: 'Avaliação já registrada ou dados inválidos'),
        ]
    )]
    public function store(StoreAvaliacaoRequest $request, int $id): JsonResponse
    {
        $inscricao = Inscricao::with(['demanda', 'voluntario'])->findOrFail($id);

        // Garante que a inscrição está concluída e o usuário tem permissão
        $this->authorize('create', [Avaliacao::class, $inscricao]);

        $autorTipo = $request->user()->isOng()
            ? AutorAvaliacao::ONG
            : AutorAvaliacao::Voluntario;

        // Verifica se este lado já avaliou (double-check além da constraint do BD)
        $jaAvaliou = $inscricao->avaliacoes()
            ->where('autor_tipo', $autorTipo->value)
            ->exists();

        if ($jaAvaliou) {
            return response()->json(['message' => 'Você já avaliou esta inscrição.'], 422);
        }

        $avaliacao = Avaliacao::create([
            'inscricao_id' => $inscricao->id,
            'autor_tipo'   => $autorTipo,
            'nota'         => $request->validated()['nota'],
            'comentario'   => $request->validated()['comentario'] ?? null,
        ]);

        return response()->json(
            new AvaliacaoResource($avaliacao->load(['inscricao.demanda.ong', 'inscricao.voluntario.user'])),
            201
        );
    }

    #[OA\Get(
        path: '/api/voluntarios/{id}/avaliacoes',
        summary: 'Lista as avaliações recebidas por um voluntário (feitas por ONGs)',
        tags: ['Avaliacoes'],
        parameters: [
            new OA\Parameter(name: 'id',   in: 'path',  required: true,  schema: new OA\Schema(type: 'integer', example: 1)),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de avaliações'),
            new OA\Response(response: 404, description: 'Voluntário não encontrado'),
        ]
    )]
    public function porVoluntario(int $id): JsonResponse
    {
        $voluntario = Voluntario::findOrFail($id);

        $avaliacoes = Avaliacao::where('autor_tipo', AutorAvaliacao::ONG->value)
            ->whereHas('inscricao', fn ($q) => $q->where('voluntario_id', $voluntario->id))
            ->with(['inscricao.demanda.ong', 'inscricao.voluntario.user'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json(AvaliacaoResource::collection($avaliacoes)->response()->getData(true));
    }

    #[OA\Get(
        path: '/api/ongs/{id}/avaliacoes',
        summary: 'Lista as avaliações recebidas por uma ONG (feitas por voluntários)',
        tags: ['Avaliacoes'],
        parameters: [
            new OA\Parameter(name: 'id',   in: 'path',  required: true,  schema: new OA\Schema(type: 'integer', example: 1)),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de avaliações'),
            new OA\Response(response: 404, description: 'ONG não encontrada'),
        ]
    )]
    public function porOng(int $id): JsonResponse
    {
        $ong = ONG::findOrFail($id);

        $avaliacoes = Avaliacao::where('autor_tipo', AutorAvaliacao::Voluntario->value)
            ->whereHas('inscricao.demanda', fn ($q) => $q->where('ong_id', $ong->id))
            ->with(['inscricao.demanda.ong', 'inscricao.voluntario.user'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json(AvaliacaoResource::collection($avaliacoes)->response()->getData(true));
    }
}
