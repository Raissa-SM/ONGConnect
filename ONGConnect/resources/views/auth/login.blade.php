@extends('layouts.app')
@section('title', 'Entrar — ONGConnect')
@section('content')

<div class="min-h-[80vh] flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-ink">Entrar</h1>
            <p class="text-base text-ink-2 mt-2">Bem-vindo de volta ao ONGConnect</p>
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 shadow-sm p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-base font-medium text-ink mb-2">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full rounded-xl border border-border px-4 py-3 text-base text-ink bg-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all @error('email') border-danger ring-2 ring-danger/30 @enderror"
                        placeholder="seu@email.com">
                    @error('email')
                        <p class="text-sm text-danger mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-base font-medium text-ink mb-2">Senha</label>
                    <input type="password" id="password" name="password" required
                        class="w-full rounded-xl border border-border px-4 py-3 text-base text-ink bg-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="Digite sua senha">
                </div>

                <div class="flex items-center gap-2.5">
                    <input type="checkbox" id="remember" name="remember" class="w-5 h-5 rounded border-border text-primary focus:ring-primary">
                    <label for="remember" class="text-base text-ink-2">Continuar conectado neste aparelho</label>
                </div>

                <button type="submit"
                    class="w-full bg-primary hover:bg-primary-dark text-white py-3 rounded-full font-semibold text-base transition-colors">
                    Entrar
                </button>
            </form>
        </div>

        <p class="text-center text-base text-ink-2 mt-6">
            Não tem conta?
            <a href="{{ route('registro') }}" class="text-primary hover:text-primary-dark font-semibold transition-colors">Criar conta</a>
        </p>

    </div>
</div>

@endsection
