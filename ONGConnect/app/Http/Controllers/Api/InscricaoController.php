<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class InscricaoController extends Controller
{
    public function minhas(Request $request): JsonResponse { return response()->json(['message' => 'Minhas inscrições — Etapa 3']); }
    public function porDemanda(int $id): JsonResponse { return response()->json(['message' => "Inscrições da demanda {$id} — Etapa 3"]); }
    public function store(Request $request, int $id): JsonResponse { return response()->json(['message' => "Inscrever demanda {$id} — Etapa 3"], 201); }
    public function aceitar(int $id): JsonResponse { return response()->json(['message' => "Aceitar inscrição {$id} — Etapa 3"]); }
    public function recusar(int $id): JsonResponse { return response()->json(['message' => "Recusar inscrição {$id} — Etapa 3"]); }
    public function concluir(int $id): JsonResponse { return response()->json(['message' => "Concluir inscrição {$id} — Etapa 3"]); }
    public function cancelar(int $id): JsonResponse { return response()->json(['message' => "Cancelar inscrição {$id} — Etapa 3"]); }
}
