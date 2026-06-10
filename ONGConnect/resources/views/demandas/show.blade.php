@extends('layouts.app')
@section('title', $demanda->titulo . ' — ONGConnect')
@section('content')

<div class="max-w-6xl mx-auto px-6 py-12">

    <div class="mb-6">
        <a href="{{ route('demandas.index') }}" class="text-base text-ink-2 hover:text-primary transition-colors">← Voltar para as vagas</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Principal --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Cabeçalho --}}
            <div class="bg-surface rounded-2xl border border-border/60 p-8">
                <div class="flex items-center gap-2 mb-4 flex-wrap">
                    <span class="text-sm font-semibold px-3 py-1 rounded-full
                        @if($demanda->tipo->value === 'presencial') bg-blue-50 text-blue-700
                        @elseif($demanda->tipo->value === 'doacao') bg-amber-50 text-amber-800
                        @else bg-purple-50 text-purple-700 @endif">
                        {{ $demanda->tipo->label() }}
                    </span>
                    @if($demanda->estaAberta())
                        <span class="text-sm font-semibold px-3 py-1 rounded-full bg-green-50 text-green-700">
                            Inscrições abertas
                        </span>
                    @else
                        <span class="text-sm font-semibold px-3 py-1 rounded-full bg-red-50 text-danger">
                            Inscrições encerradas
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-ink">{{ $demanda->titulo }}</h1>
                <p class="text-ink-2 mt-2 text-lg">{{ $demanda->ong->razao_social }}</p>

                <p class="text-ink leading-relaxed whitespace-pre-line mt-6 text-base">{{ $demanda->descricao }}</p>
            </div>

            {{-- Detalhes --}}
            <div class="bg-surface rounded-2xl border border-border/60 p-6">
                <h2 class="text-lg font-semibold text-ink mb-4">Informações da vaga</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5 text-base">
                    @if($demanda->cidade)
                        <div>
                            <dt class="text-ink-2 text-sm">Onde é</dt>
                            <dd class="font-semibold text-ink mt-0.5">{{ $demanda->cidade }}{{ $demanda->uf ? ', ' . $demanda->uf : '' }}</dd>
                        </div>
                    @endif
                    @if($demanda->vagas)
                        <div>
                            <dt class="text-ink-2 text-sm">Vagas disponíveis</dt>
                            <dd class="font-semibold text-ink mt-0.5">{{ $demanda->vagasDisponiveis() }} de {{ $demanda->vagas }}</dd>
                        </div>
                    @endif
                    @if($demanda->data_inicio)
                        <div>
                            <dt class="text-ink-2 text-sm">Inscrições começam em</dt>
                            <dd class="font-semibold text-ink mt-0.5">{{ $demanda->data_inicio->format('d/m/Y') }}</dd>
                        </div>
                    @endif
                    @if($demanda->data_limite)
                        <div>
                            <dt class="text-ink-2 text-sm">Inscrições vão até</dt>
                            <dd class="font-semibold text-ink mt-0.5">{{ $demanda->data_limite->format('d/m/Y') }}</dd>
                        </div>
                    @endif
                    @if($demanda->endereco)
                        <div class="sm:col-span-2">
                            <dt class="text-ink-2 text-sm">Endereço</dt>
                            <dd class="font-semibold text-ink mt-0.5">{{ $demanda->endereco }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Mapa --}}
            @if($demanda->latitude && $demanda->longitude)
                <div class="bg-surface rounded-2xl border border-border/60 p-6">
                    <h2 class="text-lg font-semibold text-ink mb-4">Onde fica</h2>
                    <x-mapa-visualizacao
                        :lat="$demanda->latitude"
                        :lng="$demanda->longitude"
                        :label="$demanda->endereco ?? ($demanda->cidade ? $demanda->cidade . ($demanda->uf ? ', ' . $demanda->uf : '') : null)"
                        map-id="mapa-demanda"
                    />
                    @if($demanda->endereco || $demanda->cidade)
                        <p class="text-sm text-ink-2 mt-3">
                            {{ implode(', ', array_filter([$demanda->endereco, $demanda->cidade, $demanda->uf])) }}
                        </p>
                    @endif
                </div>
            @endif

            {{-- Categorias --}}
            @if($demanda->categorias->count())
                <div class="bg-surface rounded-2xl border border-border/60 p-6">
                    <h2 class="text-lg font-semibold text-ink mb-3">Assuntos</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($demanda->categorias as $cat)
                            <span class="text-base bg-page text-ink-2 px-3 py-1.5 rounded-full">{{ $cat->nome }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- Lateral --}}
        <div class="space-y-5">

            {{-- Inscrição --}}
            <div class="bg-surface rounded-2xl border border-border/60 p-6 sticky top-20">
                <h2 class="text-lg font-semibold text-ink mb-4">Quero participar</h2>

                @auth
                    @if(auth()->user()->isVoluntario())
                        @if($jaInscrito)
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4 text-base text-green-800">
                                Você já se inscreveu nesta vaga.
                                @if($inscricao)
                                    <br><span class="text-sm">Situação atual: <strong>{{ $inscricao->status->label() }}</strong></span>
                                @endif
                            </div>
                            @if($inscricao && $inscricao->status->podeCancelarPeloVoluntario())
                                <form method="POST" action="{{ route('inscricoes.cancelar', $inscricao->id) }}">
                                    @csrf
                                    <button type="submit"
                                        onclick="return confirm('Tem certeza que deseja cancelar sua inscrição?')"
                                        class="w-full border border-danger text-danger hover:bg-red-50 py-3 rounded-full text-base font-semibold transition-colors">
                                        Cancelar inscrição
                                    </button>
                                </form>
                            @endif
                        @elseif($demanda->estaAberta() && (!$demanda->vagas || $demanda->vagasDisponiveis() > 0))
                            <form method="POST" action="{{ route('inscricoes.store', $demanda->id) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-base font-medium text-ink mb-2">Mensagem para a ONG <span class="text-ink-2 font-normal text-sm">(opcional)</span></label>
                                    <textarea name="mensagem" rows="3" placeholder="Conte por que você quer participar..."
                                        class="w-full rounded-xl border border-border px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all resize-none"></textarea>
                                </div>
                                <button type="submit"
                                    class="w-full bg-primary hover:bg-primary-dark text-white py-3 rounded-full text-base font-semibold transition-colors">
                                    Quero me inscrever
                                </button>
                            </form>
                        @else
                            <p class="text-base text-ink-2 text-center py-4">Esta vaga não está recebendo novas inscrições no momento.</p>
                        @endif
                    @else
                        <p class="text-base text-ink-2 text-center py-2">Apenas voluntários podem se inscrever nas vagas.</p>
                    @endif
                @endauth
                @guest
                    <p class="text-base text-ink-2 mb-4 text-center">Entre na sua conta para se inscrever nesta vaga.</p>
                    <a href="{{ route('login') }}"
                       class="block w-full text-center bg-primary hover:bg-primary-dark text-white py-3 rounded-full text-base font-semibold transition-colors">
                        Entrar para me inscrever
                    </a>
                    <a href="{{ route('registro') }}"
                       class="block w-full text-center border border-border hover:border-ink-2 text-ink py-3 rounded-full text-base font-semibold transition-colors mt-3">
                        Criar conta gratuita
                    </a>
                @endguest
            </div>

            {{-- Sobre a ONG --}}
            <div class="bg-surface rounded-2xl border border-border/60 p-6">
                <h2 class="text-lg font-semibold text-ink mb-3">Sobre a ONG</h2>
                <p class="font-semibold text-ink text-base">{{ $demanda->ong->razao_social }}</p>
                @if($demanda->ong->cidade)
                    <p class="text-sm text-ink-2 mt-1">{{ $demanda->ong->cidade }}{{ $demanda->ong->uf ? ', ' . $demanda->ong->uf : '' }}</p>
                @endif
                @if($demanda->ong->descricao_missao)
                    <p class="text-base text-ink-2 mt-3 leading-relaxed line-clamp-3">{{ $demanda->ong->descricao_missao }}</p>
                @endif
                <a href="{{ route('ongs.show', $demanda->ong->id) }}"
                   class="inline-block mt-4 text-base text-primary hover:text-primary-dark font-semibold transition-colors">
                    Ver perfil da ONG →
                </a>
            </div>

        </div>
    </div>
</div>

@endsection
