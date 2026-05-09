<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class AvaliacaoController extends Controller
{
    public function store(Request $request, int $id): JsonResponse { return response()->json(['message' => "Avaliar inscrição {$id} — Etapa 5"], 201); }
    public function porVoluntario(int $id): JsonResponse { return response()->json(['message' => "Avaliações voluntário {$id} — Etapa 5"]); }
    public function porOng(int $id): JsonResponse { return response()->json(['message' => "Avaliações ONG {$id} — Etapa 5"]); }
}
