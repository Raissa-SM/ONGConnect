<?php

namespace App\Http\Controllers\Web;

use App\Enums\StatusInscricao;
use App\Http\Controllers\Controller;
use App\Models\Demanda;
use App\Models\Inscricao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InscricaoController extends Controller
{
    public function minhas(Request $request): View
    {
        $voluntario = $request->user()->voluntario;

        $inscricoes = Inscricao::with(['demanda.ong'])
            ->where('voluntario_id', $voluntario->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('inscricoes.minhas', compact('inscricoes'));
    }

    public function store(int $id, Request $request): RedirectResponse
    {
        $demanda    = Demanda::findOrFail($id);
        $voluntario = $request->user()->voluntario;

        if (!$demanda->estaAberta()) {
            return back()->with('error', 'Esta demanda não está mais aceitando inscrições.');
        }

        if ($demanda->vagas && $demanda->vagasDisponiveis() <= 0) {
            return back()->with('error', 'Não há vagas disponíveis nesta demanda.');
        }

        if ($demanda->inscricoes()->where('voluntario_id', $voluntario->id)->exists()) {
            return back()->with('error', 'Você já está inscrito nesta demanda.');
        }

        $validated = $request->validate(['mensagem' => 'nullable|string|max:1000']);

        Inscricao::create([
            'voluntario_id' => $voluntario->id,
            'demanda_id'    => $demanda->id,
            'status'        => StatusInscricao::Pendente,
            'mensagem'      => $validated['mensagem'] ?? null,
        ]);

        return redirect()->route('inscricoes.minhas')
            ->with('success', 'Inscrição realizada com sucesso!');
    }

    public function cancelar(int $id, Request $request): RedirectResponse
    {
        $inscricao  = Inscricao::findOrFail($id);
        $voluntario = $request->user()->voluntario;

        abort_if($inscricao->voluntario_id !== $voluntario->id, 403);

        if (!$inscricao->status->podeCancelarPeloVoluntario()) {
            return back()->with('error', 'Esta inscrição não pode ser cancelada.');
        }

        $inscricao->update(['status' => StatusInscricao::Cancelada]);

        return back()->with('success', 'Inscrição cancelada.');
    }

    public function porDemanda(int $id, Request $request): View
    {
        $demanda = Demanda::with('ong')->findOrFail($id);
        abort_if($demanda->ong_id !== $request->user()->ong->id, 403);

        $inscricoes = Inscricao::with(['voluntario.user'])
            ->where('demanda_id', $demanda->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('inscricoes.demanda', compact('demanda', 'inscricoes'));
    }

    public function aceitar(int $id, Request $request): RedirectResponse
    {
        $inscricao = Inscricao::with('demanda')->findOrFail($id);
        abort_if($inscricao->demanda->ong_id !== $request->user()->ong->id, 403);

        if (!$inscricao->status->podeResponderPelaOng()) {
            return back()->with('error', 'Esta inscrição não pode ser aceita.');
        }

        $inscricao->update(['status' => StatusInscricao::Aceita, 'respondida_em' => now()]);

        return back()->with('success', 'Inscrição aceita!');
    }

    public function recusar(int $id, Request $request): RedirectResponse
    {
        $inscricao = Inscricao::with('demanda')->findOrFail($id);
        abort_if($inscricao->demanda->ong_id !== $request->user()->ong->id, 403);

        if (!$inscricao->status->podeResponderPelaOng()) {
            return back()->with('error', 'Esta inscrição não pode ser recusada.');
        }

        $inscricao->update(['status' => StatusInscricao::Recusada, 'respondida_em' => now()]);

        return back()->with('success', 'Inscrição recusada.');
    }

    public function concluir(int $id, Request $request): RedirectResponse
    {
        $inscricao = Inscricao::with('demanda')->findOrFail($id);
        abort_if($inscricao->demanda->ong_id !== $request->user()->ong->id, 403);

        if ($inscricao->status !== StatusInscricao::Aceita) {
            return back()->with('error', 'Apenas inscrições aceitas podem ser concluídas.');
        }

        $inscricao->update(['status' => StatusInscricao::Concluida, 'concluida_em' => now()]);

        return back()->with('success', 'Inscrição concluída!');
    }
}
