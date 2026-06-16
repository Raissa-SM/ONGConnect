@extends('layouts.app')
@section('title', 'Nova Demanda — ONGConnect')
@section('content')

<div class="max-w-3xl mx-auto px-6 py-12">

    <div class="mb-8">
        <a href="{{ route('demandas.minhas') }}" class="text-base text-ink-2 hover:text-primary transition-colors">← Minhas demandas</a>
        <h1 class="text-3xl font-bold tracking-tight text-ink mt-3">Nova demanda</h1>
        <p class="text-ink-2 text-base mt-2">Ela fica salva como rascunho. Você publica quando estiver pronta.</p>
    </div>

    <form method="POST" action="{{ route('demandas.store') }}" class="space-y-6">
        @csrf

        <div class="bg-surface rounded-2xl border border-border/60 p-6 space-y-5">
            <h2 class="text-lg font-semibold text-ink">Informações básicas</h2>

            <div>
                <label class="block text-base font-medium text-ink mb-2">Título <span class="text-danger">*</span></label>
                <input type="text" name="titulo" value="{{ old('titulo') }}" required
                    class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all @error('titulo') border-danger @enderror"
                    placeholder="Ex: Mutirão de limpeza no Rio do Sul">
                @error('titulo')
                    <p class="text-sm text-danger mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-base font-medium text-ink mb-2">Descrição <span class="text-danger">*</span></label>
                <textarea name="descricao" rows="5" required
                    class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all resize-none @error('descricao') border-danger @enderror"
                    placeholder="Explique o que o voluntário vai fazer e o que é preciso levar...">{{ old('descricao') }}</textarea>
                @error('descricao')
                    <p class="text-sm text-danger mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-base font-medium text-ink mb-2">Tipo de ajuda <span class="text-danger">*</span></label>
                <select name="tipo" required
                    class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all bg-white">
                    <option value="">Escolha uma opção...</option>
                    <option value="presencial" {{ old('tipo') === 'presencial' ? 'selected' : '' }}>Voluntariado Presencial</option>
                    <option value="doacao"     {{ old('tipo') === 'doacao'     ? 'selected' : '' }}>Doação de Materiais</option>
                    <option value="habilidade" {{ old('tipo') === 'habilidade' ? 'selected' : '' }}>Habilidade Específica</option>
                </select>
                @error('tipo')
                    <p class="text-sm text-danger mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 p-6 space-y-5">
            <h2 class="text-lg font-semibold text-ink">Datas e vagas</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-base font-medium text-ink mb-2">Inscrições começam</label>
                    <input type="date" name="data_inicio" value="{{ old('data_inicio') }}"
                        class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                </div>
                <div>
                    <label class="block text-base font-medium text-ink mb-2">Inscrições até</label>
                    <input type="date" name="data_limite" value="{{ old('data_limite') }}"
                        class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                </div>
                <div>
                    <label class="block text-base font-medium text-ink mb-2">Nº de vagas</label>
                    <input type="number" name="vagas" value="{{ old('vagas') }}" min="1" max="9999"
                        class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="Sem limite">
                </div>
            </div>
            <p class="text-sm text-ink-2">Essas datas são da <strong>inscrição</strong>. Deixe em branco se não quiser definir um limite.</p>
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 p-6 space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-ink">Quando o evento acontece</h2>
                <p class="text-sm text-ink-2 mt-1">Data e hora em que a atividade realmente ocorre. Diferente do prazo de inscrição.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-base font-medium text-ink mb-2">Começa em</label>
                    <input type="datetime-local" name="evento_inicio" value="{{ old('evento_inicio') }}"
                        class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all @error('evento_inicio') border-danger @enderror">
                    @error('evento_inicio')
                        <p class="text-sm text-danger mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-base font-medium text-ink mb-2">Termina em</label>
                    <input type="datetime-local" name="evento_fim" value="{{ old('evento_fim') }}"
                        class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all @error('evento_fim') border-danger @enderror">
                    @error('evento_fim')
                        <p class="text-sm text-danger mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <p class="text-sm text-ink-2">Deixe em branco para vagas sem data marcada (ex.: ajuda contínua).</p>
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 p-6 space-y-4">
            <div>
                <h2 class="text-lg font-semibold text-ink">Localização <span class="text-danger">*</span></h2>
                <p class="text-sm text-ink-2 mt-1">Busque o endereço ou clique no mapa. Cidade e estado são preenchidos sozinhos. Já começa no endereço da sua ONG.</p>
            </div>
            <x-mapa-localizacao
                :lat="old('latitude', $ong->latitude)"
                :lng="old('longitude', $ong->longitude)"
                :endereco="$ong->endereco"
                :cidade="$ong->cidade"
                :uf="$ong->uf"
                :interno="true"
                :required="true"
                map-id="mapa-demanda-criar"
            />
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 p-6">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-ink">Assuntos <span class="text-danger">*</span></h2>
                <p class="text-sm text-ink-2 mt-1">Marque ao menos um assunto relacionado a esta vaga. Ajuda voluntários a encontrá-la.</p>
                @error('categorias')<p class="text-sm text-danger mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5">
                @foreach($categorias as $cat)
                    <label class="cursor-pointer flex items-center gap-2.5 p-3 rounded-xl border border-border hover:border-primary/40 transition-all">
                        <input type="checkbox" name="categorias[]" value="{{ $cat->id }}"
                            {{ in_array($cat->id, old('categorias', [])) ? 'checked' : '' }}
                            class="w-5 h-5 text-primary rounded focus:ring-primary">
                        <span class="text-base text-ink">{{ $cat->nome }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-full font-semibold text-base transition-colors">
                Criar demanda
            </button>
            <a href="{{ route('demandas.minhas') }}"
               class="border border-border hover:border-ink-2 text-ink px-8 py-3 rounded-full font-semibold text-base transition-colors">
                Cancelar
            </a>
        </div>

    </form>
</div>

@endsection
