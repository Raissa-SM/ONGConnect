<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ONGConnect')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-page text-ink min-h-screen flex flex-col antialiased">

    {{-- Navegação --}}
    <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-border/60">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between gap-5">

            <a href="{{ route('home') }}" class="font-bold text-ink text-lg tracking-tight shrink-0">
                ONGConnect
            </a>

            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('demandas.index') }}" class="text-[15px] text-ink-2 hover:text-primary transition-colors">Demandas</a>
                <a href="{{ route('ongs.index') }}" class="text-[15px] text-ink-2 hover:text-primary transition-colors">ONGs</a>
                @auth
                    @if(auth()->user()->isVoluntario())
                        <a href="{{ route('match.sugestoes') }}" class="text-[15px] text-ink-2 hover:text-primary transition-colors">Para você</a>
                        <a href="{{ route('inscricoes.minhas') }}" class="text-[15px] text-ink-2 hover:text-primary transition-colors">Minhas inscrições</a>
                    @else
                        <a href="{{ route('demandas.minhas') }}" class="text-[15px] text-ink-2 hover:text-primary transition-colors">Minhas demandas</a>
                    @endif
                @endauth
            </div>

            <div class="flex items-center gap-3 shrink-0">
                @auth
                    @if(auth()->user()->isOng())
                        <span class="hidden md:inline-flex text-[13px] bg-blue-50 text-primary px-2.5 py-1 rounded-full font-semibold">ONG</span>
                        <a href="{{ route('dashboard.ong') }}" class="text-[15px] font-semibold text-primary hover:text-primary-dark transition-colors">Painel</a>
                        <a href="{{ route('perfil.ong') }}" class="hidden sm:inline text-[15px] text-ink-2 hover:text-ink transition-colors">Perfil</a>
                    @else
                        <span class="hidden md:inline-flex text-[13px] bg-green-50 text-green-700 px-2.5 py-1 rounded-full font-semibold">Voluntário</span>
                        <a href="{{ route('dashboard.voluntario') }}" class="text-[15px] font-semibold text-primary hover:text-primary-dark transition-colors">Painel</a>
                        <a href="{{ route('perfil.voluntario') }}" class="hidden sm:inline text-[15px] text-ink-2 hover:text-ink transition-colors">Perfil</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-[15px] text-ink-2 hover:text-ink transition-colors">Sair</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="text-[15px] font-medium text-ink-2 hover:text-ink transition-colors">Entrar</a>
                    <a href="{{ route('registro') }}" class="text-[15px] bg-primary hover:bg-primary-dark text-white px-5 py-2 rounded-full font-semibold transition-colors">Criar conta</a>
                @endguest
            </div>
        </div>
    </nav>

    {{-- Avisos (sucesso / erro) --}}
    @if(session('success') || session('error') || $errors->any())
        <div class="max-w-6xl mx-auto w-full px-6 pt-5 space-y-3">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-4 text-base font-medium flex items-start gap-3">
                    <span aria-hidden="true" class="text-green-600 font-bold">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-danger rounded-xl px-5 py-4 text-base font-medium flex items-start gap-3">
                    <span aria-hidden="true" class="font-bold">!</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-danger rounded-xl px-5 py-4 text-base">
                    <p class="font-semibold mb-1.5">Confira os campos abaixo:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <main class="flex-1 w-full">
        @yield('content')
    </main>

    <footer class="border-t border-border/60 mt-auto">
        <div class="max-w-6xl mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-2 text-sm text-ink-2">
            <span class="font-semibold text-ink text-base">ONGConnect</span>
            <span>Projeto de Extensão · Programação Web 2 + POO · Unidavi 2026</span>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
