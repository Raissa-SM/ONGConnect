@extends('layouts.app')
@section('title', 'Minhas Inscrições — ONGConnect')
@section('content')

<div class="max-w-5xl mx-auto px-6 py-12">

    <div class="flex items-center justify-between mb-8 gap-4 flex-wrap">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-ink">Minhas inscrições</h1>
            <p class="text-ink-2 text-base mt-1">Acompanhe a situação de todas as suas inscrições</p>
        </div>
        <a href="{{ route('demandas.index') }}"
           class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-full text-base font-semibold transition-colors">
            Explorar vagas
        </a>
    </div>

    @if($inscricoes->isEmpty())
        <div class="bg-surface rounded-2xl border border-border/60 p-16 text-center">
            <p class="text-xl font-semibold text-ink mb-2">Você ainda não se inscreveu em nada</p>
            <p class="text-base text-ink-2 mb-6">Encontre vagas que combinam com você e inscreva-se.</p>
            <a href="{{ route('match.sugestoes') }}"
               class="inline-block bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-full text-base font-semibold transition-colors">
                Ver vagas para você
            </a>
        </div>
    @else
        <div class="bg-surface rounded-2xl border border-border/60 overflow-hidden">
            <div class="divide-y divide-border/40">
                @foreach($inscricoes as $inscricao)
                    @php
                        $badgeClass = match($inscricao->status->value) {
                            'aceita'    => 'bg-green-50 text-green-700',
                            'concluida' => 'bg-blue-50 text-blue-700',
                            'pendente'  => 'bg-amber-50 text-amber-800',
                            'recusada'  => 'bg-red-50 text-danger',
                            default     => 'bg-page text-ink-2',
                        };
                    @endphp
                    <div class="p-5 flex items-center justify-between gap-6 flex-wrap">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-3 flex-wrap">
                                <a href="{{ route('demandas.show', $inscricao->demanda->id) }}"
                                   class="text-lg font-semibold text-ink hover:text-primary transition-colors">
                                    {{ $inscricao->demanda->titulo }}
                                </a>
                                <span class="text-sm font-semibold px-3 py-1 rounded-full {{ $badgeClass }}">
                                    {{ $inscricao->status->label() }}
                                </span>
                            </div>
                            <p class="text-base text-ink-2 mt-1">{{ $inscricao->demanda->ong->razao_social }}</p>
                            <div class="flex items-center gap-4 mt-2 text-sm text-ink-2 flex-wrap">
                                <span>Inscrito em {{ $inscricao->created_at->format('d/m/Y') }}</span>
                                @if($inscricao->respondida_em)
                                    <span>Respondido em {{ $inscricao->respondida_em->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            @if($inscricao->status->podeCancelarPeloVoluntario())
                                <form method="POST" action="{{ route('inscricoes.cancelar', $inscricao->id) }}">
                                    @csrf
                                    <button type="submit"
                                        onclick="return confirm('Tem certeza que deseja cancelar esta inscrição?')"
                                        class="text-sm border border-border hover:border-danger text-ink-2 hover:text-danger px-5 py-2 rounded-full transition-colors font-medium">
                                        Cancelar
                                    </button>
                                </form>
                            @endif
                            @if($inscricao->podeAvaliar())
                                @if(!$inscricao->avaliacoes()->where('autor_tipo', 'voluntario')->exists())
                                    <button onclick="document.getElementById('avaliar-{{ $inscricao->id }}').classList.toggle('hidden')"
                                        class="text-sm bg-primary/10 hover:bg-primary/20 text-primary px-5 py-2 rounded-full transition-colors font-semibold">
                                        Avaliar ONG
                                    </button>
                                @else
                                    <span class="text-sm text-green-700 font-semibold">✓ ONG avaliada</span>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if($inscricao->podeAvaliar() && !$inscricao->avaliacoes()->where('autor_tipo', 'voluntario')->exists())
                        <div id="avaliar-{{ $inscricao->id }}" class="hidden bg-page px-5 pb-5 pt-4">
                            <p class="text-sm font-medium text-ink mb-3">Como foi sua experiência com esta ONG?</p>
                            <form method="POST" action="{{ route('avaliacoes.store', $inscricao->id) }}" class="flex items-end gap-4 flex-wrap">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-ink mb-2">Nota (1 a 5)</label>
                                    <select name="nota" required class="rounded-xl border border-border px-3 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
                                        @for($n = 5; $n >= 1; $n--)
                                            <option value="{{ $n }}">{{ $n }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-sm font-medium text-ink mb-2">Comentário (opcional)</label>
                                    <input type="text" name="comentario" placeholder="Conte como foi..."
                                        class="w-full rounded-xl border border-border px-4 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary">
                                </div>
                                <button type="submit"
                                    class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-full text-base font-semibold transition-colors">
                                    Enviar avaliação
                                </button>
                            </form>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="mt-6">{{ $inscricoes->links() }}</div>
    @endif

</div>

@endsection
