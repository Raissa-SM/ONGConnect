<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class DemandaController extends Controller
{
    public function index(Request $request): JsonResponse { return response()->json(['message' => 'Demandas — Etapa 3']); }
    public function show(int $id): JsonResponse { return response()->json(['message' => "Demanda {$id} — Etapa 3"]); }
    public function store(Request $request): JsonResponse { return response()->json(['message' => 'Criar demanda — Etapa 3'], 201); }
    public function update(Request $request, int $id): JsonResponse { return response()->json(['message' => "Atualizar demanda {$id} — Etapa 3"]); }
    public function destroy(int $id): JsonResponse { return response()->json(['message' => "Remover demanda {$id} — Etapa 3"]); }
    public function publicar(int $id): JsonResponse { return response()->json(['message' => "Publicar demanda {$id} — Etapa 3"]); }
    public function encerrar(int $id): JsonResponse { return response()->json(['message' => "Encerrar demanda {$id} — Etapa 3"]); }
}
