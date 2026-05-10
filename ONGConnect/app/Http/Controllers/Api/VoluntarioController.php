<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Voluntario\SyncCategoriasRequest;
use App\Http\Requests\Voluntario\UpdateVoluntarioRequest;
use App\Http\Resources\VoluntarioResource;
use App\Models\Voluntario;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class VoluntarioController extends Controller
{
    #[OA\Get(
        path: '/api/voluntarios/{id}',
        summary: 'Retorna o perfil público de um voluntário',
        tags: ['Voluntarios'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true,
                schema: new OA\Schema(type: 'integer', example: 1))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Dados públicos do voluntário'),
            new OA\Response(response: 404, description: 'Voluntário não encontrado'),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $voluntario = Voluntario::with(['user', 'categorias'])->findOrFail($id);
        return response()->json(new VoluntarioResource($voluntario));
    }

    #[OA\Put(
        path: '/api/voluntarios/{id}',
        summary: 'Atualiza o perfil do voluntário autenticado',
        security: [['sanctum' => []]],
        tags: ['Voluntarios'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true,
                schema: new OA\Schema(type: 'integer', example: 1))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nome',             type: 'string',  example: 'João da Silva'),
                    new OA\Property(property: 'cpf',              type: 'string',  example: '12345678901'),
                    new OA\Property(property: 'telefone',         type: 'string',  example: '(47) 99999-9999'),
                    new OA\Property(property: 'descricao',        type: 'string',  example: 'Desenvolvedor web com experiência em Laravel.'),
                    new OA\Property(property: 'habilidades',      type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['desenvolvimento web', 'design gráfico']),
                    new OA\Property(property: 'disponibilidade',  type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['sabado_manha', 'domingo_tarde']),
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
            new OA\Response(response: 403, description: 'Sem permissão para editar este perfil'),
            new OA\Response(response: 404, description: 'Voluntário não encontrado'),
            new OA\Response(response: 422, description: 'Erros de validação'),
        ]
    )]
    public function update(UpdateVoluntarioRequest $request, int $id): JsonResponse
    {
        $voluntario = Voluntario::with(['user', 'categorias'])->findOrFail($id);

        $this->authorize('update', $voluntario);

        $dados = $request->validated();

        if (isset($dados['nome'])) {
            $voluntario->user->update(['name' => $dados['nome']]);
        }

        $voluntario->update(collect($dados)->except('nome')->all());

        return response()->json([
            'message'    => 'Perfil atualizado com sucesso.',
            'voluntario' => new VoluntarioResource($voluntario->fresh(['user', 'categorias'])),
        ]);
    }

    #[OA\Post(
        path: '/api/voluntarios/{id}/categorias',
        summary: 'Atualiza as categorias de interesse do voluntário (substitui todas)',
        security: [['sanctum' => []]],
        tags: ['Voluntarios'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true,
                schema: new OA\Schema(type: 'integer', example: 1))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['categorias'],
                properties: [
                    new OA\Property(property: 'categorias', type: 'array',
                        items: new OA\Items(type: 'integer'),
                        example: [1, 3, 5],
                        description: 'Array de IDs das categorias de interesse')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Categorias atualizadas'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 403, description: 'Sem permissão'),
            new OA\Response(response: 422, description: 'IDs inválidos'),
        ]
    )]
    public function syncCategorias(SyncCategoriasRequest $request, int $id): JsonResponse
    {
        $voluntario = Voluntario::with(['user', 'categorias'])->findOrFail($id);

        $this->authorize('update', $voluntario);

        $voluntario->categorias()->sync($request->validated()['categorias']);

        return response()->json([
            'message'    => 'Categorias de interesse atualizadas.',
            'categorias' => $voluntario->fresh('categorias')->categorias->map(fn($c) => [
                'id'   => $c->id,
                'nome' => $c->nome,
            ]),
        ]);
    }
}
