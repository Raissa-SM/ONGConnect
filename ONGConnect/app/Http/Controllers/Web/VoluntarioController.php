<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Voluntario;
use Illuminate\View\View;

class VoluntarioController extends Controller
{
    public function show(int $id): View
    {
        $voluntario = Voluntario::with(['user', 'categorias'])->findOrFail($id);

        $avaliacoes = $voluntario->avaliacoesRecebidas();
        $media = $avaliacoes->count() >= 3 ? round($avaliacoes->avg('nota'), 1) : null;
        $totalConcluidas = $voluntario->totalConcluidas();

        return view('voluntarios.show', compact('voluntario', 'avaliacoes', 'media', 'totalConcluidas'));
    }
}
