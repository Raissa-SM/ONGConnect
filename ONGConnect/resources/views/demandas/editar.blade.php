@extends('layouts.app')
@section('title', 'Editar Demanda — ONGConnect')
@section('content')

<div class="max-w-3xl mx-auto px-6 py-12">

    <div class="mb-8">
        <a href="{{ route('demandas.minhas') }}" class="text-sm text-ink-2 hover:text-primary transition-colors">← Minhas demandas</a>
        <h1 class="text-2xl font-bold tracking-tight text-ink mt-3">Editar demanda</h1>
    </div>

    <form method="POST" action="{{ route('demandas.update', $demanda->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-surface rounded-2xl border border-border/60 p-6 space-y-5">
            <h2 class="font-semibold text-ink">Informações básicas</h2>

            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Título <span class="text-danger">*</span></label>
                <input type="text" name="titulo" value="{{ old('titulo', $demanda->titulo) }}" required
                    class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
            </div>

            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Descrição <span class="text-danger">*</span></label>
                <textarea name="descricao" rows="5" required
                    class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all resize-none">{{ old('descricao', $demanda->descricao) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Tipo <span class="text-danger">*</span></label>
                <select name="tipo" required
                    class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all bg-white">
                    <option value="presencial" {{ old('tipo', $demanda->tipo->value) === 'presencial' ? 'selected' : '' }}>Voluntariado Presencial</option>
                    <option value="doacao"     {{ old('tipo', $demanda->tipo->value) === 'doacao'     ? 'selected' : '' }}>Doação Material</option>
                    <option value="habilidade" {{ old('tipo', $demanda->tipo->value) === 'habilidade' ? 'selected' : '' }}>Habilidade Específica</option>
                </select>
            </div>
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 p-6 space-y-5">
            <h2 class="font-semibold text-ink">Datas e vagas</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Data de início</label>
                    <input type="date" name="data_inicio" value="{{ old('data_inicio', $demanda->data_inicio?->format('Y-m-d')) }}"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Prazo de inscrição</label>
                    <input type="date" name="data_limite" value="{{ old('data_limite', $demanda->data_limite?->format('Y-m-d')) }}"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Nº de vagas</label>
                    <input type="number" name="vagas" value="{{ old('vagas', $demanda->vagas) }}" min="1"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="Ilimitado">
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 p-6 space-y-4">
            <h2 class="font-semibold text-ink">Localização</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-ink mb-1.5">Endereço</label>
                    <input type="text" name="endereco" value="{{ old('endereco', $demanda->endereco) }}"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">UF</label>
                    <input type="text" name="uf" value="{{ old('uf', $demanda->uf) }}" maxlength="2"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-ink mb-1.5">Cidade</label>
                    <input type="text" name="cidade" value="{{ old('cidade', $demanda->cidade) }}"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 p-6">
            <h2 class="font-semibold text-ink mb-4">Categorias</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                @php $selectedCats = old('categorias', $demanda->categorias->pluck('id')->toArray()); @endphp
                @foreach($categorias as $cat)
                    <label class="cursor-pointer flex items-center gap-2 p-3 rounded-xl border transition-all
                        {{ in_array($cat->id, $selectedCats) ? 'border-primary bg-primary/5' : 'border-border hover:border-primary/40' }}">
                        <input type="checkbox" name="categorias[]" value="{{ $cat->id }}"
                            {{ in_array($cat->id, $selectedCats) ? 'checked' : '' }}
                            class="text-primary rounded focus:ring-primary">
                        <span class="text-sm text-ink">{{ $cat->nome }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="bg-primary hover:bg-primary-dark text-white px-8 py-2.5 rounded-full font-medium text-sm transition-colors">
                Salvar alterações
            </button>
            <a href="{{ route('demandas.minhas') }}"
               class="border border-border hover:border-ink-2 text-ink px-8 py-2.5 rounded-full font-medium text-sm transition-colors">
                Cancelar
            </a>
        </div>

    </form>
</div>

@endsection
