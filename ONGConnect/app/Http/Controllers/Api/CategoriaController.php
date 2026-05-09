<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class CategoriaController extends Controller
{
    public function index(): JsonResponse { return response()->json(['message' => 'Categorias — Etapa 2']); }
    public function show(int $id): JsonResponse { return response()->json(['message' => "Categoria {$id} — Etapa 2"]); }
    public function store(Request $request): JsonResponse { return response()->json(['message' => 'Criar categoria — Etapa 2'], 201); }
    public function update(Request $request, int $id): JsonResponse { return response()->json(['message' => "Atualizar categoria {$id} — Etapa 2"]); }
    public function destroy(int $id): JsonResponse { return response()->json(['message' => "Remover categoria {$id} — Etapa 2"]); }
}
