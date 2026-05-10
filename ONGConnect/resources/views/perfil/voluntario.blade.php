@extends('layouts.app')
@section('title', 'Meu Perfil — ONGConnect')
@section('content')

<div class="max-w-3xl mx-auto px-6 py-12">

    <div class="mb-8">
        <a href="{{ route('dashboard.voluntario') }}" class="text-sm text-ink-2 hover:text-primary transition-colors">← Painel</a>
        <h1 class="text-2xl font-bold tracking-tight text-ink mt-3">Meu perfil</h1>
        <p class="text-ink-2 text-sm mt-1">Mantenha seu perfil atualizado para receber melhores sugestões de match.</p>
    </div>

    <form method="POST" action="{{ route('perfil.voluntario.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Dados pessoais --}}
        <div class="bg-surface rounded-2xl border border-border/60 p-6 space-y-5">
            <h2 class="font-semibold text-ink">Dados pessoais</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Nome completo</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">CPF</label>
                    <input type="text" name="cpf" value="{{ old('cpf', $voluntario->cpf) }}"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="000.000.000-00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Telefone</label>
                    <input type="text" name="telefone" value="{{ old('telefone', $voluntario->telefone) }}"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="(47) 99999-9999">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Sobre você</label>
                <textarea name="descricao" rows="3"
                    class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all resize-none"
                    placeholder="Conte um pouco sobre suas habilidades e motivações...">{{ old('descricao', $voluntario->descricao) }}</textarea>
            </div>
        </div>

        {{-- Localização --}}
        <div class="bg-surface rounded-2xl border border-border/60 p-6 space-y-4">
            <div>
                <h2 class="font-semibold text-ink">Localização</h2>
                <p class="text-xs text-ink-2 mt-0.5">Necessário para o algoritmo de match por proximidade.</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Cidade</label>
                    <input type="text" name="cidade" value="{{ old('cidade', $voluntario->cidade) }}"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="Rio do Sul">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">UF</label>
                    <input type="text" name="uf" value="{{ old('uf', $voluntario->uf) }}" maxlength="2"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="SC">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Latitude</label>
                    <input type="number" step="any" name="latitude" value="{{ old('latitude', $voluntario->latitude) }}"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="-27.2138">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Longitude</label>
                    <input type="number" step="any" name="longitude" value="{{ old('longitude', $voluntario->longitude) }}"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="-49.6438">
                </div>
            </div>
        </div>

        {{-- Categorias --}}
        <div class="bg-surface rounded-2xl border border-border/60 p-6">
            <div class="mb-4">
                <h2 class="font-semibold text-ink">Áreas de interesse</h2>
                <p class="text-xs text-ink-2 mt-0.5">Selecione as categorias que combinam com seu perfil de voluntariado.</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                @foreach($categorias as $cat)
                    <label class="cursor-pointer flex items-center gap-2 p-3 rounded-xl border border-border hover:border-primary/40 transition-all
                        {{ in_array($cat->id, $voluntario->categorias->pluck('id')->toArray()) ? 'border-primary bg-primary/5' : '' }}">
                        <input type="checkbox" name="categorias[]" value="{{ $cat->id }}"
                            {{ in_array($cat->id, $voluntario->categorias->pluck('id')->toArray()) ? 'checked' : '' }}
                            class="text-primary rounded focus:ring-primary">
                        <span class="text-sm text-ink">{{ $cat->nome }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="bg-primary hover:bg-primary-dark text-white px-8 py-2.5 rounded-full font-medium text-sm transition-colors">
                Salvar perfil
            </button>
            <a href="{{ route('dashboard.voluntario') }}"
               class="border border-border hover:border-ink-2 text-ink px-8 py-2.5 rounded-full font-medium text-sm transition-colors">
                Cancelar
            </a>
        </div>

    </form>
</div>

@endsection
