@extends('layouts.app')
@section('title', 'Criar conta — ONGConnect')
@section('content')

<div class="min-h-[80vh] flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-ink">Criar conta</h1>
            <p class="text-sm text-ink-2 mt-1">Junte-se à rede de voluntariado do Alto Vale</p>
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 shadow-sm p-8">
            <form method="POST" action="{{ route('registro') }}" class="space-y-5" id="registroForm">
                @csrf

                {{-- Tipo de perfil --}}
                <div>
                    <p class="text-sm font-medium text-ink mb-3">Você é</p>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="tipo_perfil" value="voluntario"
                                {{ old('tipo_perfil', 'voluntario') === 'voluntario' ? 'checked' : '' }}
                                class="peer sr-only" onchange="togglePerfil()">
                            <div class="border-2 border-border peer-checked:border-primary peer-checked:bg-primary/5 rounded-xl p-3 text-center transition-all">
                                <p class="font-semibold text-sm text-ink">Voluntário</p>
                                <p class="text-xs text-ink-2 mt-0.5">Quero ajudar ONGs</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="tipo_perfil" value="ong"
                                {{ old('tipo_perfil') === 'ong' ? 'checked' : '' }}
                                class="peer sr-only" onchange="togglePerfil()">
                            <div class="border-2 border-border peer-checked:border-primary peer-checked:bg-primary/5 rounded-xl p-3 text-center transition-all">
                                <p class="font-semibold text-sm text-ink">ONG</p>
                                <p class="text-xs text-ink-2 mt-0.5">Busco voluntários</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-ink mb-1.5">Nome completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="Seu nome">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-ink mb-1.5">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all @error('email') border-danger @enderror"
                        placeholder="seu@email.com">
                    @error('email')
                        <p class="text-xs text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="password" class="block text-sm font-medium text-ink mb-1.5">Senha</label>
                        <input type="password" id="password" name="password" required
                            class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                            placeholder="Mín. 8 chars">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-ink mb-1.5">Confirmar</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                            placeholder="Repita">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="cidade" class="block text-sm font-medium text-ink mb-1.5">Cidade</label>
                        <input type="text" id="cidade" name="cidade" value="{{ old('cidade') }}"
                            class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                            placeholder="Rio do Sul">
                    </div>
                    <div>
                        <label for="uf" class="block text-sm font-medium text-ink mb-1.5">UF</label>
                        <input type="text" id="uf" name="uf" value="{{ old('uf') }}" maxlength="2"
                            class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                            placeholder="SC">
                    </div>
                </div>

                <div id="camposVoluntario" class="space-y-4">
                    <div>
                        <label for="cpf" class="block text-sm font-medium text-ink mb-1.5">CPF <span class="text-ink-2 font-normal">(opcional)</span></label>
                        <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}"
                            class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                            placeholder="000.000.000-00">
                    </div>
                </div>

                <div id="camposOng" class="space-y-4 hidden">
                    <div>
                        <label for="razao_social" class="block text-sm font-medium text-ink mb-1.5">Razão Social <span class="text-danger">*</span></label>
                        <input type="text" id="razao_social" name="razao_social" value="{{ old('razao_social') }}"
                            class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all @error('razao_social') border-danger @enderror"
                            placeholder="Nome oficial da ONG">
                        @error('razao_social')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="cnpj" class="block text-sm font-medium text-ink mb-1.5">CNPJ <span class="text-ink-2 font-normal">(opcional)</span></label>
                        <input type="text" id="cnpj" name="cnpj" value="{{ old('cnpj') }}"
                            class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                            placeholder="00.000.000/0001-00">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-primary hover:bg-primary-dark text-white py-2.5 rounded-full font-medium text-sm transition-colors">
                    Criar conta
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-ink-2 mt-6">
            Já tem conta?
            <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark font-medium transition-colors">Entrar</a>
        </p>
    </div>
</div>

<script>
function togglePerfil() {
    const tipo = document.querySelector('input[name="tipo_perfil"]:checked').value;
    document.getElementById('camposVoluntario').classList.toggle('hidden', tipo !== 'voluntario');
    document.getElementById('camposOng').classList.toggle('hidden', tipo !== 'ong');
}
togglePerfil();
</script>

@endsection
