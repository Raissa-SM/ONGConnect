@extends('layouts.app')
@section('title', 'Nova Demanda — ONGConnect')
@section('content')

<div class="max-w-3xl mx-auto px-6 py-12">

    <div class="mb-8">
        <a href="{{ route('demandas.minhas') }}" class="text-sm text-ink-2 hover:text-primary transition-colors">← Minhas demandas</a>
        <h1 class="text-2xl font-bold tracking-tight text-ink mt-3">Nova demanda</h1>
        <p class="text-ink-2 text-sm mt-1">A demanda será criada como rascunho. Publique quando estiver pronta.</p>
    </div>

    <form method="POST" action="{{ route('demandas.store') }}" class="space-y-6">
        @csrf

        <div class="bg-surface rounded-2xl border border-border/60 p-6 space-y-5">
            <h2 class="font-semibold text-ink">Informações básicas</h2>

            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Título <span class="text-danger">*</span></label>
                <input type="text" name="titulo" value="{{ old('titulo') }}" required
                    class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all @error('titulo') border-danger @enderror"
                    placeholder="Ex: Mutirão de limpeza no Rio do Sul">
                @error('titulo')
                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Descrição <span class="text-danger">*</span></label>
                <textarea name="descricao" rows="5" required
                    class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all resize-none @error('descricao') border-danger @enderror"
                    placeholder="Descreva a atividade, o que o voluntário irá fazer, o que é necessário...">{{ old('descricao') }}</textarea>
                @error('descricao')
                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Tipo <span class="text-danger">*</span></label>
                <select name="tipo" required
                    class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all bg-white">
                    <option value="">Selecione o tipo...</option>
                    <option value="presencial" {{ old('tipo') === 'presencial' ? 'selected' : '' }}>Voluntariado Presencial</option>
                    <option value="doacao"     {{ old('tipo') === 'doacao'     ? 'selected' : '' }}>Doação Material</option>
                    <option value="habilidade" {{ old('tipo') === 'habilidade' ? 'selected' : '' }}>Habilidade Específica</option>
                </select>
                @error('tipo')
                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 p-6 space-y-5">
            <h2 class="font-semibold text-ink">Datas e vagas</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Início das inscrições</label>
                    <input type="date" name="data_inicio" value="{{ old('data_inicio') }}"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Prazo de inscrição</label>
                    <input type="date" name="data_limite" value="{{ old('data_limite') }}"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Nº de vagas</label>
                    <input type="number" name="vagas" value="{{ old('vagas') }}" min="1" max="9999"
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
                    <input type="text" name="endereco" value="{{ old('endereco') }}"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="Rua e número">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">UF</label>
                    <input type="text" name="uf" value="{{ old('uf') }}" maxlength="2"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="SC">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-ink mb-1.5">Cidade</label>
                    <input type="text" name="cidade" value="{{ old('cidade') }}"
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="Rio do Sul">
                </div>
            </div>
            <x-mapa-localizacao
                :lat="old('latitude')"
                :lng="old('longitude')"
                map-id="mapa-demanda-criar"
            />
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 p-6">
            <div class="mb-4">
                <h2 class="font-semibold text-ink">Categorias</h2>
                <p class="text-xs text-ink-2 mt-0.5">Selecione as categorias que se aplicam a esta demanda.</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                @foreach($categorias as $cat)
                    <label class="cursor-pointer flex items-center gap-2 p-3 rounded-xl border border-border hover:border-primary/40 transition-all">
                        <input type="checkbox" name="categorias[]" value="{{ $cat->id }}"
                            {{ in_array($cat->id, old('categorias', [])) ? 'checked' : '' }}
                            class="text-primary rounded focus:ring-primary">
                        <span class="text-sm text-ink">{{ $cat->nome }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="bg-primary hover:bg-primary-dark text-white px-8 py-2.5 rounded-full font-medium text-sm transition-colors">
                Criar demanda
            </button>
            <a href="{{ route('demandas.minhas') }}"
               class="border border-border hover:border-ink-2 text-ink px-8 py-2.5 rounded-full font-medium text-sm transition-colors">
                Cancelar
            </a>
        </div>

    </form>
</div>

@endsection
