@props([
    'lat'   => null,
    'lng'   => null,
    'mapId' => 'mapa-view',
    'label' => null,
])

@if($lat && $lng)

@pushOnce('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endPushOnce

<div style="position: relative; isolation: isolate;">
    <div
        id="{{ $mapId }}"
        class="rounded-2xl overflow-hidden border border-border/60 shadow-sm"
        style="height: 240px;"
    ></div>
</div>

@pushOnce('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endPushOnce

@push('scripts')
<script>
(function () {
    const lat = {{ $lat }};
    const lng = {{ $lng }};

    function init() {
        const map = L.map(@json($mapId), { zoomControl: true, dragging: true, scrollWheelZoom: false })
                     .setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(map);

        @if($label)
        L.marker([lat, lng]).addTo(map).bindPopup(@json($label)).openPopup();
        @else
        L.marker([lat, lng]).addTo(map);
        @endif
    }

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

@endif
