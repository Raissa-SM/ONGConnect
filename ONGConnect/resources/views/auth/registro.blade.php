@extends('layouts.app')
@section('title', 'Cadastro — ONGConnect')
@section('content')
<div class="max-w-md mx-auto bg-white rounded-xl shadow p-8 mt-8">
    <h2 class="text-2xl font-bold text-primary mb-6 text-center">Criar Conta</h2>
    <p class="text-gray-500 text-center text-sm mb-6">
        Cadastre-se via Swagger UI usando <code class="bg-gray-100 px-1 rounded text-xs">POST /api/auth/registro</code>.
    </p>
    <a href="/api/documentation" target="_blank"
       class="block w-full bg-accent text-white text-center py-3 rounded-lg font-semibold hover:bg-green-600 transition-colors">
        Abrir Swagger UI
    </a>
    <p class="text-center mt-4 text-sm text-gray-400">
        Já tem conta? <a href="{{ route('login') }}" class="text-primary underline">Entrar</a>
    </p>
</div>
@endsection
