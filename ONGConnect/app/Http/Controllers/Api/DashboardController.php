<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class DashboardController extends Controller
{
    public function voluntario(Request $request): JsonResponse { return response()->json(['message' => 'Dashboard voluntário — Etapa 5']); }
    public function ong(Request $request): JsonResponse { return response()->json(['message' => 'Dashboard ONG — Etapa 5']); }
}
