<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class VoluntarioController extends Controller
{
    public function show(int $id): JsonResponse { return response()->json(['message' => "Voluntário {$id} — Etapa 2"]); }
    public function update(Request $request, int $id): JsonResponse { return response()->json(['message' => "Atualizar voluntário {$id} — Etapa 2"]); }
    public function syncCategorias(Request $request, int $id): JsonResponse { return response()->json(['message' => "Categorias voluntário {$id} — Etapa 2"]); }
}
