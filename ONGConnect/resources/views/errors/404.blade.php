@extends('layouts.app')
@section('title', 'Página não encontrada — ONGConnect')
@section('content')

<div class="min-h-[60vh] flex items-center justify-center px-6">
    <div class="text-center max-w-md">
        <p class="text-8xl font-bold text-primary/20 tracking-tighter mb-6">404</p>
        <h1 class="text-3xl font-bold tracking-tight text-ink mb-3">Página não encontrada</h1>
        <p class="text-lg text-ink-2 mb-8">O endereço que você acessou não existe ou foi removido.</p>
        <div class="flex gap-3 justify-center flex-wrap">
            <a href="{{ route('home') }}"
               class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-full font-semibold text-base transition-colors">
                Ir para a página inicial
            </a>
            <button onclick="history.back()"
                class="border border-border hover:border-ink-2 text-ink px-6 py-3 rounded-full font-semibold text-base transition-colors">
                Voltar
            </button>
        </div>
    </div>
</div>

@endsection
