@extends('layouts.app')
@section('title', $ong->razao_social . ' — ONGConnect')
@section('content')

<div class="max-w-6xl mx-auto px-6 py-12">

    <div class="mb-6">
        <a href="{{ route('ongs.index') }}" class="text-base text-ink-2 hover:text-primary transition-colors">← Voltar para ONGs</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Info principal --}}
        <div class="lg:col-span-1">
            <div class="bg-surface rounded-2xl border border-border/60 p-6 sticky top-20">
                <h1 class="text-2xl font-bold tracking-tight text-ink mb-1">{{ $ong->razao_social }}</h1>
                @if($ong->cidade)
                    <p class="text-base text-ink-2 mb-4">{{ $ong->cidade }}{{ $ong->uf ? ', ' . $ong->uf : '' }}</p>
                @endif

                @if($ong->descricao_missao)
                    <p class="text-base text-ink leading-relaxed mb-4">{{ $ong->descricao_missao }}</p>
                @endif

                @if($ong->website)
                    <a href="{{ $ong->website }}" target="_blank" rel="noopener"
                       class="text-base text-primary hover:text-primary-dark transition-colors font-medium">
                        Site oficial →
                    </a>
                @endif

                @if($ong->telefone)
                    <p class="text-base text-ink-2 mt-3">{{ $ong->telefone_formatado }}</p>
                @endif

                @if($ong->cnpj)
                    <p class="text-sm text-ink-2 mt-2">CNPJ: {{ $ong->cnpj_formatado }}</p>
                @endif

                {{-- Avaliação --}}
                <div class="mt-5 pt-5 border-t border-border/50">
                    @if($media !== null)
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-bold text-ink">{{ number_format($media, 1) }}</span>
                            <x-estrelas :nota="$media" tamanho="text-lg" />
                        </div>
                        <p class="text-sm text-ink-2 mt-1">Média de {{ $avaliacoes->count() }} avaliações de voluntários</p>
                    @else
                        <p class="text-base font-semibold text-ink">Ainda sem nota</p>
                        <p class="text-sm text-ink-2 mt-1">A nota aparece após 3 avaliações ({{ $avaliacoes->count() }} até agora).</p>
                    @endif
                </div>

                {{-- Indicadores --}}
                <div class="mt-5 pt-5 border-t border-border/50 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-2xl font-bold text-ink">{{ $demandasAbertas->count() }}</p>
                        <p class="text-sm text-ink-2">Vagas abertas</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-ink">{{ $totalConcluidas }}</p>
                        <p class="text-sm text-ink-2">Trabalhos concluídos</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Demandas --}}
        <div class="lg:col-span-2 space-y-10">

            {{-- Abertas --}}
            <div>
                <h2 class="text-xl font-bold tracking-tight text-ink mb-5">Vagas abertas</h2>

                @if($demandasAbertas->isEmpty())
                    <div class="bg-surface rounded-2xl border border-border/60 p-10 text-center text-ink-2">
                        <p class="text-base">Nenhuma vaga aberta no momento.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($demandasAbertas as $demanda)
                            <a href="{{ route('demandas.show', $demanda->id) }}"
                               class="bg-surface rounded-2xl border border-border/50 p-6 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 block group">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-sm font-semibold px-3 py-1 rounded-full
                                                @if($demanda->tipo->value === 'presencial') bg-blue-50 text-blue-700
                                                @elseif($demanda->tipo->value === 'doacao') bg-amber-50 text-amber-800
                                                @else bg-purple-50 text-purple-700 @endif">
                                                {{ $demanda->tipo->label() }}
                                            </span>
                                        </div>
                                        <h3 class="text-lg font-semibold text-ink group-hover:text-primary transition-colors">{{ $demanda->titulo }}</h3>
                                        <p class="text-base text-ink-2 mt-1 line-clamp-2">{{ $demanda->descricao }}</p>
                                        @if($demanda->evento_inicio)
                                            <p class="text-sm font-semibold {{ $demanda->eventoEmAndamento() ? 'text-success' : 'text-ink' }} mt-2">
                                                @if($demanda->eventoEmAndamento())
                                                    ● Acontecendo agora
                                                @else
                                                    Evento · {{ $demanda->evento_inicio->format('d/m/Y \à\s H:i') }}
                                                @endif
                                            </p>
                                        @endif
                                        @if($demanda->categorias->count())
                                            <div class="flex flex-wrap gap-1.5 mt-3">
                                                @foreach($demanda->categorias->take(4) as $cat)
                                                    <span class="text-sm bg-page text-ink-2 px-2.5 py-0.5 rounded-full">{{ $cat->nome }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @if($demanda->vagas)
                                        <div class="text-right shrink-0">
                                            <p class="text-2xl font-bold text-ink">{{ $demanda->vagasDisponiveis() }}</p>
                                            <p class="text-sm text-ink-2">vagas</p>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Encerradas --}}
            @if($demandasEncerradas->isNotEmpty())
                <div>
                    <h2 class="text-lg font-semibold text-ink-2 mb-4">Vagas encerradas</h2>
                    <div class="space-y-3">
                        @foreach($demandasEncerradas as $demanda)
                            <a href="{{ route('demandas.show', $demanda->id) }}"
                               class="bg-surface rounded-2xl border border-border/50 p-5 hover:shadow-sm transition-all duration-200 block group opacity-75 hover:opacity-100">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <span class="text-sm font-semibold px-3 py-1 rounded-full
                                                @if($demanda->tipo->value === 'presencial') bg-blue-50 text-blue-700
                                                @elseif($demanda->tipo->value === 'doacao') bg-amber-50 text-amber-800
                                                @else bg-purple-50 text-purple-700 @endif">
                                                {{ $demanda->tipo->label() }}
                                            </span>
                                            <span class="text-sm font-semibold px-3 py-1 rounded-full bg-red-50 text-danger">
                                                Encerrada
                                            </span>
                                        </div>
                                        <h3 class="text-base font-medium text-ink-2 group-hover:text-ink transition-colors">{{ $demanda->titulo }}</h3>
                                        @if($demanda->categorias->count())
                                            <div class="flex flex-wrap gap-1.5 mt-2">
                                                @foreach($demanda->categorias->take(3) as $cat)
                                                    <span class="text-sm bg-page text-ink-2 px-2.5 py-0.5 rounded-full">{{ $cat->nome }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @if($demanda->data_limite)
                                        <p class="text-sm text-ink-2 shrink-0">{{ $demanda->data_limite->format('d/m/Y') }}</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Avaliações recebidas dos voluntários --}}
            <div>
                <h2 class="text-xl font-bold tracking-tight text-ink mb-5">O que os voluntários dizem ({{ $avaliacoes->count() }})</h2>
                @if($avaliacoes->isEmpty())
                    <div class="bg-surface rounded-2xl border border-border/60 p-10 text-center text-ink-2">
                        <p class="text-base">Esta ONG ainda não recebeu avaliações.</p>
                    </div>
                @else
                    <div class="bg-surface rounded-2xl border border-border/60 p-6 space-y-5">
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
                                    @if($av->inscricao?->voluntario)
                                        <a href="{{ route('voluntarios.show', $av->inscricao->voluntario->id) }}"
                                           class="font-semibold text-primary hover:text-primary-dark transition-colors">{{ $av->inscricao->voluntario->user?->name }}</a>
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
