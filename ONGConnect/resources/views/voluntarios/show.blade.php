@extends('layouts.app')
@section('title', $voluntario->user->name . ' — ONGConnect')
@section('content')

<div class="max-w-5xl mx-auto px-6 py-12">

    <div class="mb-6">
        <a href="{{ url()->previous() }}" class="text-base text-ink-2 hover:text-primary transition-colors">← Voltar</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Cartão de identidade --}}
        <div class="lg:col-span-1">
            <div class="bg-surface rounded-2xl border border-border/60 p-6">
                <div class="w-16 h-16 rounded-full bg-primary/10 text-primary flex items-center justify-center text-2xl font-bold mb-4">
                    {{ mb_strtoupper(mb_substr($voluntario->user->name, 0, 1)) }}
                </div>
                <p class="text-sm text-ink-2 mb-1">Voluntário</p>
                <h1 class="text-2xl font-bold tracking-tight text-ink">{{ $voluntario->user->name }}</h1>
                @if($voluntario->cidade)
                    <p class="text-base text-ink-2 mt-1">{{ $voluntario->cidade }}{{ $voluntario->uf ? ', ' . $voluntario->uf : '' }}</p>
                @endif

                {{-- Avaliação --}}
                <div class="mt-5 pt-5 border-t border-border/50">
                    @if($media !== null)
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-bold text-ink">{{ number_format($media, 1) }}</span>
                            <x-estrelas :nota="$media" tamanho="text-lg" />
                        </div>
                        <p class="text-sm text-ink-2 mt-1">Média de {{ $avaliacoes->count() }} avaliações de ONGs</p>
                    @else
                        <p class="text-base font-semibold text-ink">Ainda sem nota</p>
                        <p class="text-sm text-ink-2 mt-1">A nota aparece após 3 avaliações ({{ $avaliacoes->count() }} até agora).</p>
                    @endif
                </div>

                {{-- Indicadores --}}
                <div class="mt-5 pt-5 border-t border-border/50 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-2xl font-bold text-ink">{{ $totalConcluidas }}</p>
                        <p class="text-sm text-ink-2">Trabalhos concluídos</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-ink">{{ $voluntario->categorias->count() }}</p>
                        <p class="text-sm text-ink-2">Áreas de interesse</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Conteúdo --}}
        <div class="lg:col-span-2 space-y-6">

            @if($voluntario->descricao)
                <div class="bg-surface rounded-2xl border border-border/60 p-6">
                    <h2 class="text-lg font-semibold text-ink mb-3">Sobre</h2>
                    <p class="text-base text-ink leading-relaxed whitespace-pre-line">{{ $voluntario->descricao }}</p>
                </div>
            @endif

            @if(!empty($voluntario->habilidades))
                <div class="bg-surface rounded-2xl border border-border/60 p-6">
                    <h2 class="text-lg font-semibold text-ink mb-3">Habilidades</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($voluntario->habilidades as $hab)
                            <span class="text-base bg-page text-ink px-3 py-1.5 rounded-full">{{ $hab }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!empty($voluntario->disponibilidade))
                <div class="bg-surface rounded-2xl border border-border/60 p-6">
                    <h2 class="text-lg font-semibold text-ink mb-3">Disponibilidade</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($voluntario->disponibilidade as $disp)
                            <span class="text-base bg-page text-ink px-3 py-1.5 rounded-full">{{ $disp }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($voluntario->categorias->count())
                <div class="bg-surface rounded-2xl border border-border/60 p-6">
                    <h2 class="text-lg font-semibold text-ink mb-3">Áreas de interesse</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($voluntario->categorias as $cat)
                            <span class="text-base bg-page text-ink-2 px-3 py-1.5 rounded-full">{{ $cat->nome }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Avaliações recebidas das ONGs --}}
            <div class="bg-surface rounded-2xl border border-border/60 p-6">
                <h2 class="text-lg font-semibold text-ink mb-4">O que as ONGs dizem ({{ $avaliacoes->count() }})</h2>
                @if($avaliacoes->isEmpty())
                    <p class="text-base text-ink-2 py-2">Este voluntário ainda não recebeu avaliações.</p>
                @else
                    <div class="space-y-5">
                        @foreach($avaliacoes as $av)
                            <div class="pb-5 border-b border-border/40 last:border-0 last:pb-0">
                                <div class="flex items-center justify-between gap-3 flex-wrap">
                                    <x-estrelas :nota="$av->nota" />
                                    <span class="text-sm text-ink-2">{{ $av->created_at->format('d/m/Y') }}</span>
                                </div>
                                @if($av->comentario)
                                    <p class="text-base text-ink mt-2 leading-relaxed">"{{ $av->comentario }}"</p>
                                @endif
                                <p class="text-sm text-ink-2 mt-2">
                                    @if($av->inscricao?->demanda?->ong)
                                        <a href="{{ route('ongs.show', $av->inscricao->demanda->ong->id) }}"
                                           class="font-semibold text-primary hover:text-primary-dark transition-colors">{{ $av->inscricao->demanda->ong->razao_social }}</a>
                                    @endif
                                    @if($av->inscricao?->demanda)
                                        · <a href="{{ route('demandas.show', $av->inscricao->demanda->id) }}"
                                             class="text-ink-2 hover:text-primary transition-colors">{{ $av->inscricao->demanda->titulo }}</a>
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

@endsection
