<?php

namespace App\Http\Controllers\Web;

use App\Enums\AutorAvaliacao;
use App\Enums\StatusDemanda;
use App\Enums\StatusInscricao;
use App\Http\Controllers\Controller;
use App\Models\Avaliacao;
use App\Models\Demanda;
use App\Models\Inscricao;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function voluntario(Request $request): View
    {
        $voluntario = $request->user()->voluntario->load('categorias');

        $inscricoes = Inscricao::with(['demanda.ong'])
            ->where('voluntario_id', $voluntario->id)
            ->orderByDesc('created_at')
            ->get();

        $porStatus = collect(StatusInscricao::cases())
            ->mapWithKeys(fn ($s) => [
                $s->value => $inscricoes->filter(fn ($i) => $i->status === $s)->count(),
            ]);

        $proximas = $inscricoes
            ->filter(fn ($i) =>
                $i->status === StatusInscricao::Aceita
                && $i->demanda->data_inicio
                && $i->demanda->data_inicio->isFuture()
            )
            ->sortBy(fn ($i) => $i->demanda->data_inicio)
            ->take(3)
            ->values();

        $ultimas = $inscricoes->take(5);

        $avaliacoes = Avaliacao::where('autor_tipo', AutorAvaliacao::ONG->value)
            ->whereHas('inscricao', fn ($q) => $q->where('voluntario_id', $voluntario->id))
            ->orderByDesc('created_at')
            ->get();

        $mediaAvaliacoes = $avaliacoes->count() >= 3 ? round($avaliacoes->avg('nota'), 1) : null;

        return view('dashboard.voluntario', compact(
            'voluntario', 'inscricoes', 'porStatus', 'proximas', 'ultimas', 'mediaAvaliacoes'
        ));
    }

    public function ong(Request $request): View
    {
        $ong = $request->user()->ong;

        $demandas = Demanda::where('ong_id', $ong->id)->get();

        $demandasPorStatus = collect(StatusDemanda::cases())
            ->mapWithKeys(fn ($s) => [
                $s->value => $demandas->filter(fn ($d) => $d->status === $s)->count(),
            ]);

        $inscricoes = Inscricao::whereHas('demanda', fn ($q) => $q->where('ong_id', $ong->id))
            ->with(['demanda', 'voluntario.user'])
            ->orderByDesc('created_at')
            ->get();

        $inscricoesPorStatus = collect(StatusInscricao::cases())
            ->mapWithKeys(fn ($s) => [
                $s->value => $inscricoes->filter(fn ($i) => $i->status === $s)->count(),
            ]);

        $inscricoesPendentes = $inscricoes
            ->filter(fn ($i) => $i->status === StatusInscricao::Pendente)
            ->take(5)
            ->values();

        $demandasAbertas = Demanda::aberta()
            ->where('ong_id', $ong->id)
            ->with('categorias')
            ->get()
            ->filter(fn ($d) => $d->vagasDisponiveis() > 0)
            ->take(5);

        $avaliacoes = Avaliacao::where('autor_tipo', AutorAvaliacao::Voluntario->value)
            ->whereHas('inscricao.demanda', fn ($q) => $q->where('ong_id', $ong->id))
            ->get();

        $mediaAvaliacoes = $avaliacoes->count() >= 3 ? round($avaliacoes->avg('nota'), 1) : null;

        return view('dashboard.ong', compact(
            'ong', 'demandas', 'demandasPorStatus', 'inscricoes', 'inscricoesPorStatus',
            'inscricoesPendentes', 'demandasAbertas', 'mediaAvaliacoes'
        ));
    }
}
