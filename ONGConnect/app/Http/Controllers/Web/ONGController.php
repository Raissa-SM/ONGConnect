<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ONG;
use Illuminate\View\View;

class ONGController extends Controller
{
    public function index(): View
    {
        $ongs = ONG::withCount([
                'demandas as demandas_abertas_count' => fn ($q) => $q->where('status', 'aberta'),
            ])
            ->orderBy('razao_social')
            ->paginate(12);

        return view('ongs.index', compact('ongs'));
    }

    public function show(int $id): View
    {
        $ong = ONG::findOrFail($id);

        $demandasAbertas = $ong->demandas()->aberta()->with('categorias')->latest()->get();
        $demandasEncerradas = $ong->demandas()
            ->where('status', 'encerrada')
            ->with('categorias')
            ->latest()
            ->get();

        $avaliacoes = $ong->avaliacoesRecebidas();
        $media = $avaliacoes->count() >= 3 ? round($avaliacoes->avg('nota'), 1) : null;
        $totalConcluidas = $ong->totalConcluidas();

        return view('ongs.show', compact(
            'ong', 'demandasAbertas', 'demandasEncerradas', 'avaliacoes', 'media', 'totalConcluidas'
        ));
    }
}
