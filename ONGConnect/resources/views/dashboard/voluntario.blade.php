@extends('layouts.app')
@section('title', 'Painel — ONGConnect')
@section('content')

<div class="max-w-6xl mx-auto px-6 py-12">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <p class="text-sm text-ink-2 mb-1">Bem-vindo de volta</p>
            <h1 class="text-3xl font-bold tracking-tight text-ink">{{ auth()->user()->name }}</h1>
            @if($voluntario->cidade)
                <p class="text-ink-2 mt-1 text-sm">{{ $voluntario->cidade }}{{ $voluntario->uf ? ', ' . $voluntario->uf : '' }}</p>
            @endif
        </div>
        <div class="flex gap-3">
            <a href="{{ route('match.sugestoes') }}"
               class="bg-primary hover:bg-primary-dark text-white px-5 py-2 rounded-full text-sm font-medium transition-colors">
                Ver Match
            </a>
            <a href="{{ route('perfil.voluntario') }}"
               class="border border-border hover:border-ink-2 text-ink px-5 py-2 rounded-full text-sm font-medium transition-colors">
                Editar perfil
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-surface rounded-2xl border border-border/60 p-5">
            <p class="text-3xl font-bold text-ink">{{ $inscricoes->count() }}</p>
            <p class="text-sm text-ink-2 mt-1">Total de inscrições</p>
        </div>
        <div class="bg-surface rounded-2xl border border-border/60 p-5">
            <p class="text-3xl font-bold text-green-600">{{ $porStatus['aceita'] ?? 0 }}</p>
            <p class="text-sm text-ink-2 mt-1">Aceitas</p>
        </div>
        <div class="bg-surface rounded-2xl border border-border/60 p-5">
            <p class="text-3xl font-bold text-primary">{{ $porStatus['concluida'] ?? 0 }}</p>
            <p class="text-sm text-ink-2 mt-1">Concluídas</p>
        </div>
        <div class="bg-surface rounded-2xl border border-border/60 p-5">
            @if($mediaAvaliacoes !== null)
                <p class="text-3xl font-bold text-amber-500">{{ number_format($mediaAvaliacoes, 1) }}</p>
                <p class="text-sm text-ink-2 mt-1">Avaliação média</p>
            @else
                <p class="text-3xl font-bold text-ink-2">—</p>
                <p class="text-sm text-ink-2 mt-1">Avaliação (mín. 3)</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Próximas atividades --}}
        <div class="bg-surface rounded-2xl border border-border/60 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-semibold text-ink">Próximas atividades</h2>
            </div>
            @if($proximas->isEmpty())
                <p class="text-sm text-ink-2 py-4 text-center">Nenhuma atividade futura agendada.</p>
            @else
                <div class="space-y-3">
                    @foreach($proximas as $i)
                        <div class="flex items-start gap-4 py-3 border-b border-border/40 last:border-0">
                            <div class="text-center bg-primary/10 text-primary rounded-xl px-3 py-2 shrink-0">
                                <p class="text-xs font-medium">{{ $i->demanda->data_inicio->format('M') }}</p>
                                <p class="text-lg font-bold leading-none">{{ $i->demanda->data_inicio->format('d') }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-ink text-sm truncate">{{ $i->demanda->titulo }}</p>
                                <p class="text-xs text-ink-2 mt-0.5">{{ $i->demanda->ong->razao_social }}</p>
                                @if($i->demanda->cidade)
                                    <p class="text-xs text-ink-2">{{ $i->demanda->cidade }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Últimas inscrições --}}
        <div class="bg-surface rounded-2xl border border-border/60 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-semibold text-ink">Últimas inscrições</h2>
                <a href="{{ route('inscricoes.minhas') }}" class="text-xs text-primary hover:text-primary-dark transition-colors">Ver todas →</a>
            </div>
            @if($ultimas->isEmpty())
                <p class="text-sm text-ink-2 py-4 text-center">Você ainda não tem inscrições.</p>
                <a href="{{ route('demandas.index') }}" class="block text-center mt-2 text-sm text-primary hover:text-primary-dark transition-colors">Explorar demandas →</a>
            @else
                <div class="space-y-3">
                    @foreach($ultimas as $i)
                        <div class="flex items-center justify-between py-2.5 border-b border-border/40 last:border-0 gap-4">
                            <div class="min-w-0">
                                <p class="font-medium text-ink text-sm truncate">{{ $i->demanda->titulo }}</p>
                                <p class="text-xs text-ink-2 mt-0.5">{{ $i->demanda->ong->razao_social }}</p>
                            </div>
                            @php
                                $badgeClass = match($i->status->value) {
                                    'aceita'    => 'bg-green-50 text-green-700',
                                    'concluida' => 'bg-blue-50 text-blue-700',
                                    'pendente'  => 'bg-amber-50 text-amber-700',
                                    'recusada'  => 'bg-red-50 text-danger',
                                    default     => 'bg-page text-ink-2',
                                };
                            @endphp
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full shrink-0 {{ $badgeClass }}">
                                {{ $i->status->label() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Match CTA --}}
    @if(!$voluntario->aptoParaMatch())
        <div class="mt-6 bg-amber-50 border border-amber-200 rounded-2xl p-6 flex items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-amber-900">Complete seu perfil para ativar o Match</p>
                <p class="text-sm text-amber-700 mt-1">Adicione sua localização e pelo menos uma categoria de interesse.</p>
            </div>
            <a href="{{ route('perfil.voluntario') }}"
               class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-full text-sm font-medium transition-colors shrink-0">
                Completar perfil
            </a>
        </div>
    @endif

</div>

@endsection
