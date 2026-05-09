<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class ONGController extends Controller
{
    public function index(): JsonResponse { return response()->json(['message' => 'ONGs — Etapa 2']); }
    public function show(int $id): JsonResponse { return response()->json(['message' => "ONG {$id} — Etapa 2"]); }
    public function update(Request $request, int $id): JsonResponse { return response()->json(['message' => "Atualizar ONG {$id} — Etapa 2"]); }
}
