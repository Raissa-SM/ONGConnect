@extends('layouts.app')
@section('title', 'Inscrições — ' . $demanda->titulo)
@section('content')

<div class="max-w-5xl mx-auto px-6 py-12">

    <div class="mb-6">
        <a href="{{ route('demandas.minhas') }}" class="text-sm text-ink-2 hover:text-primary transition-colors">← Minhas demandas</a>
    </div>

    <div class="flex items-start justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink">{{ $demanda->titulo }}</h1>
            <div class="flex items-center gap-3 mt-2">
                <span class="text-xs font-medium px-2.5 py-1 rounded-full
                    @if($demanda->tipo->value === 'presencial') bg-blue-50 text-blue-700
                    @elseif($demanda->tipo->value === 'doacao') bg-amber-50 text-amber-700
                    @else bg-purple-50 text-purple-700 @endif">
                    {{ $demanda->tipo->label() }}
                </span>
                <span class="text-sm text-ink-2">{{ $inscricoes->total() }} inscrição(ões)</span>
                @if($demanda->vagas)
                    <span class="text-sm text-ink-2">· {{ $demanda->vagasDisponiveis() }} vaga(s) disponível(eis)</span>
                @endif
            </div>
        </div>
    </div>

    @if($inscricoes->isEmpty())
        <div class="bg-surface rounded-2xl border border-border/60 p-16 text-center text-ink-2">
            <p class="text-lg font-medium text-ink mb-2">Nenhuma inscrição ainda</p>
            <p class="text-sm">Quando voluntários se inscreverem, eles aparecerão aqui.</p>
        </div>
    @else
        <div class="bg-surface rounded-2xl border border-border/60 overflow-hidden">
            <div class="divide-y divide-border/40">
                @foreach($inscricoes as $inscricao)
                    @php
                        $badgeClass = match($inscricao->status->value) {
                            'aceita'    => 'bg-green-50 text-green-700',
                            'concluida' => 'bg-blue-50 text-blue-700',
                            'pendente'  => 'bg-amber-50 text-amber-700',
                            'recusada'  => 'bg-red-50 text-danger',
                            default     => 'bg-page text-ink-2',
                        };
                    @endphp
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-semibold text-ink">{{ $inscricao->voluntario->user?->name }}</p>
                                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $badgeClass }}">
                                        {{ $inscricao->status->label() }}
                                    </span>
                                </div>
                                @if($inscricao->mensagem)
                                    <p class="text-sm text-ink-2 mt-2 italic">"{{ $inscricao->mensagem }}"</p>
                                @endif
                                <p class="text-xs text-ink-2 mt-2">Inscrito em {{ $inscricao->created_at->format('d/m/Y') }}</p>
                            </div>

                            <div class="flex flex-wrap gap-2 shrink-0">
                                @if($inscricao->status->podeResponderPelaOng())
                                    <form method="POST" action="{{ route('inscricoes.aceitar', $inscricao->id) }}">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs bg-green-50 hover:bg-green-100 text-green-700 px-3 py-1.5 rounded-full transition-colors font-medium">
                                            Aceitar
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('inscricoes.recusar', $inscricao->id) }}">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs bg-red-50 hover:bg-red-100 text-danger px-3 py-1.5 rounded-full transition-colors font-medium">
                                            Recusar
                                        </button>
                                    </form>
                                @endif
                                @if($inscricao->status->value === 'aceita')
                                    <form method="POST" action="{{ route('inscricoes.concluir', $inscricao->id) }}">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full transition-colors font-medium">
                                            Concluir
                                        </button>
                                    </form>
                                @endif
                                @if($inscricao->podeAvaliar())
                                    @if(!$inscricao->avaliacoes()->where('autor_tipo', 'ong')->exists())
                                        <button onclick="document.getElementById('avaliar-{{ $inscricao->id }}').classList.toggle('hidden')"
                                            class="text-xs bg-primary/10 hover:bg-primary/20 text-primary px-3 py-1.5 rounded-full transition-colors font-medium">
                                            Avaliar voluntário
                                        </button>
                                    @else
                                        <span class="text-xs text-green-600 font-medium self-center">Avaliado</span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if($inscricao->podeAvaliar() && !$inscricao->avaliacoes()->where('autor_tipo', 'ong')->exists())
                            <div id="avaliar-{{ $inscricao->id }}" class="hidden mt-4 bg-page rounded-xl p-4">
                                <form method="POST" action="{{ route('avaliacoes.store', $inscricao->id) }}" class="flex items-end gap-4">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-medium text-ink mb-1.5">Nota (1–5)</label>
                                        <select name="nota" required class="rounded-xl border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                                            @for($n = 5; $n >= 1; $n--)
                                                <option value="{{ $n }}">{{ $n }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-ink mb-1.5">Comentário (opcional)</label>
                                        <input type="text" name="comentario" placeholder="Desempenho do voluntário..."
                                            class="w-full rounded-xl border border-border px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                                    </div>
                                    <button type="submit"
                                        class="bg-primary hover:bg-primary-dark text-white px-5 py-2 rounded-full text-sm font-medium transition-colors">
                                        Enviar
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6">{{ $inscricoes->links() }}</div>
    @endif

</div>

@endsection
