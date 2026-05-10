@extends('layouts.app')
@section('title', 'Entrar — ONGConnect')
@section('content')

<div class="min-h-[80vh] flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-sm">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-ink">Entrar</h1>
            <p class="text-sm text-ink-2 mt-1">Bem-vindo de volta ao ONGConnect</p>
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 shadow-sm p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-ink mb-1.5">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm text-ink bg-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all @error('email') border-danger ring-2 ring-danger/30 @enderror"
                        placeholder="seu@email.com">
                    @error('email')
                        <p class="text-xs text-danger mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-ink mb-1.5">Senha</label>
                    <input type="password" id="password" name="password" required
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm text-ink bg-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember" class="rounded border-border text-primary focus:ring-primary">
                    <label for="remember" class="text-sm text-ink-2">Lembrar de mim</label>
                </div>

                <button type="submit"
                    class="w-full bg-primary hover:bg-primary-dark text-white py-2.5 rounded-full font-medium text-sm transition-colors">
                    Entrar
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-ink-2 mt-6">
            Não tem conta?
            <a href="{{ route('registro') }}" class="text-primary hover:text-primary-dark font-medium transition-colors">Cadastre-se</a>
        </p>

    </div>
</div>

@endsection
