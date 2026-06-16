@extends('layouts.app')
@section('title', 'Painel ONG — ONGConnect')
@section('content')

<div class="max-w-6xl mx-auto px-6 py-12">

    <div class="flex items-start justify-between mb-8 gap-4 flex-wrap">
        <div>
            <p class="text-base text-ink-2 mb-1">Painel da ONG</p>
            <h1 class="text-3xl font-bold tracking-tight text-ink">{{ $ong->razao_social }}</h1>
            @if($ong->cidade)
                <p class="text-ink-2 mt-1 text-base">{{ $ong->cidade }}{{ $ong->uf ? ', ' . $ong->uf : '' }}</p>
            @endif
        </div>
        <div class="flex gap-3 flex-wrap">
            <a href="{{ route('demandas.criar') }}"
               class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-full text-base font-semibold transition-colors">
                + Nova demanda
            </a>
            <a href="{{ route('perfil.ong') }}"
               class="border border-border hover:border-ink-2 text-ink px-6 py-2.5 rounded-full text-base font-medium transition-colors">
                Editar perfil
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-surface rounded-2xl border border-border/60 p-5">
            <p class="text-3xl font-bold text-ink">{{ $demandas->count() }}</p>
            <p class="text-base text-ink-2 mt-1">Demandas no total</p>
        </div>
        <div class="bg-surface rounded-2xl border border-border/60 p-5">
            <p class="text-3xl font-bold text-green-600">{{ $demandasPorStatus['aberta'] ?? 0 }}</p>
            <p class="text-base text-ink-2 mt-1">Vagas abertas</p>
        </div>
        <div class="bg-surface rounded-2xl border border-border/60 p-5">
            <p class="text-3xl font-bold text-amber-500">{{ $inscricoesPorStatus['pendente'] ?? 0 }}</p>
            <p class="text-base text-ink-2 mt-1">Aguardando resposta</p>
        </div>
        <div class="bg-surface rounded-2xl border border-border/60 p-5">
            @if($mediaAvaliacoes !== null)
                <p class="text-3xl font-bold text-primary">{{ number_format($mediaAvaliacoes, 1) }}</p>
                <p class="text-base text-ink-2 mt-1">Nota média</p>
            @else
                <p class="text-3xl font-bold text-ink-2">—</p>
                <p class="text-base text-ink-2 mt-1">Nota (após 3 avaliações)</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-surface rounded-2xl border border-border/60 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-ink">Inscrições aguardando resposta</h2>
                <a href="{{ route('demandas.minhas') }}" class="text-sm text-primary hover:text-primary-dark transition-colors font-medium">Ver demandas →</a>
            </div>
            @if($inscricoesPendentes->isEmpty())
                <p class="text-base text-ink-2 py-4 text-center">Nenhuma inscrição aguardando resposta.</p>
            @else
                <div class="space-y-3">
                    @foreach($inscricoesPendentes as $i)
                        <div class="py-3 border-b border-border/40 last:border-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <a href="{{ route('voluntarios.show', $i->voluntario->id) }}" class="font-semibold text-primary hover:text-primary-dark transition-colors text-base inline-block">{{ $i->voluntario->user?->name }}</a>
                                    <p class="text-sm text-ink-2 mt-0.5 truncate">{{ $i->demanda->titulo }}</p>
                                    @if($i->mensagem)
                                        <p class="text-sm text-ink-2 mt-1 line-clamp-1 italic">"{{ $i->mensagem }}"</p>
                                    @endif
                                </div>
                                <div class="flex gap-2 shrink-0">
                                    <form method="POST" action="{{ route('inscricoes.aceitar', $i->id) }}">
                                        @csrf
                                        <button type="submit" class="text-sm bg-green-50 hover:bg-green-100 text-green-700 px-4 py-2 rounded-full transition-colors font-semibold">Aceitar</button>
                                    </form>
                                    <form method="POST" action="{{ route('inscricoes.recusar', $i->id) }}">
                                        @csrf
                                        <button type="submit" class="text-sm bg-red-50 hover:bg-red-100 text-danger px-4 py-2 rounded-full transition-colors font-semibold">Recusar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-ink">Vagas abertas com vagas livres</h2>
                <a href="{{ route('demandas.minhas') }}" class="text-sm text-primary hover:text-primary-dark transition-colors font-medium">Ver todas →</a>
            </div>
            @if($demandasAbertas->isEmpty())
                <div class="text-center py-4">
                    <p class="text-base text-ink-2 mb-4">Nenhuma vaga aberta com lugares livres.</p>
                    <a href="{{ route('demandas.criar') }}"
                       class="inline-block text-base bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-full font-semibold transition-colors">
                        Criar primeira demanda
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($demandasAbertas as $d)
                        <div class="flex items-center justify-between py-2.5 border-b border-border/40 last:border-0 gap-4">
                            <div class="min-w-0">
                                <p class="font-semibold text-ink text-base truncate">{{ $d->titulo }}</p>
                                <p class="text-sm text-ink-2 mt-0.5">{{ $d->vagas ? $d->vagasDisponiveis() . ' vaga(s) livre(s)' : 'Vagas ilimitadas' }}</p>
                            </div>
                            <a href="{{ route('inscricoes.demanda', $d->id) }}"
                               class="text-sm text-primary hover:text-primary-dark font-semibold transition-colors shrink-0">
                                Ver inscrições →
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
