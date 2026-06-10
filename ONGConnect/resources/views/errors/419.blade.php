@extends('layouts.app')
@section('title', 'Sessão expirada — ONGConnect')
@section('content')

<div class="min-h-[60vh] flex items-center justify-center px-6">
    <div class="text-center max-w-md">
        <p class="text-8xl font-bold text-primary/20 tracking-tighter mb-6">419</p>
        <h1 class="text-3xl font-bold tracking-tight text-ink mb-3">Sessão expirada</h1>
        <p class="text-lg text-ink-2 mb-8">A página ficou aberta por muito tempo. Volte e tente de novo.</p>
        <div class="flex gap-3 justify-center flex-wrap">
            <button onclick="history.back()"
                class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-full font-semibold text-base transition-colors">
                Voltar e tentar de novo
            </button>
            <a href="{{ route('home') }}"
               class="border border-border hover:border-ink-2 text-ink px-6 py-3 rounded-full font-semibold text-base transition-colors">
                Página inicial
            </a>
        </div>
    </div>
</div>

@endsection
