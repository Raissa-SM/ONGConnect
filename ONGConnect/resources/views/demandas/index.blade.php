@extends('layouts.app')
@section('title', 'Demandas — ONGConnect')
@section('content')

<div class="max-w-6xl mx-auto px-6 py-12">

    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-ink">
            {{ $mostrarEncerradas ? 'Todas as demandas' : 'Demandas abertas' }}
        </h1>
        <p class="text-ink-2 mt-1">Encontre oportunidades de voluntariado no Alto Vale do Itajaí</p>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('demandas.index') }}" class="bg-surface rounded-2xl border border-border/60 p-5 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar demandas..."
                class="rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">

            <input type="text" name="cidade" value="{{ request('cidade') }}" placeholder="Cidade..."
                class="rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">

            <select name="tipo"
                class="rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all bg-white">
                <option value="">Todos os tipos</option>
                <option value="presencial" {{ request('tipo') === 'presencial' ? 'selected' : '' }}>Voluntariado Presencial</option>
                <option value="doacao"     {{ request('tipo') === 'doacao'     ? 'selected' : '' }}>Doação Material</option>
                <option value="habilidade" {{ request('tipo') === 'habilidade' ? 'selected' : '' }}>Habilidade Específica</option>
            </select>

            <select name="categoria_id"
                class="rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all bg-white">
                <option value="">Todas as categorias</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center justify-between mt-4 flex-wrap gap-3">
            <div class="flex gap-3 items-center flex-wrap">
                <button type="submit"
                    class="bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded-full text-sm font-medium transition-colors">
                    Filtrar
                </button>
                @if(request()->hasAny(['q','cidade','tipo','categoria_id']) || $mostrarEncerradas)
                    <a href="{{ route('demandas.index') }}"
                       class="border border-border hover:border-ink-2 text-ink-2 px-6 py-2 rounded-full text-sm font-medium transition-colors">
                        Limpar
                    </a>
                @endif
            </div>

            {{-- Toggle encerradas --}}
            <label class="flex items-center gap-2 cursor-pointer select-none text-sm text-ink-2 hover:text-ink transition-colors">
                <input type="checkbox" name="encerradas" value="1"
                    {{ $mostrarEncerradas ? 'checked' : '' }}
                    onchange="this.form.submit()"
                    class="rounded text-primary focus:ring-primary">
                Mostrar encerradas
            </label>
        </div>
    </form>

    {{-- Resultados --}}
    @if($demandas->isEmpty())
        <div class="text-center py-20 text-ink-2">
            <p class="text-lg font-medium text-ink mb-2">Nenhuma demanda encontrada</p>
            <p class="text-sm">Tente outros filtros ou volte mais tarde.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            @foreach($demandas as $demanda)
                @php $encerrada = $demanda->status->value === 'encerrada'; @endphp
                <a href="{{ route('demandas.show', $demanda->id) }}"
                   class="bg-surface rounded-2xl p-6 border border-border/50 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 block group {{ $encerrada ? 'opacity-70' : '' }}">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full
                                @if($demanda->tipo->value === 'presencial') bg-blue-50 text-blue-700
                                @elseif($demanda->tipo->value === 'doacao') bg-amber-50 text-amber-700
                                @else bg-purple-50 text-purple-700 @endif">
                                {{ $demanda->tipo->label() }}
                            </span>
                            @if($encerrada)
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-red-50 text-danger">
                                    Encerrada
                                </span>
                            @endif
                        </div>
                        @if(!$encerrada && $demanda->vagas)
                            <span class="text-xs text-ink-2 whitespace-nowrap">{{ $demanda->vagasDisponiveis() }} vagas</span>
                        @endif
                    </div>
                    <h3 class="font-semibold text-ink mb-1 leading-snug group-hover:text-primary transition-colors">{{ $demanda->titulo }}</h3>
                    <p class="text-sm text-ink-2 mb-3">{{ $demanda->ong->razao_social }}</p>
                    <p class="text-sm text-ink-2 line-clamp-2 mb-4">{{ $demanda->descricao }}</p>
                    <div class="flex items-center justify-between text-xs text-ink-2">
                        @if($demanda->cidade)
                            <span>{{ $demanda->cidade }}{{ $demanda->uf ? ', ' . $demanda->uf : '' }}</span>
                        @endif
                        @if($demanda->data_inicio)
                            <span>{{ $demanda->data_inicio->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    @if($demanda->categorias->count())
                        <div class="flex flex-wrap gap-1.5 mt-3">
                            @foreach($demanda->categorias->take(3) as $cat)
                                <span class="text-xs bg-page text-ink-2 px-2 py-0.5 rounded-full">{{ $cat->nome }}</span>
                            @endforeach
                        </div>
                    @endif
                </a>
            @endforeach
        </div>

        {{ $demandas->links() }}
    @endif

</div>

@endsection
