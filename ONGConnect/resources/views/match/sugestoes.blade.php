@extends('layouts.app')
@section('title', 'Vagas para você — ONGConnect')
@section('content')

<div class="max-w-5xl mx-auto px-6 py-12">

    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-ink">Vagas para você</h1>
        <p class="text-ink-2 mt-2 text-lg">Escolhemos estas vagas com base nos seus interesses e na distância da sua cidade.</p>
    </div>

    @if(!$apto)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center">
            <p class="text-xl font-semibold text-amber-900 mb-2">Falta pouco para receber suas vagas</p>
            <p class="text-base text-amber-800 mb-6 max-w-xl mx-auto">
                Para mostrarmos vagas feitas para você, informe onde você mora (no mapa do perfil) e escolha pelo menos um assunto de interesse.
            </p>
            <a href="{{ route('perfil.voluntario') }}"
               class="inline-block bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-full font-semibold text-base transition-colors">
                Completar perfil
            </a>
        </div>
    @elseif($sugestoes->isEmpty())
        <div class="bg-surface rounded-2xl border border-border/60 p-16 text-center">
            <p class="text-xl font-semibold text-ink mb-2">Nenhuma vaga encontrada por perto</p>
            <p class="text-base text-ink-2 mb-6">Não achamos vagas compatíveis na distância escolhida. Aumente a distância e tente de novo.</p>
            <form method="GET" action="{{ route('match.sugestoes') }}" class="flex justify-center gap-3 flex-wrap">
                <select name="raio_km" class="rounded-xl border border-border px-4 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 bg-white">
                    <option value="50">Até 50 km</option>
                    <option value="100">Até 100 km</option>
                    <option value="200">Até 200 km</option>
                    <option value="500">Até 500 km</option>
                </select>
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-full text-base font-semibold transition-colors">
                    Buscar
                </button>
            </form>
        </div>
    @else
        {{-- Distância máxima --}}
        <form method="GET" action="{{ route('match.sugestoes') }}" class="flex items-center gap-3 mb-6 flex-wrap">
            <label class="text-base text-ink-2">Distância máxima:</label>
            <select name="raio_km" onchange="this.form.submit()"
                class="rounded-xl border border-border px-4 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 bg-surface">
                @foreach([25, 50, 100, 200, 500] as $r)
                    <option value="{{ $r }}" {{ request('raio_km', 50) == $r ? 'selected' : '' }}>Até {{ $r }} km</option>
                @endforeach
            </select>
            <span class="text-base text-ink-2">· {{ $sugestoes->count() }} vaga(s) encontrada(s)</span>
        </form>

        <div class="space-y-4">
            @foreach($sugestoes as $item)
                @php
                    $demanda = $item['demanda'];
                    $score   = $item['score'];
                    $pct     = round($score['total'] * 100);
                    $barColor = $pct >= 70 ? 'bg-green-500' : ($pct >= 40 ? 'bg-amber-400' : 'bg-red-400');
                    $pctColor = $pct >= 70 ? 'text-green-600' : ($pct >= 40 ? 'text-amber-600' : 'text-danger');
                @endphp
                <div class="bg-surface rounded-2xl border border-border/60 p-6">
                    <div class="flex items-start gap-5 flex-wrap sm:flex-nowrap">

                        {{-- Compatibilidade --}}
                        <div class="shrink-0 text-center w-20">
                            <p class="text-3xl font-bold {{ $pctColor }}">{{ $pct }}%</p>
                            <div class="w-full bg-page rounded-full h-2 mt-1.5">
                                <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-sm text-ink-2 mt-1.5">combina com você</p>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <a href="{{ route('demandas.show', $demanda->id) }}"
                                   class="text-lg font-semibold text-ink hover:text-primary transition-colors">
                                    {{ $demanda->titulo }}
                                </a>
                                <span class="text-sm font-semibold px-3 py-1 rounded-full
                                    @if($demanda->tipo->value === 'presencial') bg-blue-50 text-blue-700
                                    @elseif($demanda->tipo->value === 'doacao') bg-amber-50 text-amber-800
                                    @else bg-purple-50 text-purple-700 @endif">
                                    {{ $demanda->tipo->label() }}
                                </span>
                            </div>
                            <p class="text-base text-ink-2 mb-3">{{ $demanda->ong->razao_social }}</p>

                            <div class="flex flex-wrap gap-x-5 gap-y-1 text-sm text-ink-2">
                                <span>Tem a ver com seus interesses: <strong class="text-ink">{{ round($score['categoria'] * 100) }}%</strong></span>
                                @if($score['distancia_km'] !== null)
                                    <span>A <strong class="text-ink">{{ $score['distancia_km'] }} km</strong> de você</span>
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
                                        <span class="text-sm bg-page text-ink-2 px-2.5 py-0.5 rounded-full">{{ $cat->nome }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Ação --}}
                        <div class="shrink-0 w-full sm:w-auto">
                            <a href="{{ route('demandas.show', $demanda->id) }}"
                               class="block text-center bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-full text-base font-semibold transition-colors">
                                Ver vaga
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

@endsection
