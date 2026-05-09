<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class MatchController extends Controller
{
    public function sugestoes(Request $request): JsonResponse { return response()->json(['message' => 'Sugestões de match — Etapa 4']); }
    public function score(Request $request): JsonResponse { return response()->json(['message' => 'Score de match — Etapa 4']); }
}
