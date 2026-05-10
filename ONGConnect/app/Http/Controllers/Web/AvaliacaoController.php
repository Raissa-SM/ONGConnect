<?php

namespace App\Http\Controllers\Web;

use App\Enums\AutorAvaliacao;
use App\Http\Controllers\Controller;
use App\Models\Avaliacao;
use App\Models\Inscricao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AvaliacaoController extends Controller
{
    public function store(int $id, Request $request): RedirectResponse
    {
        $inscricao = Inscricao::with(['demanda', 'voluntario'])->findOrFail($id);

        if (!$inscricao->podeAvaliar()) {
            return back()->with('error', 'Avaliação disponível apenas para inscrições concluídas.');
        }

        $user  = $request->user();
        $isOng = $user->isOng();

        if ($isOng && $user->ong?->id !== $inscricao->demanda->ong_id) {
            abort(403);
        }
        if (!$isOng && $user->voluntario?->id !== $inscricao->voluntario_id) {
            abort(403);
        }

        $autorTipo = $isOng ? AutorAvaliacao::ONG : AutorAvaliacao::Voluntario;

        if ($inscricao->avaliacoes()->where('autor_tipo', $autorTipo->value)->exists()) {
            return back()->with('error', 'Você já avaliou esta inscrição.');
        }

        $validated = $request->validate([
            'nota'       => 'required|integer|between:1,5',
            'comentario' => 'nullable|string|max:1000',
        ]);

        Avaliacao::create([
            'inscricao_id' => $inscricao->id,
            'autor_tipo'   => $autorTipo,
            'nota'         => $validated['nota'],
            'comentario'   => $validated['comentario'] ?? null,
        ]);

        return back()->with('success', 'Avaliação registrada!');
    }
}
