@extends('layouts.app')
@section('title', $ong->razao_social . ' — ONGConnect')
@section('content')

<div class="max-w-6xl mx-auto px-6 py-12">

    <div class="mb-6">
        <a href="{{ route('ongs.index') }}" class="text-sm text-ink-2 hover:text-primary transition-colors">← ONGs</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Info principal --}}
        <div class="lg:col-span-1">
            <div class="bg-surface rounded-2xl border border-border/60 p-6 sticky top-20">
                <h1 class="text-xl font-bold tracking-tight text-ink mb-1">{{ $ong->razao_social }}</h1>
                @if($ong->cidade)
                    <p class="text-sm text-ink-2 mb-4">{{ $ong->cidade }}{{ $ong->uf ? ', ' . $ong->uf : '' }}</p>
                @endif

                @if($ong->descricao_missao)
                    <p class="text-sm text-ink leading-relaxed mb-4">{{ $ong->descricao_missao }}</p>
                @endif

                @if($ong->website)
                    <a href="{{ $ong->website }}" target="_blank"
                       class="text-sm text-primary hover:text-primary-dark transition-colors">
                        Site oficial →
                    </a>
                @endif

                @if($ong->telefone)
                    <p class="text-sm text-ink-2 mt-3">{{ $ong->telefone }}</p>
                @endif

                @if($ong->cnpj)
                    <p class="text-xs text-ink-2 mt-2">CNPJ: {{ $ong->cnpj }}</p>
                @endif
            </div>
        </div>

        {{-- Demandas --}}
        <div class="lg:col-span-2">
            <h2 class="text-xl font-bold tracking-tight text-ink mb-5">Demandas abertas</h2>

            @if($ong->demandas->isEmpty())
                <div class="bg-surface rounded-2xl border border-border/60 p-10 text-center text-ink-2">
                    <p>Nenhuma demanda aberta no momento.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($ong->demandas as $demanda)
                        <a href="{{ route('demandas.show', $demanda->id) }}"
                           class="bg-surface rounded-2xl border border-border/50 p-6 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 block group">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-xs font-medium px-2.5 py-1 rounded-full
                                            @if($demanda->tipo->value === 'presencial') bg-blue-50 text-blue-700
                                            @elseif($demanda->tipo->value === 'doacao') bg-amber-50 text-amber-700
                                            @else bg-purple-50 text-purple-700 @endif">
                                            {{ $demanda->tipo->label() }}
                                        </span>
                                    </div>
                                    <h3 class="font-semibold text-ink group-hover:text-primary transition-colors">{{ $demanda->titulo }}</h3>
                                    <p class="text-sm text-ink-2 mt-1 line-clamp-2">{{ $demanda->descricao }}</p>
                                    @if($demanda->categorias->count())
                                        <div class="flex flex-wrap gap-1.5 mt-3">
                                            @foreach($demanda->categorias->take(4) as $cat)
                                                <span class="text-xs bg-page text-ink-2 px-2 py-0.5 rounded-full">{{ $cat->nome }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                @if($demanda->vagas)
                                    <div class="text-right shrink-0">
                                        <p class="text-2xl font-bold text-ink">{{ $demanda->vagasDisponiveis() }}</p>
                                        <p class="text-xs text-ink-2">vagas</p>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
