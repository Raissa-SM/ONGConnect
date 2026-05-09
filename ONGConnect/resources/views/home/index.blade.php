@extends('layouts.app')
@section('title', 'ONGConnect — Conectando Voluntários e ONGs')
@section('content')
<div class="text-center py-12">
    <div class="text-6xl mb-4">🤝</div>
    <h1 class="text-4xl font-bold text-primary mb-3">ONGConnect</h1>
    <p class="text-gray-500 text-lg max-w-xl mx-auto mb-8">
        Conectando voluntários do Alto Vale do Itajaí com ONGs que precisam de apoio.
    </p>
    <div class="flex justify-center gap-4">
        <a href="{{ route('registro') }}"
           class="bg-accent hover:bg-green-600 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
            Quero ser voluntário
        </a>
        <a href="/api/documentation" target="_blank"
           class="border border-primary text-primary hover:bg-primary hover:text-white font-semibold px-6 py-3 rounded-lg transition-colors">
            Ver API Docs ↗
        </a>
    </div>
</div>

<div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow p-6 border-t-4 border-blue-500">
        <div class="text-3xl mb-2">📋</div>
        <h3 class="font-bold text-lg mb-1">Voluntariado Presencial</h3>
        <p class="text-gray-500 text-sm">Mutirões, eventos e ações comunitárias.</p>
    </div>
    <div class="bg-white rounded-xl shadow p-6 border-t-4 border-green-500">
        <div class="text-3xl mb-2">📦</div>
        <h3 class="font-bold text-lg mb-1">Doação Material</h3>
        <p class="text-gray-500 text-sm">Alimentos, roupas e itens específicos.</p>
    </div>
    <div class="bg-white rounded-xl shadow p-6 border-t-4 border-purple-500">
        <div class="text-3xl mb-2">💡</div>
        <h3 class="font-bold text-lg mb-1">Habilidades Específicas</h3>
        <p class="text-gray-500 text-sm">Design, tecnologia, jurídico e mais.</p>
    </div>
</div>
<div class="mt-8 text-center text-sm text-gray-400">
    <a href="/api/documentation" class="underline text-primary">Acesse o Swagger UI</a> para testar a API.
</div>
@endsection
