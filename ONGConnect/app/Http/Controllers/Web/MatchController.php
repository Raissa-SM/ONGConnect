<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Demanda;
use App\Models\Voluntario;
use App\Support\Geo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    private const PESO_CATEGORIA   = 0.6;
    private const PESO_PROXIMIDADE = 0.4;
    private const RAIO_PADRAO_KM   = 50.0;

    public function sugestoes(Request $request): View
    {
        $voluntario = $request->user()->voluntario->load('categorias');
        $apto       = $voluntario->aptoParaMatch();
        $sugestoes  = collect();

        if ($apto) {
            $raioKm      = max(1.0, min(500.0, (float) $request->input('raio_km', self::RAIO_PADRAO_KM)));
            $jaInscritas = $voluntario->inscricoes()->pluck('demanda_id')->toArray();

            $demandas = Demanda::with(['ong', 'categorias'])
                ->aberta()
                ->whereNotIn('id', $jaInscritas)
                ->get()
                ->filter(fn ($d) => $d->vagasDisponiveis() > 0);

            $sugestoes = $demandas
                ->map(fn ($demanda) => [
                    'demanda' => $demanda,
                    'score'   => $this->calcularScore($voluntario, $demanda),
                ])
                ->filter(function ($item) use ($voluntario, $raioKm) {
                    $d = $item['demanda'];
                    if ($d->latitude && $d->longitude) {
                        return Geo::dentroDe(
                            $voluntario->latitude, $voluntario->longitude,
                            $d->latitude, $d->longitude,
                            $raioKm
                        );
                    }
                    return true;
                })
                ->filter(fn ($item) => $item['score']['total'] > 0)
                ->sortByDesc(fn ($item) => $item['score']['total'])
                ->values();
        }

        return view('match.sugestoes', compact('voluntario', 'apto', 'sugestoes'));
    }

    private function calcularScore(Voluntario $voluntario, Demanda $demanda): array
    {
        $catsDemanda    = $demanda->categorias->pluck('id')->toArray();
        $catsVoluntario = $voluntario->categorias->pluck('id')->toArray();

        if (empty($catsDemanda)) {
            $scoreCategoria = 0.5;
        } elseif (empty($catsVoluntario)) {
            $scoreCategoria = 0.0;
        } else {
            $intersecao     = count(array_intersect($catsDemanda, $catsVoluntario));
            $scoreCategoria = $intersecao / count($catsDemanda);
        }

        $distanciaKm = null;
        if ($voluntario->possuiLocalizacao() && $demanda->latitude && $demanda->longitude) {
            $distanciaKm      = Geo::distanciaKm(
                $voluntario->latitude, $voluntario->longitude,
                $demanda->latitude, $demanda->longitude
            );
            $scoreProximidade = Geo::fatorProximidade(
                $voluntario->latitude, $voluntario->longitude,
                $demanda->latitude, $demanda->longitude
            );
        } else {
            $scoreProximidade = 0.5;
        }

        $scoreTotal = (self::PESO_CATEGORIA * $scoreCategoria)
                    + (self::PESO_PROXIMIDADE * $scoreProximidade);

        return [
            'total'        => round($scoreTotal, 4),
            'categoria'    => round($scoreCategoria, 4),
            'proximidade'  => round($scoreProximidade, 4),
            'distancia_km' => $distanciaKm !== null ? round($distanciaKm, 1) : null,
        ];
    }
}
