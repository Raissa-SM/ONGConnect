@extends('layouts.app')
@section('title', 'Sessão expirada — ONGConnect')
@section('content')

<div class="min-h-[60vh] flex items-center justify-center px-6">
    <div class="text-center max-w-md">
        <p class="text-8xl font-bold text-primary/20 tracking-tighter mb-6">419</p>
        <h1 class="text-2xl font-bold tracking-tight text-ink mb-3">Sessão expirada</h1>
        <p class="text-ink-2 mb-8">Sua sessão expirou ou o formulário ficou aberto por muito tempo. Volte e tente novamente.</p>
        <div class="flex gap-3 justify-center">
            <button onclick="history.back()"
                class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-full font-medium text-sm transition-colors">
                Voltar e tentar de novo
            </button>
            <a href="{{ route('home') }}"
               class="border border-border hover:border-ink-2 text-ink px-6 py-2.5 rounded-full font-medium text-sm transition-colors">
                Página inicial
            </a>
        </div>
    </div>
</div>

@endsection
