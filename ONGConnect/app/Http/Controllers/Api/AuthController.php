<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistroRequest;
use App\Models\ONG;
use App\Models\User;
use App\Models\Voluntario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/auth/registro',
        summary: 'Registra um novo usuário (ONG ou Voluntário)',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation', 'tipo_perfil'],
                properties: [
                    new OA\Property(property: 'name',                  type: 'string',  example: 'João da Silva'),
                    new OA\Property(property: 'email',                 type: 'string',  example: 'joao@email.com'),
                    new OA\Property(property: 'password',              type: 'string',  example: 'senha1234'),
                    new OA\Property(property: 'password_confirmation', type: 'string',  example: 'senha1234'),
                    new OA\Property(property: 'tipo_perfil',           type: 'string',  enum: ['ong', 'voluntario'], example: 'voluntario'),
                    new OA\Property(property: 'razao_social',          type: 'string',  example: 'ONG Mãos Solidárias', description: 'Obrigatório se tipo_perfil = ong'),
                    new OA\Property(property: 'cidade',                type: 'string',  example: 'Rio do Sul'),
                    new OA\Property(property: 'uf',                    type: 'string',  example: 'SC'),
                    new OA\Property(property: 'latitude',              type: 'number',  example: -27.2138),
                    new OA\Property(property: 'longitude',             type: 'number',  example: -49.6438),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Usuário criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Usuário criado com sucesso.'),
                        new OA\Property(property: 'token',   type: 'string', example: '1|abc123...'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Erros de validação'),
        ]
    )]
    public function registro(RegistroRequest $request): JsonResponse
    {
        $dados = $request->validated();

        $user = User::create([
            'name'        => $dados['name'],
            'email'       => $dados['email'],
            'password'    => $dados['password'],
            'tipo_perfil' => $dados['tipo_perfil'],
        ]);

        $camposComuns = [
            'user_id'   => $user->id,
            'telefone'  => $dados['telefone'] ?? null,
            'endereco'  => $dados['endereco'] ?? null,
            'cidade'    => $dados['cidade'] ?? null,
            'uf'        => $dados['uf'] ?? null,
            'latitude'  => $dados['latitude'] ?? null,
            'longitude' => $dados['longitude'] ?? null,
        ];

        if ($user->isOng()) {
            ONG::create(array_merge($camposComuns, [
                'razao_social'     => $dados['razao_social'],
                'cnpj'             => $dados['cnpj'] ?? null,
                'descricao_missao' => $dados['descricao_missao'] ?? null,
            ]));
        } else {
            Voluntario::create(array_merge($camposComuns, [
                'cpf'       => $dados['cpf'] ?? null,
                'descricao' => $dados['descricao'] ?? null,
            ]));
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Usuário criado com sucesso.',
            'token'   => $token,
            'user'    => $user->load($user->isOng() ? 'ong' : 'voluntario'),
        ], 201);
    }

    #[OA\Post(
        path: '/api/auth/login',
        summary: 'Autentica o usuário e retorna um token Bearer',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email',    type: 'string', example: 'joao@email.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'senha1234'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login realizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Login realizado com sucesso.'),
                        new OA\Property(property: 'token',   type: 'string', example: '1|abc123...'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Credenciais inválidas'),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Credenciais inválidas.'], 401);
        }

        /** @var User $user */
        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso.',
            'token'   => $token,
            'user'    => $user->load($user->isOng() ? 'ong' : 'voluntario'),
        ]);
    }

    #[OA\Post(
        path: '/api/auth/logout',
        summary: 'Revoga o token atual do usuário autenticado',
        security: [['sanctum' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Logout realizado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    #[OA\Get(
        path: '/api/auth/eu',
        summary: 'Retorna os dados do usuário autenticado com seu perfil',
        security: [['sanctum' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Dados do usuário autenticado'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function eu(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json(
            $user->load($user->isOng() ? 'ong' : 'voluntario')
        );
    }
}
