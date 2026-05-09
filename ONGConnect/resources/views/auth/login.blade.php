@extends('layouts.app')
@section('title', 'Entrar — ONGConnect')
@section('content')
<div class="max-w-md mx-auto bg-white rounded-xl shadow p-8 mt-8">
    <h2 class="text-2xl font-bold text-primary mb-6 text-center">Entrar</h2>
    <p class="text-gray-500 text-center text-sm mb-6">
        Use o Swagger UI para autenticar via API nesta etapa de desenvolvimento.
    </p>
    <a href="/api/documentation" target="_blank"
       class="block w-full bg-primary text-white text-center py-3 rounded-lg font-semibold hover:bg-blue-800 transition-colors">
        Abrir Swagger UI
    </a>
    <p class="text-center mt-4 text-sm text-gray-400">
        Não tem conta? <a href="{{ route('registro') }}" class="text-primary underline">Cadastre-se</a>
    </p>
</div>
@endsection
