<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusInscricao;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inscricao\StoreInscricaoRequest;
use App\Http\Resources\InscricaoResource;
use App\Models\Demanda;
use App\Models\Inscricao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class InscricaoController extends Controller
{
    #[OA\Get(
        path: '/api/inscricoes/minhas',
        summary: 'Lista as inscrições do voluntário autenticado',
        security: [['sanctum' => []]],
        tags: ['Inscricoes'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de inscrições'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Apenas voluntários têm inscrições'),
        ]
    )]
    public function minhas(Request $request): JsonResponse
    {
        if (!$request->user()->isVoluntario()) {
            return response()->json(['message' => 'Apenas voluntários têm inscrições.'], 403);
        }

        $inscricoes = Inscricao::with(['demanda.ong'])
            ->where('voluntario_id', $request->user()->voluntario->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json(InscricaoResource::collection($inscricoes)->response()->getData(true));
    }

    #[OA\Get(
        path: '/api/demandas/{id}/inscricoes',
        summary: 'Lista as inscrições de uma demanda (apenas a ONG dona)',
        security: [['sanctum' => []]],
        tags: ['Inscricoes'],
        parameters: [
            new OA\Parameter(name: 'id',   in: 'path',  required: true,  schema: new OA\Schema(type: 'integer', example: 1)),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de inscrições'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão'),
            new OA\Response(response: 404, description: 'Demanda não encontrada'),
        ]
    )]
    public function porDemanda(Request $request, int $id): JsonResponse
    {
        $demanda = Demanda::findOrFail($id);

        if (!$request->user()->isOng() || $request->user()->ong?->id !== $demanda->ong_id) {
            return response()->json(['message' => 'Sem permissão para ver as inscrições desta demanda.'], 403);
        }

        $inscricoes = Inscricao::with(['voluntario.user'])
            ->where('demanda_id', $demanda->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(InscricaoResource::collection($inscricoes)->response()->getData(true));
    }

    #[OA\Post(
        path: '/api/demandas/{id}/inscricoes',
        summary: 'Inscreve o voluntário autenticado em uma demanda',
        security: [['sanctum' => []]],
        tags: ['Inscricoes'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'mensagem', type: 'string', example: 'Tenho disponibilidade nos fins de semana.'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Inscrição realizada com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Apenas voluntários podem se inscrever'),
            new OA\Response(response: 404, description: 'Demanda não encontrada'),
            new OA\Response(response: 422, description: 'Demanda fechada, sem vagas ou já inscrito'),
        ]
    )]
    public function store(StoreInscricaoRequest $request, int $id): JsonResponse
    {
        if (!$request->user()->isVoluntario()) {
            return response()->json(['message' => 'Apenas voluntários podem se inscrever em demandas.'], 403);
        }

        $demanda    = Demanda::findOrFail($id);
        $voluntario = $request->user()->voluntario;

        if (!$demanda->estaAberta()) {
            return response()->json(['message' => 'Esta demanda não está aberta para inscrições.'], 422);
        }

        if ($demanda->vagasDisponiveis() <= 0) {
            return response()->json(['message' => 'Não há vagas disponíveis nesta demanda.'], 422);
        }

        $jaInscrito = Inscricao::where('voluntario_id', $voluntario->id)
            ->where('demanda_id', $demanda->id)
            ->exists();

        if ($jaInscrito) {
            return response()->json(['message' => 'Você já está inscrito nesta demanda.'], 422);
        }

        $inscricao = Inscricao::create([
            'voluntario_id' => $voluntario->id,
            'demanda_id'    => $demanda->id,
            'mensagem'      => $request->validated()['mensagem'] ?? null,
        ]);

        return response()->json(
            new InscricaoResource($inscricao->load(['demanda', 'voluntario.user'])),
            201
        );
    }

    #[OA\Post(
        path: '/api/inscricoes/{id}/aceitar',
        summary: 'Aceita uma inscrição pendente (ONG dona da demanda)',
        security: [['sanctum' => []]],
        tags: ['Inscricoes'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Inscrição aceita'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão'),
            new OA\Response(response: 404, description: 'Inscrição não encontrada'),
            new OA\Response(response: 422, description: 'Inscrição não está pendente'),
        ]
    )]
    public function aceitar(int $id): JsonResponse
    {
        $inscricao = Inscricao::with('demanda')->findOrFail($id);
        $this->authorize('gerenciar', $inscricao);

        if (!$inscricao->status->podeResponderPelaOng()) {
            return response()->json(['message' => 'Apenas inscrições pendentes podem ser aceitas.'], 422);
        }

        $inscricao->update([
            'status'        => StatusInscricao::Aceita,
            'respondida_em' => now(),
        ]);

        return response()->json([
            'message'   => 'Inscrição aceita com sucesso.',
            'inscricao' => new InscricaoResource($inscricao),
        ]);
    }

    #[OA\Post(
        path: '/api/inscricoes/{id}/recusar',
        summary: 'Recusa uma inscrição pendente (ONG dona da demanda)',
        security: [['sanctum' => []]],
        tags: ['Inscricoes'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Inscrição recusada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão'),
            new OA\Response(response: 404, description: 'Inscrição não encontrada'),
            new OA\Response(response: 422, description: 'Inscrição não está pendente'),
        ]
    )]
    public function recusar(int $id): JsonResponse
    {
        $inscricao = Inscricao::with('demanda')->findOrFail($id);
        $this->authorize('gerenciar', $inscricao);

        if (!$inscricao->status->podeResponderPelaOng()) {
            return response()->json(['message' => 'Apenas inscrições pendentes podem ser recusadas.'], 422);
        }

        $inscricao->update([
            'status'        => StatusInscricao::Recusada,
            'respondida_em' => now(),
        ]);

        return response()->json([
            'message'   => 'Inscrição recusada.',
            'inscricao' => new InscricaoResource($inscricao),
        ]);
    }

    #[OA\Post(
        path: '/api/inscricoes/{id}/concluir',
        summary: 'Conclui uma inscrição aceita — libera avaliação (ONG dona)',
        security: [['sanctum' => []]],
        tags: ['Inscricoes'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Inscrição concluída — avaliação liberada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão'),
            new OA\Response(response: 404, description: 'Inscrição não encontrada'),
            new OA\Response(response: 422, description: 'Inscrição não está aceita'),
        ]
    )]
    public function concluir(int $id): JsonResponse
    {
        $inscricao = Inscricao::with('demanda')->findOrFail($id);
        $this->authorize('gerenciar', $inscricao);

        if ($inscricao->status !== StatusInscricao::Aceita) {
            return response()->json(['message' => 'Apenas inscrições aceitas podem ser concluídas.'], 422);
        }

        $inscricao->update([
            'status'       => StatusInscricao::Concluida,
            'concluida_em' => now(),
        ]);

        return response()->json([
            'message'   => 'Inscrição concluída. Avaliação disponível.',
            'inscricao' => new InscricaoResource($inscricao),
        ]);
    }

    #[OA\Post(
        path: '/api/inscricoes/{id}/cancelar',
        summary: 'Cancela a própria inscrição (voluntário — se pendente ou aceita)',
        security: [['sanctum' => []]],
        tags: ['Inscricoes'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Inscrição cancelada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão'),
            new OA\Response(response: 404, description: 'Inscrição não encontrada'),
            new OA\Response(response: 422, description: 'Inscrição não pode ser cancelada neste status'),
        ]
    )]
    public function cancelar(int $id): JsonResponse
    {
        $inscricao = Inscricao::with('demanda')->findOrFail($id);
        $this->authorize('cancelar', $inscricao);

        if (!$inscricao->status->podeCancelarPeloVoluntario()) {
            return response()->json(['message' => 'Esta inscrição não pode mais ser cancelada.'], 422);
        }

        $inscricao->update(['status' => StatusInscricao::Cancelada]);

        return response()->json([
            'message'   => 'Inscrição cancelada com sucesso.',
            'inscricao' => new InscricaoResource($inscricao),
        ]);
    }
}
