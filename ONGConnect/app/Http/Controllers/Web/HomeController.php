<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Demanda;
use App\Models\ONG;
use App\Models\Voluntario;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $stats = [
            'voluntarios'     => Voluntario::count(),
            'ongs'            => ONG::count(),
            'demandas_abertas'=> Demanda::aberta()->count(),
        ];

        $demandasDestaque = Demanda::with(['ong', 'categorias'])
            ->aberta()
            ->latest()
            ->take(6)
            ->get();

        return view('home.index', compact('stats', 'demandasDestaque'));
    }
}
