@extends('layouts.app')
@section('title', 'Criar conta — ONGConnect')
@section('content')

<div class="min-h-[80vh] flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-lg">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-ink">Criar conta</h1>
            <p class="text-base text-ink-2 mt-2">É grátis e leva menos de um minuto</p>
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 shadow-sm p-8">
            <form method="POST" action="{{ route('registro') }}" class="space-y-6" id="registroForm">
                @csrf

                {{-- Tipo de perfil --}}
                <div>
                    <p class="text-base font-medium text-ink mb-3">Como você quer usar o ONGConnect?</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="tipo_perfil" value="voluntario"
                                {{ old('tipo_perfil', 'voluntario') === 'voluntario' ? 'checked' : '' }}
                                class="peer sr-only" onchange="togglePerfil()">
                            <div class="border-2 border-border peer-checked:border-primary peer-checked:bg-primary/5 rounded-xl p-4 text-center transition-all">
                                <p class="font-semibold text-base text-ink">Sou voluntário</p>
                                <p class="text-sm text-ink-2 mt-1">Quero ajudar ONGs</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="tipo_perfil" value="ong"
                                {{ old('tipo_perfil') === 'ong' ? 'checked' : '' }}
                                class="peer sr-only" onchange="togglePerfil()">
                            <div class="border-2 border-border peer-checked:border-primary peer-checked:bg-primary/5 rounded-xl p-4 text-center transition-all">
                                <p class="font-semibold text-base text-ink">Sou uma ONG</p>
                                <p class="text-sm text-ink-2 mt-1">Procuro voluntários</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-base font-medium text-ink mb-2" id="labelName">Nome completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="Seu nome">
                </div>

                <div>
                    <label for="email" class="block text-base font-medium text-ink mb-2">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all @error('email') border-danger @enderror"
                        placeholder="seu@email.com">
                    @error('email')
                        <p class="text-sm text-danger mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-base font-medium text-ink mb-2">Senha</label>
                        <input type="password" id="password" name="password" required
                            class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                            placeholder="Mínimo 8 caracteres">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-base font-medium text-ink mb-2">Repita a senha</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                            placeholder="Digite a senha de novo">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label for="cidade" class="block text-base font-medium text-ink mb-2">Cidade</label>
                        <input type="text" id="cidade" name="cidade" value="{{ old('cidade') }}"
                            class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                            placeholder="Rio do Sul">
                    </div>
                    <div>
                        <label for="uf" class="block text-base font-medium text-ink mb-2">Estado (UF)</label>
                        <input type="text" id="uf" name="uf" value="{{ old('uf') }}" maxlength="2"
                            class="w-full rounded-xl border border-border px-4 py-3 text-base uppercase focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                            placeholder="SC">
                    </div>
                </div>

                <div id="camposVoluntario" class="space-y-4">
                    <div>
                        <label for="cpf" class="block text-base font-medium text-ink mb-2">CPF <span class="text-ink-2 font-normal text-sm">(opcional)</span></label>
                        <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}" inputmode="numeric"
                            class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                            placeholder="000.000.000-00">
                    </div>
                </div>

                <div id="camposOng" class="space-y-4 hidden">
                    <div>
                        <label for="razao_social" class="block text-base font-medium text-ink mb-2">Nome oficial da ONG <span class="text-danger">*</span></label>
                        <input type="text" id="razao_social" name="razao_social" value="{{ old('razao_social') }}"
                            class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all @error('razao_social') border-danger @enderror"
                            placeholder="Razão social da organização">
                        @error('razao_social')
                            <p class="text-sm text-danger mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="cnpj" class="block text-base font-medium text-ink mb-2">CNPJ <span class="text-ink-2 font-normal text-sm">(opcional)</span></label>
                        <input type="text" id="cnpj" name="cnpj" value="{{ old('cnpj') }}" inputmode="numeric"
                            class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                            placeholder="00.000.000/0001-00">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-primary hover:bg-primary-dark text-white py-3 rounded-full font-semibold text-base transition-colors">
                    Criar minha conta
                </button>
            </form>
        </div>

        <p class="text-center text-base text-ink-2 mt-6">
            Já tem conta?
            <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark font-semibold transition-colors">Entrar</a>
        </p>
    </div>
</div>

<script>
function togglePerfil() {
    const tipo = document.querySelector('input[name="tipo_perfil"]:checked').value;
    document.getElementById('camposVoluntario').classList.toggle('hidden', tipo !== 'voluntario');
    document.getElementById('camposOng').classList.toggle('hidden', tipo !== 'ong');
    document.getElementById('labelName').textContent = tipo === 'ong' ? 'Nome do responsável' : 'Nome completo';
}
togglePerfil();
</script>

@endsection
