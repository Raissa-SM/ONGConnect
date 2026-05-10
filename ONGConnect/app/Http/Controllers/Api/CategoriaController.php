<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Categoria\StoreCategoriaRequest;
use App\Http\Requests\Categoria\UpdateCategoriaRequest;
use App\Http\Resources\CategoriaResource;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CategoriaController extends Controller
{
    #[OA\Get(
        path: '/api/categorias',
        summary: 'Lista todas as categorias disponíveis',
        tags: ['Categorias'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false,
                schema: new OA\Schema(type: 'integer', example: 1))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de categorias',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array',
                            items: new OA\Items(properties: [
                                new OA\Property(property: 'id',        type: 'integer', example: 1),
                                new OA\Property(property: 'nome',      type: 'string',  example: 'Educação'),
                                new OA\Property(property: 'slug',      type: 'string',  example: 'educacao'),
                                new OA\Property(property: 'descricao', type: 'string',  example: 'Atividades de ensino'),
                            ])
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $categorias = Categoria::orderBy('nome')->paginate(15);
        return response()->json(CategoriaResource::collection($categorias)->response()->getData(true));
    }

    #[OA\Get(
        path: '/api/categorias/{id}',
        summary: 'Retorna uma categoria pelo ID',
        tags: ['Categorias'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true,
                schema: new OA\Schema(type: 'integer', example: 1))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Dados da categoria'),
            new OA\Response(response: 404, description: 'Categoria não encontrada'),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $categoria = Categoria::findOrFail($id);
        return response()->json(new CategoriaResource($categoria));
    }

    #[OA\Post(
        path: '/api/categorias',
        summary: 'Cria uma nova categoria',
        security: [['sanctum' => []]],
        tags: ['Categorias'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nome'],
                properties: [
                    new OA\Property(property: 'nome',      type: 'string', example: 'Educação'),
                    new OA\Property(property: 'descricao', type: 'string', example: 'Atividades de ensino e letramento'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Categoria criada com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Erros de validação'),
        ]
    )]
    public function store(StoreCategoriaRequest $request): JsonResponse
    {
        $categoria = Categoria::create($request->validated());
        return response()->json(new CategoriaResource($categoria), 201);
    }

    #[OA\Put(
        path: '/api/categorias/{id}',
        summary: 'Atualiza uma categoria existente',
        security: [['sanctum' => []]],
        tags: ['Categorias'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true,
                schema: new OA\Schema(type: 'integer', example: 1))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nome'],
                properties: [
                    new OA\Property(property: 'nome',      type: 'string', example: 'Educação'),
                    new OA\Property(property: 'descricao', type: 'string', example: 'Atividades de ensino'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Categoria atualizada'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Categoria não encontrada'),
            new OA\Response(response: 422, description: 'Erros de validação'),
        ]
    )]
    public function update(UpdateCategoriaRequest $request, int $id): JsonResponse
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->update($request->validated());
        return response()->json(new CategoriaResource($categoria));
    }

    #[OA\Delete(
        path: '/api/categorias/{id}',
        summary: 'Remove uma categoria',
        security: [['sanctum' => []]],
        tags: ['Categorias'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true,
                schema: new OA\Schema(type: 'integer', example: 1))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Categoria removida'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Categoria não encontrada'),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();
        return response()->json(['message' => 'Categoria removida com sucesso.']);
    }
}
