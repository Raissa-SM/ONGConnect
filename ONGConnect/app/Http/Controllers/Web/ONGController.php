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
        $ong = ONG::with([
                'demandas' => fn ($q) => $q->aberta()->with('categorias')->latest(),
            ])
            ->findOrFail($id);

        return view('ongs.show', compact('ong'));
    }
}
