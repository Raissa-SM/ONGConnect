<?php

namespace App\Http\Controllers\Web;

use App\Enums\StatusDemanda;
use App\Enums\StatusInscricao;
use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Demanda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DemandaController extends Controller
{
    public function index(Request $request): View
    {
        $mostrarEncerradas = $request->boolean('encerradas');

        if ($mostrarEncerradas) {
            $query = Demanda::with(['ong', 'categorias'])
                ->whereIn('status', [StatusDemanda::Aberta->value, StatusDemanda::Encerrada->value]);
        } else {
            $query = Demanda::with(['ong', 'categorias'])->aberta();
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($sq) => $sq->where('titulo', 'like', "%{$q}%")
                ->orWhere('descricao', 'like', "%{$q}%"));
        }
        if ($request->filled('cidade')) {
            $query->where('cidade', 'like', '%' . $request->cidade . '%');
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('categoria_id')) {
            $query->whereHas('categorias', fn ($q) => $q->where('categorias.id', $request->categoria_id));
        }

        $demandas   = $query->orderByDesc('created_at')->paginate(12)->withQueryString();
        $categorias = Categoria::orderBy('nome')->get();

        return view('demandas.index', compact('demandas', 'categorias', 'mostrarEncerradas'));
    }

    public function show(int $id): View
    {
        $demanda = Demanda::with(['ong', 'categorias'])->findOrFail($id);

        $jaInscrito = false;
        $inscricao  = null;
        if (auth()->check() && auth()->user()->isVoluntario()) {
            $voluntario = auth()->user()->voluntario;
            $inscricao  = $demanda->inscricoes()->where('voluntario_id', $voluntario->id)->first();
            $jaInscrito = $inscricao !== null;
        }

        return view('demandas.show', compact('demanda', 'jaInscrito', 'inscricao'));
    }

    public function minhas(Request $request): View
    {
        $ong = $request->user()->ong;
        $demandas = Demanda::where('ong_id', $ong->id)
            ->withCount('inscricoes')
            ->orderByDesc('created_at')
            ->get();

        return view('demandas.minhas', compact('demandas'));
    }

    public function criar(): View
    {
        $categorias = Categoria::orderBy('nome')->get();
        return view('demandas.criar', compact('categorias'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titulo'       => 'required|string|max:255',
            'descricao'    => 'required|string',
            'tipo'         => 'required|in:presencial,doacao,habilidade',
            'data_inicio'  => 'nullable|date',
            'data_limite'  => 'nullable|date|after_or_equal:data_inicio',
            'vagas'        => 'nullable|integer|min:1|max:9999',
            'cidade'       => 'nullable|string|max:100',
            'uf'           => 'nullable|string|max:2',
            'endereco'     => 'nullable|string|max:255',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'categorias'   => 'nullable|array',
            'categorias.*' => 'exists:categorias,id',
        ]);

        $demanda = Demanda::create(array_merge($validated, [
            'ong_id' => $request->user()->ong->id,
            'status' => StatusDemanda::Rascunho,
        ]));

        if (!empty($validated['categorias'])) {
            $demanda->categorias()->sync($validated['categorias']);
        }

        return redirect()->route('demandas.minhas')
            ->with('success', 'Demanda criada como rascunho.');
    }

    public function editar(int $id, Request $request): View
    {
        $demanda = Demanda::with('categorias')->findOrFail($id);
        abort_if($demanda->ong_id !== $request->user()->ong->id, 403);

        $categorias = Categoria::orderBy('nome')->get();
        return view('demandas.editar', compact('demanda', 'categorias'));
    }

    public function update(int $id, Request $request): RedirectResponse
    {
        $demanda = Demanda::findOrFail($id);
        abort_if($demanda->ong_id !== $request->user()->ong->id, 403);

        $validated = $request->validate([
            'titulo'       => 'required|string|max:255',
            'descricao'    => 'required|string',
            'tipo'         => 'required|in:presencial,doacao,habilidade',
            'data_inicio'  => 'nullable|date',
            'data_limite'  => 'nullable|date|after_or_equal:data_inicio',
            'vagas'        => 'nullable|integer|min:1|max:9999',
            'cidade'       => 'nullable|string|max:100',
            'uf'           => 'nullable|string|max:2',
            'endereco'     => 'nullable|string|max:255',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'categorias'   => 'nullable|array',
            'categorias.*' => 'exists:categorias,id',
        ]);

        $demanda->update($validated);
        $demanda->categorias()->sync($validated['categorias'] ?? []);

        return redirect()->route('demandas.minhas')
            ->with('success', 'Demanda atualizada.');
    }

    public function destroy(int $id, Request $request): RedirectResponse
    {
        $demanda = Demanda::findOrFail($id);
        abort_if($demanda->ong_id !== $request->user()->ong->id, 403);

        if ($demanda->inscricoes()->whereIn('status', ['aceita', 'concluida'])->exists()) {
            return back()->with('error', 'Não é possível excluir uma demanda com inscrições aceitas ou concluídas.');
        }

        $demanda->delete();

        return redirect()->route('demandas.minhas')->with('success', 'Demanda excluída.');
    }

    public function publicar(int $id, Request $request): RedirectResponse
    {
        $demanda = Demanda::findOrFail($id);
        abort_if($demanda->ong_id !== $request->user()->ong->id, 403);

        if ($demanda->status !== StatusDemanda::Rascunho) {
            return back()->with('error', 'Apenas rascunhos podem ser publicados.');
        }

        $demanda->update(['status' => StatusDemanda::Aberta]);

        return back()->with('success', 'Demanda publicada com sucesso!');
    }

    public function concluirTodas(int $id, Request $request): RedirectResponse
    {
        $demanda = Demanda::findOrFail($id);
        abort_if($demanda->ong_id !== $request->user()->ong->id, 403);

        if ($demanda->status !== StatusDemanda::Aberta) {
            return back()->with('error', 'Apenas demandas abertas podem ser concluídas.');
        }

        $concluidas = $demanda->inscricoes()
            ->where('status', StatusInscricao::Aceita)
            ->update(['status' => StatusInscricao::Concluida, 'concluida_em' => now()]);

        $demanda->update(['status' => StatusDemanda::Encerrada]);

        return back()->with('success', "Demanda concluída! {$concluidas} inscrição(ões) marcada(s) como concluída(s).");
    }
}
