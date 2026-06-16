@extends('layouts.app')
@section('title', 'Perfil da ONG — ONGConnect')
@section('content')

<div class="max-w-3xl mx-auto px-6 py-12">

    <div class="mb-8">
        <a href="{{ route('dashboard.ong') }}" class="text-base text-ink-2 hover:text-primary transition-colors">← Painel</a>
        <h1 class="text-3xl font-bold tracking-tight text-ink mt-3">Perfil da ONG</h1>
        <p class="text-ink-2 text-base mt-2">Um perfil completo passa mais confiança para os voluntários.</p>
    </div>

    <form method="POST" action="{{ route('perfil.ong.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-surface rounded-2xl border border-border/60 p-6 space-y-5">
            <h2 class="text-lg font-semibold text-ink">Dados da organização</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-base font-medium text-ink mb-2">Nome do responsável</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                        class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all">
                </div>
                <div>
                    <label class="block text-base font-medium text-ink mb-2">Nome oficial da ONG <span class="text-danger">*</span></label>
                    <input type="text" name="razao_social" value="{{ old('razao_social', $ong->razao_social) }}" required
                        class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all @error('razao_social') border-danger @enderror">
                    @error('razao_social')
                        <p class="text-sm text-danger mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-base font-medium text-ink mb-2">CNPJ</label>
                    <input type="text" name="cnpj" value="{{ old('cnpj', $ong->cnpj_formatado) }}" inputmode="numeric"
                        class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="00.000.000/0001-00">
                </div>
                <div>
                    <label class="block text-base font-medium text-ink mb-2">Telefone</label>
                    <input type="text" name="telefone" value="{{ old('telefone', $ong->telefone_formatado) }}" inputmode="numeric"
                        class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                        placeholder="(47) 99999-9999">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-base font-medium text-ink mb-2">Site (opcional)</label>
                    <input type="url" name="website" value="{{ old('website', $ong->website) }}"
                        class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all @error('website') border-danger @enderror"
                        placeholder="https://minhaong.org.br">
                    @error('website')
                        <p class="text-sm text-danger mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-base font-medium text-ink mb-2">Sobre a ONG / Missão</label>
                <textarea name="descricao_missao" rows="4"
                    class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all resize-none"
                    placeholder="Conte o que a sua ONG faz e quem ela ajuda...">{{ old('descricao_missao', $ong->descricao_missao) }}</textarea>
            </div>
        </div>

        <div class="bg-surface rounded-2xl border border-border/60 p-6 space-y-4">
            <div>
                <h2 class="text-lg font-semibold text-ink">Endereço e localização</h2>
                <p class="text-sm text-ink-2 mt-1">Busque o endereço ou clique no mapa. A cidade e o estado são preenchidos sozinhos.</p>
            </div>
            <x-mapa-localizacao
                :lat="old('latitude', $ong->latitude)"
                :lng="old('longitude', $ong->longitude)"
                :endereco="$ong->endereco"
                :cidade="$ong->cidade"
                :uf="$ong->uf"
                :interno="true"
                map-id="mapa-ong"
            />
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-full font-semibold text-base transition-colors">
                Salvar perfil
            </button>
            <a href="{{ route('dashboard.ong') }}"
               class="border border-border hover:border-ink-2 text-ink px-8 py-3 rounded-full font-semibold text-base transition-colors">
                Cancelar
            </a>
        </div>

    </form>
</div>

@endsection
