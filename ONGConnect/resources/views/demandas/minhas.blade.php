@extends('layouts.app')
@section('title', 'Minhas Demandas — ONGConnect')
@section('content')

<div class="max-w-5xl mx-auto px-6 py-12">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink">Minhas demandas</h1>
            <p class="text-ink-2 text-sm mt-1">Gerencie as oportunidades de voluntariado da sua ONG</p>
        </div>
        <a href="{{ route('demandas.criar') }}"
           class="bg-primary hover:bg-primary-dark text-white px-5 py-2 rounded-full text-sm font-medium transition-colors">
            Nova demanda
        </a>
    </div>

    @if($demandas->isEmpty())
        <div class="bg-surface rounded-2xl border border-border/60 p-16 text-center">
            <p class="text-lg font-medium text-ink mb-2">Nenhuma demanda criada</p>
            <p class="text-sm text-ink-2 mb-6">Crie sua primeira demanda para começar a receber voluntários.</p>
            <a href="{{ route('demandas.criar') }}"
               class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-full text-sm font-medium transition-colors">
                Criar primeira demanda
            </a>
        </div>
    @else
        <div class="bg-surface rounded-2xl border border-border/60 overflow-hidden">
            <div class="divide-y divide-border/40">
                @foreach($demandas as $demanda)
                    @php
                        $statusBadge = match($demanda->status->value) {
                            'aberta'    => 'bg-green-50 text-green-700',
                            'rascunho'  => 'bg-page text-ink-2',
                            'encerrada' => 'bg-red-50 text-danger',
                            default     => 'bg-page text-ink-2',
                        };
                    @endphp
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <a href="{{ route('demandas.show', $demanda->id) }}"
                                       class="font-semibold text-ink hover:text-primary transition-colors">
                                        {{ $demanda->titulo }}
                                    </a>
                                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $statusBadge }}">
                                        {{ $demanda->status->label() }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-xs text-ink-2">
                                    <span>{{ $demanda->tipo->label() }}</span>
                                    @if($demanda->vagas)
                                        <span>{{ $demanda->vagasDisponiveis() }}/{{ $demanda->vagas }} vagas</span>
                                    @endif
                                    <span>{{ $demanda->inscricoes_count }} inscrição(ões)</span>
                                    @if($demanda->cidade)
                                        <span>{{ $demanda->cidade }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2 shrink-0">
                                @if($demanda->inscricoes_count > 0)
                                    <a href="{{ route('inscricoes.demanda', $demanda->id) }}"
                                       class="text-xs bg-primary/10 hover:bg-primary/20 text-primary px-3 py-1.5 rounded-full transition-colors font-medium">
                                        Inscrições
                                    </a>
                                @endif
                                <a href="{{ route('demandas.editar', $demanda->id) }}"
                                   class="text-xs border border-border hover:border-ink-2 text-ink-2 px-3 py-1.5 rounded-full transition-colors font-medium">
                                    Editar
                                </a>
                                @if($demanda->status->value === 'rascunho')
                                    <form method="POST" action="{{ route('demandas.publicar', $demanda->id) }}">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs bg-green-50 hover:bg-green-100 text-green-700 px-3 py-1.5 rounded-full transition-colors font-medium">
                                            Publicar
                                        </button>
                                    </form>
                                @elseif($demanda->status->value === 'aberta')
                                    <form method="POST" action="{{ route('demandas.concluir', $demanda->id) }}"
                                          onsubmit="return confirm('Concluir a demanda e marcar todas as inscrições aceitas como concluídas? Esta ação não pode ser desfeita.')">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs bg-primary/10 hover:bg-primary/20 text-primary px-3 py-1.5 rounded-full transition-colors font-medium">
                                            Concluir demanda
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('demandas.destroy', $demanda->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Excluir esta demanda? Esta ação não pode ser desfeita.')"
                                        class="text-xs text-ink-2 hover:text-danger px-3 py-1.5 rounded-full transition-colors font-medium">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

@endsection
