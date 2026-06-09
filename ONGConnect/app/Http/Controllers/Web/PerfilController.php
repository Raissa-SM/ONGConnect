<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerfilController extends Controller
{
    public function voluntario(Request $request): View
    {
        $voluntario = $request->user()->voluntario->load('categorias');
        $categorias = Categoria::orderBy('nome')->get();

        return view('perfil.voluntario', compact('voluntario', 'categorias'));
    }

    public function atualizarVoluntario(Request $request): RedirectResponse
    {
        $voluntario = $request->user()->voluntario;

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'telefone'     => 'nullable|string|max:20',
            'cpf'          => 'nullable|string|max:20',
            'cidade'       => 'nullable|string|max:100',
            'uf'           => 'nullable|string|max:2',
            'descricao'    => 'nullable|string|max:1000',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'categorias'   => 'nullable|array',
            'categorias.*' => 'exists:categorias,id',
        ]);

        $strip = fn(?string $v) => $v !== null ? preg_replace('/\D/', '', $v) : null;

        $request->user()->update(['name' => $validated['name']]);

        $voluntario->update([
            'telefone'  => $strip($validated['telefone'] ?? null),
            'cpf'       => $strip($validated['cpf'] ?? null),
            'cidade'    => $validated['cidade'] ?? null,
            'uf'        => $validated['uf'] ?? null,
            'descricao' => $validated['descricao'] ?? null,
            'latitude'  => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        $voluntario->categorias()->sync($validated['categorias'] ?? []);

        return back()->with('success', 'Perfil atualizado!');
    }

    public function ong(Request $request): View
    {
        $ong = $request->user()->ong;
        return view('perfil.ong', compact('ong'));
    }

    public function atualizarOng(Request $request): RedirectResponse
    {
        $ong = $request->user()->ong;

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'razao_social'     => 'required|string|max:255',
            'cnpj'             => 'nullable|string|max:20',
            'telefone'         => 'nullable|string|max:20',
            'cidade'           => 'nullable|string|max:100',
            'uf'               => 'nullable|string|max:2',
            'endereco'         => 'nullable|string|max:255',
            'descricao_missao' => 'nullable|string|max:2000',
            'website'          => 'nullable|url|max:255',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
        ]);

        $strip = fn(?string $v) => $v !== null ? preg_replace('/\D/', '', $v) : null;

        $request->user()->update(['name' => $validated['name']]);

        $ong->update([
            'razao_social'     => $validated['razao_social'],
            'cnpj'             => $strip($validated['cnpj'] ?? null),
            'telefone'         => $strip($validated['telefone'] ?? null),
            'cidade'           => $validated['cidade'] ?? null,
            'uf'               => $validated['uf'] ?? null,
            'endereco'         => $validated['endereco'] ?? null,
            'descricao_missao' => $validated['descricao_missao'] ?? null,
            'website'          => $validated['website'] ?? null,
            'latitude'         => $validated['latitude'] ?? null,
            'longitude'        => $validated['longitude'] ?? null,
        ]);

        return back()->with('success', 'Perfil da ONG atualizado!');
    }
}
