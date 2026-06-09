<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusDemanda;
use App\Http\Controllers\Controller;
use App\Http\Requests\Demanda\StoreDemandaRequest;
use App\Http\Requests\Demanda\UpdateDemandaRequest;
use App\Http\Resources\DemandaResource;
use App\Models\Demanda;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DemandaController extends Controller
{
    #[OA\Get(
        path: '/api/demandas',
        summary: 'Lista demandas abertas com filtros opcionais',
        tags: ['Demandas'],
        parameters: [
            new OA\Parameter(name: 'q',            in: 'query', required: false, description: 'Busca por título ou descrição', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'cidade',        in: 'query', required: false, schema: new OA\Schema(type: 'string', example: 'Rio do Sul')),
            new OA\Parameter(name: 'uf',            in: 'query', required: false, schema: new OA\Schema(type: 'string', example: 'SC')),
            new OA\Parameter(name: 'tipo',          in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['presencial', 'doacao', 'habilidade'])),
            new OA\Parameter(name: 'categoria_id',  in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'page',          in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de demandas abertas'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Demanda::with(['ong', 'categorias'])
            ->aberta()
            ->when($request->q,            fn ($q, $v) => $q->busca($v))
            ->when($request->cidade,       fn ($q, $v) => $q->where('cidade', $v))
            ->when($request->uf,           fn ($q, $v) => $q->where('uf', $v))
            ->when($request->tipo,         fn ($q, $v) => $q->where('tipo', $v))
            ->when($request->categoria_id, fn ($q, $v) => $q->whereHas(
                'categorias', fn ($cq) => $cq->where('categorias.id', $v)
            ));

        $demandas = $query->orderByDesc('created_at')->paginate(10);

        return response()->json(DemandaResource::collection($demandas)->response()->getData(true));
    }

    #[OA\Get(
        path: '/api/demandas/{id}',
        summary: 'Retorna os detalhes de uma demanda',
        tags: ['Demandas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Dados da demanda'),
            new OA\Response(response: 404, description: 'Demanda não encontrada'),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $demanda = Demanda::with(['ong', 'categorias', 'inscricoes'])->findOrFail($id);

        return response()->json(new DemandaResource($demanda));
    }

    #[OA\Post(
        path: '/api/demandas',
        summary: 'Cria uma nova demanda em rascunho (apenas ONGs)',
        security: [['sanctum' => []]],
        tags: ['Demandas'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['titulo', 'descricao', 'tipo'],
                properties: [
                    new OA\Property(property: 'titulo',      type: 'string',  example: 'Professores para reforço escolar'),
                    new OA\Property(property: 'descricao',   type: 'string',  example: 'Buscamos voluntários para dar aulas de reforço...'),
                    new OA\Property(property: 'tipo',        type: 'string',  enum: ['presencial', 'doacao', 'habilidade'], example: 'presencial'),
                    new OA\Property(property: 'data_inicio', type: 'string',  format: 'date', example: '2026-06-01'),
                    new OA\Property(property: 'data_limite', type: 'string',  format: 'date', example: '2026-08-01'),
                    new OA\Property(property: 'vagas',       type: 'integer', example: 5),
                    new OA\Property(property: 'cidade',      type: 'string',  example: 'Rio do Sul'),
                    new OA\Property(property: 'uf',          type: 'string',  example: 'SC'),
                    new OA\Property(property: 'latitude',    type: 'number',  example: -27.2138),
                    new OA\Property(property: 'longitude',   type: 'number',  example: -49.6438),
                    new OA\Property(property: 'categorias',  type: 'array',   items: new OA\Items(type: 'integer'), example: [1, 3]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Demanda criada (status: rascunho)'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Apenas ONGs podem criar demandas'),
            new OA\Response(response: 422, description: 'Erros de validação'),
        ]
    )]
    public function store(StoreDemandaRequest $request): JsonResponse
    {
        $this->authorize('create', Demanda::class);

        $dados   = $request->validated();
        $demanda = Demanda::create(array_merge(
            collect($dados)->except('categorias')->all(),
            [
                'ong_id' => $request->user()->ong->id,
                'status' => StatusDemanda::Rascunho,
            ]
        ));

        if (!empty($dados['categorias'])) {
            $demanda->categorias()->sync($dados['categorias']);
        }

        return response()->json(new DemandaResource($demanda->load(['ong', 'categorias'])), 201);
    }

    #[OA\Put(
        path: '/api/demandas/{id}',
        summary: 'Atualiza uma demanda (apenas a ONG dona)',
        security: [['sanctum' => []]],
        tags: ['Demandas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['titulo', 'descricao', 'tipo'],
                properties: [
                    new OA\Property(property: 'titulo',      type: 'string'),
                    new OA\Property(property: 'descricao',   type: 'string'),
                    new OA\Property(property: 'tipo',        type: 'string', enum: ['presencial', 'doacao', 'habilidade']),
                    new OA\Property(property: 'data_inicio', type: 'string', format: 'date'),
                    new OA\Property(property: 'data_limite', type: 'string', format: 'date'),
                    new OA\Property(property: 'vagas',       type: 'integer'),
                    new OA\Property(property: 'cidade',      type: 'string'),
                    new OA\Property(property: 'uf',          type: 'string'),
                    new OA\Property(property: 'latitude',    type: 'number'),
                    new OA\Property(property: 'longitude',   type: 'number'),
                    new OA\Property(property: 'categorias',  type: 'array', items: new OA\Items(type: 'integer')),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Demanda atualizada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão'),
            new OA\Response(response: 404, description: 'Demanda não encontrada'),
            new OA\Response(response: 422, description: 'Erros de validação'),
        ]
    )]
    public function update(UpdateDemandaRequest $request, int $id): JsonResponse
    {
        $demanda = Demanda::findOrFail($id);
        $this->authorize('update', $demanda);

        $dados = $request->validated();
        $demanda->update(collect($dados)->except('categorias')->all());

        if (array_key_exists('categorias', $dados)) {
            $demanda->categorias()->sync($dados['categorias'] ?? []);
        }

        return response()->json(new DemandaResource($demanda->load(['ong', 'categorias'])));
    }

    #[OA\Delete(
        path: '/api/demandas/{id}',
        summary: 'Remove uma demanda (apenas ONG dona, sem inscrições ativas)',
        security: [['sanctum' => []]],
        tags: ['Demandas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Demanda removida'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão'),
            new OA\Response(response: 404, description: 'Demanda não encontrada'),
            new OA\Response(response: 422, description: 'Demanda possui inscrições aceitas ou concluídas'),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $demanda = Demanda::findOrFail($id);
        $this->authorize('delete', $demanda);

        $temInscricoesAtivas = $demanda->inscricoes()
            ->whereIn('status', ['aceita', 'concluida'])
            ->exists();

        if ($temInscricoesAtivas) {
            return response()->json(
                ['message' => 'Não é possível remover uma demanda com inscrições aceitas ou concluídas.'],
                422
            );
        }

        $demanda->delete();

        return response()->json(['message' => 'Demanda removida com sucesso.']);
    }

    #[OA\Post(
        path: '/api/demandas/{id}/publicar',
        summary: 'Publica uma demanda (rascunho → aberta)',
        security: [['sanctum' => []]],
        tags: ['Demandas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Demanda publicada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão'),
            new OA\Response(response: 404, description: 'Demanda não encontrada'),
            new OA\Response(response: 422, description: 'Demanda não está em rascunho'),
        ]
    )]
    public function publicar(int $id): JsonResponse
    {
        $demanda = Demanda::with(['ong', 'categorias'])->findOrFail($id);
        $this->authorize('update', $demanda);

        if ($demanda->status !== StatusDemanda::Rascunho) {
            return response()->json(['message' => 'Apenas demandas em rascunho podem ser publicadas.'], 422);
        }

        $demanda->update(['status' => StatusDemanda::Aberta]);

        return response()->json([
            'message' => 'Demanda publicada com sucesso.',
            'demanda' => new DemandaResource($demanda->refresh()),
        ]);
    }

    #[OA\Post(
        path: '/api/demandas/{id}/encerrar',
        summary: 'Encerra uma demanda (aberta → encerrada)',
        security: [['sanctum' => []]],
        tags: ['Demandas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Demanda encerrada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão'),
            new OA\Response(response: 404, description: 'Demanda não encontrada'),
            new OA\Response(response: 422, description: 'Demanda não está aberta'),
        ]
    )]
    public function encerrar(int $id): JsonResponse
    {
        $demanda = Demanda::with(['ong', 'categorias'])->findOrFail($id);
        $this->authorize('update', $demanda);

        if ($demanda->status !== StatusDemanda::Aberta) {
            return response()->json(['message' => 'Apenas demandas abertas podem ser encerradas.'], 422);
        }

        $demanda->update(['status' => StatusDemanda::Encerrada]);

        return response()->json([
            'message' => 'Demanda encerrada com sucesso.',
            'demanda' => new DemandaResource($demanda->refresh()),
        ]);
    }
}
