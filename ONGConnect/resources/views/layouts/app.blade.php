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

    {{-- Navigation --}}
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-border/60">
        <div class="max-w-6xl mx-auto px-6 h-14 flex items-center justify-between gap-6">

            <a href="{{ route('home') }}" class="font-semibold text-ink text-base tracking-tight shrink-0">
                ONGConnect
            </a>

            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('demandas.index') }}" class="text-sm text-ink-2 hover:text-primary transition-colors">Demandas</a>
                <a href="{{ route('ongs.index') }}" class="text-sm text-ink-2 hover:text-primary transition-colors">ONGs</a>
                @auth
                    @if(auth()->user()->isVoluntario())
                        <a href="{{ route('match.sugestoes') }}" class="text-sm text-ink-2 hover:text-primary transition-colors">Match</a>
                        <a href="{{ route('inscricoes.minhas') }}" class="text-sm text-ink-2 hover:text-primary transition-colors">Inscrições</a>
                    @else
                        <a href="{{ route('demandas.minhas') }}" class="text-sm text-ink-2 hover:text-primary transition-colors">Minhas Demandas</a>
                    @endif
                @endauth
            </div>

            <div class="flex items-center gap-3 shrink-0">
                @auth
                    <span class="hidden md:block text-sm text-ink-2 truncate max-w-32">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->isOng())
                        <span class="hidden md:inline-flex text-xs bg-blue-50 text-primary px-2 py-0.5 rounded-full font-medium">ONG</span>
                        <a href="{{ route('dashboard.ong') }}" class="text-sm font-medium text-primary hover:text-primary-dark transition-colors">Painel</a>
                        <a href="{{ route('perfil.ong') }}" class="text-sm text-ink-2 hover:text-ink transition-colors">Perfil</a>
                    @else
                        <span class="hidden md:inline-flex text-xs bg-green-50 text-green-700 px-2 py-0.5 rounded-full font-medium">Voluntário</span>
                        <a href="{{ route('dashboard.voluntario') }}" class="text-sm font-medium text-primary hover:text-primary-dark transition-colors">Painel</a>
                        <a href="{{ route('perfil.voluntario') }}" class="text-sm text-ink-2 hover:text-ink transition-colors">Perfil</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-ink-2 hover:text-ink transition-colors">Sair</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="text-sm text-ink-2 hover:text-ink transition-colors">Entrar</a>
                    <a href="{{ route('registro') }}" class="text-sm bg-primary hover:bg-primary-dark text-white px-4 py-1.5 rounded-full font-medium transition-colors">Cadastrar</a>
                @endguest
            </div>
        </div>
    </nav>

    {{-- Toasts --}}
    @if(session('success') || session('error') || $errors->any())
        <div class="max-w-6xl mx-auto w-full px-6 pt-4 space-y-2">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-danger rounded-xl px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-danger rounded-xl px-4 py-3 text-sm">
                    <ul class="list-disc list-inside space-y-0.5">
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
            <span class="font-medium text-ink">ONGConnect</span>
            <span>Projeto de Extensão · Programação Web 2 + POO · Unidavi 2026</span>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
