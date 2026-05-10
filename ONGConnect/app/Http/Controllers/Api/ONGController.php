<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ONG\UpdateONGRequest;
use App\Http\Resources\ONGResource;
use App\Models\ONG;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ONGController extends Controller
{
    #[OA\Get(
        path: '/api/ongs',
        summary: 'Lista todas as ONGs cadastradas',
        tags: ['ONGs'],
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', required: false,
                description: 'Busca por nome ou cidade',
                schema: new OA\Schema(type: 'string', example: 'Rio do Sul')),
            new OA\Parameter(name: 'page', in: 'query', required: false,
                schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de ONGs paginada'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = ONG::with('user')
            ->when($request->q, function ($q, $termo) {
                $q->where(function ($query) use ($termo) {
                    $query->where('razao_social', 'like', "%{$termo}%")
                          ->orWhere('cidade', 'like', "%{$termo}%")
                          ->orWhere('descricao_missao', 'like', "%{$termo}%");
                });
            });

        $ongs = $query->orderBy('razao_social')->paginate(10);
        return response()->json(ONGResource::collection($ongs)->response()->getData(true));
    }

    #[OA\Get(
        path: '/api/ongs/{id}',
        summary: 'Retorna o perfil público de uma ONG',
        tags: ['ONGs'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true,
                schema: new OA\Schema(type: 'integer', example: 1))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Dados da ONG'),
            new OA\Response(response: 404, description: 'ONG não encontrada'),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $ong = ONG::with(['user', 'demandas'])->findOrFail($id);
        return response()->json(new ONGResource($ong));
    }

    #[OA\Put(
        path: '/api/ongs/{id}',
        summary: 'Atualiza o perfil da ONG autenticada',
        security: [['sanctum' => []]],
        tags: ['ONGs'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true,
                schema: new OA\Schema(type: 'integer', example: 1))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['razao_social'],
                properties: [
                    new OA\Property(property: 'nome',             type: 'string',  example: 'Usuário Responsável'),
                    new OA\Property(property: 'razao_social',     type: 'string',  example: 'ONG Mãos Solidárias'),
                    new OA\Property(property: 'cnpj',             type: 'string',  example: '12345678000195'),
                    new OA\Property(property: 'telefone',         type: 'string',  example: '(47) 99999-9999'),
                    new OA\Property(property: 'descricao_missao', type: 'string',  example: 'Apoio a famílias carentes.'),
                    new OA\Property(property: 'endereco',         type: 'string',  example: 'Rua das Flores, 100'),
                    new OA\Property(property: 'cidade',           type: 'string',  example: 'Rio do Sul'),
                    new OA\Property(property: 'uf',               type: 'string',  example: 'SC'),
                    new OA\Property(property: 'latitude',         type: 'number',  example: -27.2138),
                    new OA\Property(property: 'longitude',        type: 'number',  example: -49.6438),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Perfil atualizado'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão para editar esta ONG'),
            new OA\Response(response: 404, description: 'ONG não encontrada'),
            new OA\Response(response: 422, description: 'Erros de validação'),
        ]
    )]
    public function update(UpdateONGRequest $request, int $id): JsonResponse
    {
        $ong = ONG::with('user')->findOrFail($id);

        // Verifica se o usuário autenticado é o dono desta ONG
        $this->authorize('update', $ong);

        $dados = $request->validated();

        // Atualiza o nome do usuário se informado
        if (isset($dados['nome'])) {
            $ong->user->update(['name' => $dados['nome']]);
        }

        $ong->update(collect($dados)->except('nome')->all());

        return response()->json([
            'message' => 'Perfil da ONG atualizado com sucesso.',
            'ong'     => new ONGResource($ong->fresh(['user'])),
        ]);
    }
}
