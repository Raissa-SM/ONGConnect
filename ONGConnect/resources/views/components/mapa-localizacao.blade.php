@props([
    'lat'      => null,
    'lng'      => null,
    'mapId'    => 'mapa-loc',
])

@pushOnce('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endPushOnce

<div class="space-y-3">

    {{-- Busca de endereço --}}
    <div class="flex gap-2">
        <input
            type="text"
            id="{{ $mapId }}-search"
            placeholder="Buscar endereço ou cidade..."
            class="flex-1 rounded-xl border border-border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all bg-white"
        >
        <button
            type="button"
            id="{{ $mapId }}-btn"
            class="bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-xl text-sm font-medium transition-colors whitespace-nowrap">
            Buscar
        </button>
    </div>

    {{-- Status da busca --}}
    <p id="{{ $mapId }}-status" class="text-xs text-ink-2 min-h-[1rem]"></p>

    {{-- isolation:isolate cria novo stacking context — evita que os z-indexes internos do Leaflet vazem sobre a navbar sticky --}}
    <div style="position: relative; isolation: isolate;">
        <div
            id="{{ $mapId }}"
            class="rounded-2xl overflow-hidden border border-border/60 shadow-sm"
            style="height: 340px;"
        ></div>
    </div>

    <p class="text-xs text-ink-2">
        Clique no mapa ou arraste o marcador para ajustar a posição exata.
    </p>

    {{-- Coordenadas exibidas --}}
    <div id="{{ $mapId }}-coords" class="text-xs text-ink-2 font-mono">
        @if($lat && $lng)
            Localização atual: {{ number_format($lat, 5) }}, {{ number_format($lng, 5) }}
        @else
            Nenhuma localização definida — busque um endereço ou clique no mapa.
        @endif
    </div>

    {{-- Inputs hidden enviados no formulário --}}
    <input type="hidden" name="latitude"  id="{{ $mapId }}-lat" value="{{ $lat }}">
    <input type="hidden" name="longitude" id="{{ $mapId }}-lng" value="{{ $lng }}">

</div>

@pushOnce('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endPushOnce

@push('scripts')
<script>
(function () {
    const ID       = @json($mapId);
    const initLat  = {{ $lat ?? 'null' }};
    const initLng  = {{ $lng ?? 'null' }};
    const fallLat  = -27.2138;   // Rio do Sul, SC
    const fallLng  = -49.6438;

    let leafMap, pin;
    let searchTimeout;

    /* ── bootstrap ───────────────────────────────────────── */
    function init() {
        const lat  = initLat ?? fallLat;
        const lng  = initLng ?? fallLng;
        const zoom = initLat ? 15 : 11;

        leafMap = L.map(ID).setView([lat, lng], zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(leafMap);

        if (initLat && initLng) {
            colocarPin(initLat, initLng, false);
            reverseGeocode(initLat, initLng); // preenche campo de busca na carga inicial
        }

        leafMap.on('click', e => {
            colocarPin(e.latlng.lat, e.latlng.lng, true);
        });

        /* busca ao digitar (debounce 600ms) */
        const searchInput = document.getElementById(ID + '-search');
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (searchInput.value.trim().length >= 4) buscar();
            }, 600);
        });
        searchInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); buscar(); }
        });

        document.getElementById(ID + '-btn').addEventListener('click', buscar);
    }

    /* ── coloca/move o marcador ──────────────────────────── */
    function colocarPin(lat, lng, moverView) {
        if (pin) {
            pin.setLatLng([lat, lng]);
        } else {
            pin = L.marker([lat, lng], { draggable: true }).addTo(leafMap);
            pin.on('dragend', e => {
                const p = e.target.getLatLng();
                salvarCoords(p.lat, p.lng);
                reverseGeocode(p.lat, p.lng);
            });
        }
        salvarCoords(lat, lng);
        if (moverView) leafMap.setView([lat, lng], 16);
    }

    /* ── salva nos inputs ocultos e exibe ────────────────── */
    function salvarCoords(lat, lng) {
        document.getElementById(ID + '-lat').value = lat.toFixed(6);
        document.getElementById(ID + '-lng').value = lng.toFixed(6);
        document.getElementById(ID + '-coords').textContent =
            'Localização: ' + lat.toFixed(5) + ', ' + lng.toFixed(5);
    }

    /* ── geocode (endereço → coords) ─────────────────────── */
    function buscar() {
        const q = document.getElementById(ID + '-search').value.trim();
        if (!q) return;

        setStatus('Buscando…');

        fetch('https://nominatim.openstreetmap.org/search?' + new URLSearchParams({
            format: 'json',
            q,
            limit: 1,
            countrycodes: 'br',
            addressdetails: 1,
        }), { headers: { 'Accept-Language': 'pt-BR,pt;q=0.9' } })
        .then(r => r.json())
        .then(data => {
            if (!data.length) { setStatus('Endereço não encontrado. Tente algo mais específico.'); return; }

            const r   = data[0];
            const lat = parseFloat(r.lat);
            const lng = parseFloat(r.lon);

            setStatus('Encontrado: ' + r.display_name.substring(0, 70) + (r.display_name.length > 70 ? '…' : ''));
            colocarPin(lat, lng, true);
            preencherCidadeUF(r.address ?? {});
        })
        .catch(() => setStatus('Erro de conexão ao buscar endereço.'));
    }

    /* ── reverse geocode (coords → endereço) ─────────────── */
    function reverseGeocode(lat, lng) {
        fetch('https://nominatim.openstreetmap.org/reverse?' + new URLSearchParams({
            format: 'json',
            lat,
            lon: lng,
            addressdetails: 1,
        }), { headers: { 'Accept-Language': 'pt-BR,pt;q=0.9' } })
        .then(r => r.json())
        .then(data => {
            if (!data.address) return;
            const display = data.display_name ?? '';
            document.getElementById(ID + '-search').value = display.substring(0, 100);
            setStatus('');
            preencherCidadeUF(data.address);
        })
        .catch(() => {});
    }

    /* ── preenche cidade/UF do formulário pai ─────────────── */
    function preencherCidadeUF(address) {
        const form = document.getElementById(ID).closest('form');
        if (!form) return;

        const cidade = address.city
            || address.town
            || address.village
            || address.municipality
            || address.county
            || '';

        /* state_code vem como "BR-SC" ou similar */
        let uf = address.state_code ?? address.ISO3166_2_lvl4 ?? '';
        uf = uf.replace(/^BR-/, '').substring(0, 2).toUpperCase();

        const cidadeField = form.querySelector('input[name="cidade"]');
        const ufField     = form.querySelector('input[name="uf"]');

        if (cidadeField && cidade) cidadeField.value = cidade;
        if (ufField && uf.length === 2) ufField.value = uf;
    }

    /* ── helper ──────────────────────────────────────────── */
    function setStatus(msg) {
        document.getElementById(ID + '-status').textContent = msg;
    }

    /* ── inicia depois que Leaflet estiver carregado ─────── */
    function waitLeaflet() {
        if (typeof L !== 'undefined') { init(); }
        else { setTimeout(waitLeaflet, 50); }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', waitLeaflet);
    } else {
        waitLeaflet();
    }
})();
</script>
@endpush
