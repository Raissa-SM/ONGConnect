<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ONGConnect')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#1B4F8E', accent: '#3BAA75' } } }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

    <nav class="bg-primary text-white shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-lg">
                <span class="text-accent text-2xl">🤝</span> ONGConnect
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('home') }}" class="hover:text-accent transition-colors">Demandas</a>
                <a href="/api/documentation" target="_blank" class="hover:text-accent transition-colors">API Docs ↗</a>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('login') }}" class="hover:text-accent transition-colors">Entrar</a>
                <a href="{{ route('registro') }}"
                   class="bg-accent hover:bg-green-600 text-white px-4 py-1.5 rounded-md transition-colors font-semibold">
                    Cadastre-se
                </a>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="max-w-6xl mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-800 rounded-lg px-4 py-3">
                ✅ {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-6xl mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-800 rounded-lg px-4 py-3">
                ❌ {{ session('error') }}
            </div>
        </div>
    @endif

    <main class="flex-1 max-w-6xl mx-auto px-4 py-8 w-full">
        @yield('content')
    </main>

    <footer class="bg-primary text-gray-300 text-sm py-6 mt-auto">
        <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-2">
            <span>© {{ date('Y') }} ONGConnect · Projeto de Extensão Unidavi</span>
            <span>Programação Web 2 + POO · Sistemas de Informação</span>
        </div>
    </footer>
</body>
</html>
