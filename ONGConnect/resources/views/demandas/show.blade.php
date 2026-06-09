@extends('layouts.app')
@section('title', $demanda->titulo . ' — ONGConnect')
@section('content')

<div class="max-w-6xl mx-auto px-6 py-12">

    <div class="mb-6">
        <a href="{{ route('demandas.index') }}" class="text-sm text-ink-2 hover:text-primary transition-colors">← Demandas</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Principal --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Header --}}
            <div class="bg-surface rounded-2xl border border-border/60 p-8">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full
                                @if($demanda->tipo->value === 'presencial') bg-blue-50 text-blue-700
                                @elseif($demanda->tipo->value === 'doacao') bg-amber-50 text-amber-700
                                @else bg-purple-50 text-purple-700 @endif">
                                {{ $demanda->tipo->label() }}
                            </span>
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-green-50 text-green-700">
                                {{ $demanda->status->label() ?? 'Aberta' }}
                            </span>
                        </div>
                        <h1 class="text-2xl font-bold tracking-tight text-ink">{{ $demanda->titulo }}</h1>
                        <p class="text-ink-2 mt-1">{{ $demanda->ong->razao_social }}</p>
                    </div>
                </div>

                <p class="text-ink leading-relaxed whitespace-pre-line">{{ $demanda->descricao }}</p>
            </div>

            {{-- Detalhes --}}
            <div class="bg-surface rounded-2xl border border-border/60 p-6">
                <h2 class="font-semibold text-ink mb-4">Detalhes</h2>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    @if($demanda->cidade)
                        <div>
                            <dt class="text-ink-2">Localização</dt>
                            <dd class="font-medium text-ink mt-0.5">{{ $demanda->cidade }}{{ $demanda->uf ? ', ' . $demanda->uf : '' }}</dd>
                        </div>
                    @endif
                    @if($demanda->vagas)
                        <div>
                            <dt class="text-ink-2">Vagas disponíveis</dt>
                            <dd class="font-medium text-ink mt-0.5">{{ $demanda->vagasDisponiveis() }} / {{ $demanda->vagas }}</dd>
                        </div>
                    @endif
                    @if($demanda->data_inicio)
                        <div>
                            <dt class="text-ink-2">Início das inscrições</dt>
                            <dd class="font-medium text-ink mt-0.5">{{ $demanda->data_inicio->format('d/m/Y') }}</dd>
                        </div>
                    @endif
                    @if($demanda->data_limite)
                        <div>
                            <dt class="text-ink-2">Prazo para inscrição</dt>
                            <dd class="font-medium text-ink mt-0.5">{{ $demanda->data_limite->format('d/m/Y') }}</dd>
                        </div>
                    @endif
                    @if($demanda->endereco)
                        <div class="col-span-2">
                            <dt class="text-ink-2">Endereço</dt>
                            <dd class="font-medium text-ink mt-0.5">{{ $demanda->endereco }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Mapa de localização --}}
            @if($demanda->latitude && $demanda->longitude)
                <div class="bg-surface rounded-2xl border border-border/60 p-6">
                    <h2 class="font-semibold text-ink mb-4">Localização no mapa</h2>
                    <x-mapa-visualizacao
                        :lat="$demanda->latitude"
                        :lng="$demanda->longitude"
                        :label="$demanda->endereco ?? ($demanda->cidade ? $demanda->cidade . ($demanda->uf ? ', ' . $demanda->uf : '') : null)"
                        map-id="mapa-demanda"
                    />
                    @if($demanda->endereco || $demanda->cidade)
                        <p class="text-xs text-ink-2 mt-2">
                            {{ implode(', ', array_filter([$demanda->endereco, $demanda->cidade, $demanda->uf])) }}
                        </p>
                    @endif
                </div>
            @endif

            {{-- Categorias --}}
            @if($demanda->categorias->count())
                <div class="bg-surface rounded-2xl border border-border/60 p-6">
                    <h2 class="font-semibold text-ink mb-3">Categorias</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($demanda->categorias as $cat)
                            <span class="text-sm bg-page text-ink-2 px-3 py-1 rounded-full">{{ $cat->nome }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Inscrição --}}
            <div class="bg-surface rounded-2xl border border-border/60 p-6 sticky top-20">
                <h2 class="font-semibold text-ink mb-4">Quero participar</h2>

                @auth
                    @if(auth()->user()->isVoluntario())
                        @if($jaInscrito)
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4 text-sm text-green-800">
                                Você já está inscrito nesta demanda.
                                @if($inscricao)
                                    <br>Status: <strong>{{ $inscricao->status->label() }}</strong>
                                @endif
                            </div>
                            @if($inscricao && $inscricao->status->podeCancelarPeloVoluntario())
                                <form method="POST" action="{{ route('inscricoes.cancelar', $inscricao->id) }}">
                                    @csrf
                                    <button type="submit"
                                        onclick="return confirm('Cancelar inscrição?')"
                                        class="w-full border border-danger text-danger hover:bg-red-50 py-2.5 rounded-full text-sm font-medium transition-colors">
                                        Cancelar inscrição
                                    </button>
                                </form>
                            @endif
                        @elseif($demanda->estaAberta() && (!$demanda->vagas || $demanda->vagasDisponiveis() > 0))
                            <form method="POST" action="{{ route('inscricoes.store', $demanda->id) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-ink mb-1.5">Mensagem <span class="text-ink-2 font-normal">(opcional)</span></label>
                                    <textarea name="mensagem" rows="3" placeholder="Conte um pouco sobre você e por que quer participar..."
                                        class="w-full rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all resize-none"></textarea>
                                </div>
                                <button type="submit"
                                    class="w-full bg-primary hover:bg-primary-dark text-white py-2.5 rounded-full text-sm font-medium transition-colors">
                                    Inscrever-se
                                </button>
                            </form>
                        @else
                            <p class="text-sm text-ink-2 text-center py-4">Esta demanda não está aceitando novas inscrições.</p>
                        @endif
                    @else
                        <p class="text-sm text-ink-2 text-center py-2">Somente voluntários podem se inscrever.</p>
                    @endif
                @endauth
                @guest
                    <p class="text-sm text-ink-2 mb-4 text-center">Faça login para se inscrever nesta demanda.</p>
                    <a href="{{ route('login') }}"
                       class="block w-full text-center bg-primary hover:bg-primary-dark text-white py-2.5 rounded-full text-sm font-medium transition-colors">
                        Entrar para se inscrever
                    </a>
                    <a href="{{ route('registro') }}"
                       class="block w-full text-center border border-border hover:border-ink-2 text-ink py-2.5 rounded-full text-sm font-medium transition-colors mt-3">
                        Criar conta gratuita
                    </a>
                @endguest
            </div>

            {{-- ONG card --}}
            <div class="bg-surface rounded-2xl border border-border/60 p-6">
                <h2 class="font-semibold text-ink mb-3">Sobre a ONG</h2>
                <p class="font-medium text-ink text-sm">{{ $demanda->ong->razao_social }}</p>
                @if($demanda->ong->cidade)
                    <p class="text-xs text-ink-2 mt-1">{{ $demanda->ong->cidade }}{{ $demanda->ong->uf ? ', ' . $demanda->ong->uf : '' }}</p>
                @endif
                @if($demanda->ong->descricao_missao)
                    <p class="text-sm text-ink-2 mt-3 leading-relaxed line-clamp-3">{{ $demanda->ong->descricao_missao }}</p>
                @endif
                <a href="{{ route('ongs.show', $demanda->ong->id) }}"
                   class="inline-block mt-4 text-sm text-primary hover:text-primary-dark font-medium transition-colors">
                    Ver perfil completo →
                </a>
            </div>

        </div>
    </div>
</div>

@endsection
