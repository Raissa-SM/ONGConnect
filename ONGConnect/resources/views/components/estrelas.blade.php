@props([
    'nota'    => 0,      // 0–5
    'tamanho' => 'text-base',
])
@php $n = (int) round($nota); @endphp
<span class="{{ $tamanho }} leading-none whitespace-nowrap" role="img" aria-label="Nota {{ $nota }} de 5">
    <span class="text-amber-500">{{ str_repeat('★', $n) }}</span><span class="text-border">{{ str_repeat('★', 5 - $n) }}</span>
</span>
