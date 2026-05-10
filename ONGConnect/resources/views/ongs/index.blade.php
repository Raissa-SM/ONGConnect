@extends('layouts.app')
@section('title', 'ONGs — ONGConnect')
@section('content')

<div class="max-w-6xl mx-auto px-6 py-12">

    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-ink">ONGs parceiras</h1>
        <p class="text-ink-2 mt-1">Organizações que buscam voluntários no Alto Vale do Itajaí</p>
    </div>

    @if($ongs->isEmpty())
        <div class="text-center py-20 text-ink-2">
            <p class="text-lg font-medium text-ink mb-2">Nenhuma ONG cadastrada ainda</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            @foreach($ongs as $ong)
                <a href="{{ route('ongs.show', $ong->id) }}"
                   class="bg-surface rounded-2xl p-6 border border-border/50 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 block group">
                    <h3 class="font-semibold text-ink mb-1 group-hover:text-primary transition-colors">{{ $ong->razao_social }}</h3>
                    @if($ong->cidade)
                        <p class="text-sm text-ink-2 mb-3">{{ $ong->cidade }}{{ $ong->uf ? ', ' . $ong->uf : '' }}</p>
                    @endif
                    @if($ong->descricao_missao)
                        <p class="text-sm text-ink-2 leading-relaxed line-clamp-2 mb-4">{{ $ong->descricao_missao }}</p>
                    @endif
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-ink-2">{{ $ong->demandas_abertas_count }} demanda{{ $ong->demandas_abertas_count != 1 ? 's' : '' }} aberta{{ $ong->demandas_abertas_count != 1 ? 's' : '' }}</span>
                        @if($ong->demandas_abertas_count > 0)
                            <span class="bg-green-50 text-green-700 px-2 py-0.5 rounded-full font-medium">Ativa</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        {{ $ongs->links() }}
    @endif

</div>

@endsection
