@extends('layouts.app')
@section('title', 'Match — ONGConnect')
@section('content')

<div class="max-w-5xl mx-auto px-6 py-12">

    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-ink">Sugestões de match</h1>
        <p class="text-ink-2 mt-1">Demandas rankeadas por compatibilidade de categorias e proximidade</p>
    </div>

    @if(!$apto)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center">
            <p class="text-lg font-semibold text-amber-900 mb-2">Perfil incompleto para o match</p>
            <p class="text-sm text-amber-700 mb-6">
                Para receber sugestões personalizadas, adicione sua localização (latitude e longitude) e pelo menos uma categoria de interesse ao seu perfil.
            </p>
            <a href="{{ route('perfil.voluntario') }}"
               class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-full font-medium text-sm transition-colors">
                Completar perfil
            </a>
        </div>
    @elseif($sugestoes->isEmpty())
        <div class="bg-surface rounded-2xl border border-border/60 p-16 text-center">
            <p class="text-lg font-medium text-ink mb-2">Nenhuma sugestão encontrada</p>
            <p class="text-sm text-ink-2 mb-6">Não encontramos demandas abertas compatíveis com seu perfil no raio de busca. Tente expandir o raio.</p>
            <form method="GET" action="{{ route('match.sugestoes') }}" class="flex justify-center gap-3">
                <select name="raio_km" class="rounded-xl border border-border px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 bg-white">
                    <option value="50">50 km</option>
                    <option value="100">100 km</option>
                    <option value="200">200 km</option>
                    <option value="500">500 km</option>
                </select>
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-5 py-2 rounded-full text-sm font-medium transition-colors">
                    Buscar
                </button>
            </form>
        </div>
    @else
        {{-- Filtro de raio --}}
        <form method="GET" action="{{ route('match.sugestoes') }}" class="flex items-center gap-3 mb-6">
            <label class="text-sm text-ink-2">Raio de busca:</label>
            <select name="raio_km" onchange="this.form.submit()"
                class="rounded-xl border border-border px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 bg-surface">
                @foreach([25, 50, 100, 200, 500] as $r)
                    <option value="{{ $r }}" {{ request('raio_km', 50) == $r ? 'selected' : '' }}>{{ $r }} km</option>
                @endforeach
            </select>
            <span class="text-sm text-ink-2">· {{ $sugestoes->count() }} resultado(s)</span>
        </form>

        <div class="space-y-4">
            @foreach($sugestoes as $item)
                @php
                    $demanda = $item['demanda'];
                    $score   = $item['score'];
                    $pct     = round($score['total'] * 100);
                    $barColor = $pct >= 70 ? 'bg-green-500' : ($pct >= 40 ? 'bg-amber-400' : 'bg-red-400');
                @endphp
                <div class="bg-surface rounded-2xl border border-border/60 p-6">
                    <div class="flex items-start gap-5">

                        {{-- Score --}}
                        <div class="shrink-0 text-center w-16">
                            <p class="text-2xl font-bold {{ $pct >= 70 ? 'text-green-600' : ($pct >= 40 ? 'text-amber-500' : 'text-danger') }}">
                                {{ $pct }}%
                            </p>
                            <div class="w-full bg-page rounded-full h-1.5 mt-1">
                                <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-xs text-ink-2 mt-1">match</p>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <a href="{{ route('demandas.show', $demanda->id) }}"
                                   class="font-semibold text-ink hover:text-primary transition-colors">
                                    {{ $demanda->titulo }}
                                </a>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                    @if($demanda->tipo->value === 'presencial') bg-blue-50 text-blue-700
                                    @elseif($demanda->tipo->value === 'doacao') bg-amber-50 text-amber-700
                                    @else bg-purple-50 text-purple-700 @endif">
                                    {{ $demanda->tipo->label() }}
                                </span>
                            </div>
                            <p class="text-sm text-ink-2 mb-3">{{ $demanda->ong->razao_social }}</p>

                            <div class="flex flex-wrap gap-4 text-xs text-ink-2">
                                <span>Cat: {{ round($score['categoria'] * 100) }}%</span>
                                <span>Prox: {{ round($score['proximidade'] * 100) }}%</span>
                                @if($score['distancia_km'] !== null)
                                    <span>{{ $score['distancia_km'] }} km</span>
                                @endif
                                @if($demanda->cidade)
                                    <span>{{ $demanda->cidade }}{{ $demanda->uf ? ', ' . $demanda->uf : '' }}</span>
                                @endif
                                @if($demanda->vagas)
                                    <span>{{ $demanda->vagasDisponiveis() }} vaga(s)</span>
                                @endif
                            </div>

                            @if($demanda->categorias->count())
                                <div class="flex flex-wrap gap-1.5 mt-3">
                                    @foreach($demanda->categorias as $cat)
                                        <span class="text-xs bg-page text-ink-2 px-2 py-0.5 rounded-full">{{ $cat->nome }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Ação --}}
                        <div class="shrink-0">
                            <a href="{{ route('demandas.show', $demanda->id) }}"
                               class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-full text-sm font-medium transition-colors">
                                Ver →
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

@endsection
