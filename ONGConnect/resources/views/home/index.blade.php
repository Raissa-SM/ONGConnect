@extends('layouts.app')
@section('title', 'ONGConnect — Conectando Voluntários e ONGs')
@section('content')

{{-- Hero --}}
<section class="text-center pt-24 pb-20 px-6 bg-white">
    <p class="text-sm font-semibold text-primary mb-4 tracking-widest uppercase">Alto Vale do Itajaí · Projeto de Extensão</p>
    <h1 class="text-5xl md:text-6xl font-bold tracking-tight text-ink mb-5 leading-tight">
        Voluntariado que<br><span class="text-primary">transforma</span> comunidades
    </h1>
    <p class="text-xl text-ink-2 max-w-2xl mx-auto mb-10 leading-relaxed">
        Conectamos voluntários comprometidos com ONGs que precisam de apoio real.
        Nosso algoritmo de match une suas habilidades às demandas mais próximas de você.
    </p>
    <div class="flex flex-wrap justify-center gap-4">
        @guest
            <a href="{{ route('registro') }}"
               class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-full font-medium text-base transition-colors shadow-sm">
                Quero ser voluntário
            </a>
            <a href="{{ route('demandas.index') }}"
               class="border border-border hover:border-ink-2 text-ink px-8 py-3 rounded-full font-medium text-base transition-colors">
                Ver demandas abertas
            </a>
        @endguest
        @auth
            @if(auth()->user()->isVoluntario())
                <a href="{{ route('match.sugestoes') }}"
                   class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-full font-medium text-base transition-colors shadow-sm">
                    Ver minhas sugestões
                </a>
            @else
                <a href="{{ route('demandas.criar') }}"
                   class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-full font-medium text-base transition-colors shadow-sm">
                    Criar demanda
                </a>
            @endif
            <a href="{{ route('demandas.index') }}"
               class="border border-border hover:border-ink-2 text-ink px-8 py-3 rounded-full font-medium text-base transition-colors">
                Explorar demandas
            </a>
        @endauth
    </div>
</section>

{{-- Stats --}}
<section class="bg-page py-12 px-6 border-y border-border/40">
    <div class="max-w-4xl mx-auto grid grid-cols-3 gap-8 text-center">
        <div>
            <p class="text-4xl font-bold text-ink">{{ $stats['voluntarios'] }}</p>
            <p class="text-sm text-ink-2 mt-1">Voluntários</p>
        </div>
        <div>
            <p class="text-4xl font-bold text-ink">{{ $stats['ongs'] }}</p>
            <p class="text-sm text-ink-2 mt-1">ONGs parceiras</p>
        </div>
        <div>
            <p class="text-4xl font-bold text-primary">{{ $stats['demandas_abertas'] }}</p>
            <p class="text-sm text-ink-2 mt-1">Demandas abertas</p>
        </div>
    </div>
</section>

{{-- Como funciona --}}
<section class="bg-white py-20 px-6">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold tracking-tight text-center text-ink mb-2">Como funciona</h2>
        <p class="text-center text-ink-2 mb-14">Em três passos você começa a fazer a diferença</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="text-center">
                <div class="w-12 h-12 bg-primary/10 text-primary rounded-2xl flex items-center justify-center mx-auto mb-5 font-bold text-lg">1</div>
                <h3 class="font-semibold text-ink mb-2">Crie seu perfil</h3>
                <p class="text-sm text-ink-2 leading-relaxed">Cadastre-se e informe suas habilidades, categorias de interesse e localização para ativar o match.</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-primary/10 text-primary rounded-2xl flex items-center justify-center mx-auto mb-5 font-bold text-lg">2</div>
                <h3 class="font-semibold text-ink mb-2">Receba sugestões</h3>
                <p class="text-sm text-ink-2 leading-relaxed">Nosso algoritmo combina categorias e localização (Haversine) para rankear as demandas mais compatíveis com você.</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-primary/10 text-primary rounded-2xl flex items-center justify-center mx-auto mb-5 font-bold text-lg">3</div>
                <h3 class="font-semibold text-ink mb-2">Faça a diferença</h3>
                <p class="text-sm text-ink-2 leading-relaxed">Candidate-se, seja aceito pela ONG e contribua com as comunidades do Alto Vale do Itajaí.</p>
            </div>
        </div>
    </div>
</section>

{{-- Demandas em destaque --}}
@if($demandasDestaque->count() > 0)
<section class="bg-page py-20 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-ink">Demandas abertas</h2>
                <p class="text-ink-2 mt-1">Oportunidades disponíveis agora</p>
            </div>
            <a href="{{ route('demandas.index') }}" class="text-sm text-primary hover:text-primary-dark transition-colors font-medium">
                Ver todas →
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($demandasDestaque as $demanda)
                <a href="{{ route('demandas.show', $demanda->id) }}"
                   class="bg-surface rounded-2xl p-6 border border-border/50 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 block group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full
                            @if($demanda->tipo->value === 'presencial') bg-blue-50 text-blue-700
                            @elseif($demanda->tipo->value === 'doacao') bg-amber-50 text-amber-700
                            @else bg-purple-50 text-purple-700 @endif">
                            {{ $demanda->tipo->label() }}
                        </span>
                        @if($demanda->vagas)
                            <span class="text-xs text-ink-2 whitespace-nowrap">{{ $demanda->vagasDisponiveis() }} vagas</span>
                        @endif
                    </div>
                    <h3 class="font-semibold text-ink mb-1 leading-snug group-hover:text-primary transition-colors">{{ $demanda->titulo }}</h3>
                    <p class="text-sm text-ink-2 mb-3">{{ $demanda->ong->razao_social }}</p>
                    @if($demanda->cidade)
                        <p class="text-xs text-ink-2">{{ $demanda->cidade }}{{ $demanda->uf ? ', ' . $demanda->uf : '' }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA Final --}}
@guest
<section class="bg-primary py-20 px-6 text-white text-center">
    <h2 class="text-3xl font-bold tracking-tight mb-4">Pronto para começar?</h2>
    <p class="text-white/80 mb-8 text-lg max-w-xl mx-auto">Junte-se a voluntários que estão transformando o Alto Vale do Itajaí.</p>
    <a href="{{ route('registro') }}"
       class="bg-white text-primary hover:bg-gray-50 px-8 py-3 rounded-full font-medium text-base transition-colors inline-block">
        Criar conta gratuita
    </a>
</section>
@endguest

@endsection
