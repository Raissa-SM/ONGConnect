@extends('layouts.app')
@section('title', 'Painel — ONGConnect')
@section('content')

<div class="max-w-6xl mx-auto px-6 py-12">

    {{-- Cabeçalho --}}
    <div class="flex items-start justify-between mb-8 gap-4 flex-wrap">
        <div>
            <p class="text-base text-ink-2 mb-1">Bem-vindo de volta</p>
            <h1 class="text-3xl font-bold tracking-tight text-ink">{{ auth()->user()->name }}</h1>
            @if($voluntario->cidade)
                <p class="text-ink-2 mt-1 text-base">{{ $voluntario->cidade }}{{ $voluntario->uf ? ', ' . $voluntario->uf : '' }}</p>
            @endif
        </div>
        <div class="flex gap-3 flex-wrap">
            <a href="{{ route('match.sugestoes') }}"
               class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-full text-base font-semibold transition-colors">
                Vagas para você
            </a>
            <a href="{{ route('perfil.voluntario') }}"
               class="border border-border hover:border-ink-2 text-ink px-6 py-2.5 rounded-full text-base font-medium transition-colors">
                Editar perfil
            </a>
        </div>
    </div>

    {{-- Números --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-surface rounded-2xl border border-border/60 p-5">
            <p class="text-3xl font-bold text-ink">{{ $inscricoes->count() }}</p>
            <p class="text-base text-ink-2 mt-1">Inscrições feitas</p>
        </div>
        <div class="bg-surface rounded-2xl border border-border/60 p-5">
            <p class="text-3xl font-bold text-green-600">{{ $porStatus['aceita'] ?? 0 }}</p>
            <p class="text-base text-ink-2 mt-1">Aceitas</p>
        </div>
        <div class="bg-surface rounded-2xl border border-border/60 p-5">
            <p class="text-3xl font-bold text-primary">{{ $porStatus['concluida'] ?? 0 }}</p>
            <p class="text-base text-ink-2 mt-1">Concluídas</p>
        </div>
        <div class="bg-surface rounded-2xl border border-border/60 p-5">
            @if($mediaAvaliacoes !== null)
                <p class="text-3xl font-bold text-amber-500">{{ number_format($mediaAvaliacoes, 1) }}</p>
                <p class="text-base text-ink-2 mt-1">Sua nota média</p>
            @else
                <p class="text-3xl font-bold text-ink-2">—</p>
                <p class="text-base text-ink-2 mt-1">Nota (após 3 avaliações)</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Próximas atividades --}}
        <div class="bg-surface rounded-2xl border border-border/60 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-ink">Próximas atividades</h2>
            </div>
            @if($proximas->isEmpty())
                <p class="text-base text-ink-2 py-4 text-center">Nenhuma atividade marcada por enquanto.</p>
            @else
                <div class="space-y-3">
                    @foreach($proximas as $i)
                        @php $emAndamento = $i->demanda->eventoEmAndamento(); @endphp
                        <div class="flex items-start gap-4 py-3 border-b border-border/40 last:border-0">
                            <div class="text-center {{ $emAndamento ? 'bg-success/10 text-success' : 'bg-primary/10 text-primary' }} rounded-xl px-3 py-2 shrink-0">
                                <p class="text-sm font-medium">{{ $i->demanda->evento_inicio->format('M') }}</p>
                                <p class="text-xl font-bold leading-none">{{ $i->demanda->evento_inicio->format('d') }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-ink text-base truncate">{{ $i->demanda->titulo }}</p>
                                <a href="{{ route('ongs.show', $i->demanda->ong->id) }}" class="text-sm text-ink-2 hover:text-primary transition-colors mt-0.5 inline-block">{{ $i->demanda->ong->razao_social }}</a>
                                @if($emAndamento)
                                    <p class="text-sm font-semibold text-success mt-0.5">● Acontecendo agora</p>
                                @else
                                    <p class="text-sm text-ink-2 mt-0.5">{{ $i->demanda->evento_inicio->format('d/m/Y \à\s H:i') }}</p>
                                @endif
                                @if($i->demanda->cidade)
                                    <p class="text-sm text-ink-2">{{ $i->demanda->cidade }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Últimas inscrições --}}
        <div class="bg-surface rounded-2xl border border-border/60 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-ink">Últimas inscrições</h2>
                <a href="{{ route('inscricoes.minhas') }}" class="text-sm text-primary hover:text-primary-dark transition-colors font-medium">Ver todas →</a>
            </div>
            @if($ultimas->isEmpty())
                <p class="text-base text-ink-2 py-4 text-center">Você ainda não se inscreveu em nenhuma vaga.</p>
                <a href="{{ route('demandas.index') }}" class="block text-center mt-2 text-base text-primary hover:text-primary-dark transition-colors font-medium">Explorar vagas →</a>
            @else
                <div class="space-y-3">
                    @foreach($ultimas as $i)
                        <div class="flex items-center justify-between py-2.5 border-b border-border/40 last:border-0 gap-4">
                            <div class="min-w-0">
                                <p class="font-semibold text-ink text-base truncate">{{ $i->demanda->titulo }}</p>
                                <a href="{{ route('ongs.show', $i->demanda->ong->id) }}" class="text-sm text-ink-2 hover:text-primary transition-colors mt-0.5 inline-block">{{ $i->demanda->ong->razao_social }}</a>
                            </div>
                            <span class="text-sm font-semibold px-3 py-1 rounded-full shrink-0 {{ $i->status->badgeClasses() }}">
                                {{ $i->status->label() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Aviso de perfil incompleto --}}
    @if(!$voluntario->aptoParaMatch())
        <div class="mt-6 bg-amber-50 border border-amber-200 rounded-2xl p-6 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <p class="text-lg font-semibold text-amber-900">Complete seu perfil para ver vagas para você</p>
                <p class="text-base text-amber-800 mt-1">Informe onde você mora no mapa e escolha pelo menos um assunto de interesse.</p>
            </div>
            <a href="{{ route('perfil.voluntario') }}"
               class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-full text-base font-semibold transition-colors shrink-0">
                Completar perfil
            </a>
        </div>
    @endif

</div>

@endsection
