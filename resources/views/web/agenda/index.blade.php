@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid">
        <h1 style="font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 800; margin: 0;">Agenda Desa</h1>
        <p style="margin-top: .45rem; color: #4b5563;">Informasi kegiatan desa terjadwal yang dapat diikuti masyarakat.</p>
    </div>
</section>
<section class="section-wrap">
    <div class="container-grid">
        <article class="section-card agenda-map-card">
            <div class="agenda-map-card__head">
                <h2>Peta Lokasi Agenda</h2>
                <p>Klik badge agenda di peta untuk melihat detail dan rute Google Maps.</p>
            </div>
            <div id="agenda-map" class="agenda-map-canvas"></div>
            @if ($mapAgendas->isEmpty())
                <p class="agenda-map-card__empty">Belum ada agenda dengan koordinat lokasi. Tambahkan latitude/longitude di backend agenda.</p>
            @endif
        </article>
    </div>
</section>
<section class="section-wrap">
    <div class="container-grid">
        <article class="section-card agenda-calendar-card">
            <div class="agenda-calendar-card__head">
                <h2>Kalender Agenda {{ $activeMonth->translatedFormat('F Y') }}</h2>
                <div class="agenda-calendar-card__nav">
                    <a href="{{ route('agenda', ['month' => $monthPrev, 'status' => $status, 'q' => $keyword, 'day' => null]) }}">Bulan Sebelumnya</a>
                    <a href="{{ route('agenda', ['month' => $monthNext, 'status' => $status, 'q' => $keyword, 'day' => null]) }}">Bulan Berikutnya</a>
                </div>
            </div>

            <div class="agenda-calendar-grid agenda-calendar-grid--labels">
                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $label)
                    <div>{{ $label }}</div>
                @endforeach
            </div>

            @foreach ($calendarWeeks as $week)
                <div class="agenda-calendar-grid">
                    @foreach ($week as $day)
                        <a
                            href="{{ route('agenda', ['month' => $activeMonth->format('Y-m'), 'status' => $status, 'q' => $keyword, 'day' => $day['date']->toDateString()]) }}"
                            class="agenda-calendar-cell {{ !$day['in_month'] ? 'is-muted' : '' }} {{ $day['count'] > 0 ? 'has-agenda' : '' }} {{ $activeDay && $activeDay->isSameDay($day['date']) ? 'is-selected' : '' }}"
                        >
                            <span>{{ $day['date']->day }}</span>
                            @if ($day['count'] > 0)
                                <small>{{ $day['count'] }} agenda</small>
                            @else
                                <small>-</small>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endforeach
        </article>
    </div>
</section>



<section class="section-wrap section-wrap--last">
    <div class="container-grid">
        <article class="section-card agenda-filter-card">
            <form method="GET" action="{{ route('agenda') }}" class="agenda-filter-form">
                <input type="hidden" name="month" value="{{ $activeMonth->format('Y-m') }}">
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari agenda...">
                <select name="status">
                    <option value="all" @selected($status === 'all')>Semua Status</option>
                    <option value="upcoming" @selected($status === 'upcoming')>Akan Datang</option>
                    <option value="ongoing" @selected($status === 'ongoing')>Berlangsung</option>
                    <option value="done" @selected($status === 'done')>Selesai</option>
                </select>
                <button type="submit">Filter Agenda</button>
                <a href="{{ route('agenda', ['month' => $activeMonth->format('Y-m')]) }}">Reset</a>
            </form>
        </article>

        @if ($agendas->isEmpty())
            <article class="section-card" style="margin-top: .9rem; padding: 1rem;">
                <p style="margin: 0; color: #6b7280;">Belum ada agenda yang sesuai filter.</p>
            </article>
        @else
            <div class="agenda-list agenda-list--grid">
                @foreach ($agendas as $item)
                    @php
                        $now = now();
                        $agendaStatus = 'Akan Datang';
                        if ($item->start_at && $item->start_at <= $now && (!$item->end_at || $item->end_at >= $now)) {
                            $agendaStatus = 'Berlangsung';
                        } elseif (($item->end_at && $item->end_at < $now) || ($item->start_at && !$item->end_at && $item->start_at < $now)) {
                            $agendaStatus = 'Selesai';
                        }
                    @endphp
                    <article class="section-card agenda-card">
                        @if ($item->poster_url)
                            <div class="agenda-card__poster">
                                <img src="{{ $item->poster_url }}" alt="{{ $item->title }}" loading="lazy" decoding="async">
                            </div>
                        @endif
                        <div class="agenda-card__header">
                            <span class="agenda-card__badge">{{ $agendaStatus }}</span>
                            <h2><a href="{{ route('agenda.show', $item) }}">{{ $item->title }}</a></h2>
                        </div>
                        <div class="agenda-card__meta">
                            <span>
                                {{ $item->start_at?->translatedFormat('d M Y H:i') ?? '-' }}
                                @if ($item->end_at)
                                    - {{ $item->end_at->translatedFormat('d M Y H:i') }}
                                @endif
                            </span>
                            <span>{{ $item->location ?: 'Lokasi akan diinformasikan' }}</span>
                        </div>
                    </article>
                @endforeach
            </div>

            <div style="margin-top: 1rem;">
                {{ $agendas->links() }}
            </div>
        @endif
    </div>
</section>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    (function () {
        const mapEl = document.getElementById('agenda-map');
        if (!mapEl || typeof L === 'undefined') return;

        const agendaItems = @json($mapAgendaItems);

        const fallbackLat = {{ $village?->latitude ?? -8.6512299 }};
        const fallbackLng = {{ $village?->longitude ?? 115.2148033 }};
        const hasItems = Array.isArray(agendaItems) && agendaItems.length > 0;

        const map = L.map('agenda-map').setView(
            hasItems ? [agendaItems[0].latitude, agendaItems[0].longitude] : [fallbackLat, fallbackLng],
            hasItems ? 13 : 12
        );

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const bounds = [];
        const detailUrlPattern = @json(route('agenda.show', '__AGENDA_ID__'));

        agendaItems.forEach((item) => {
            if (!item.latitude || !item.longitude) return;

            const markerIcon = L.divIcon({
                className: 'agenda-map-marker-wrap',
                html: `
                    <div class="agenda-map-marker">
                        <span class="agenda-map-marker__thumb" style="${item.poster_url ? `background-image:url('${item.poster_url}')` : ''}"></span>
                        <strong>${item.title}</strong>
                    </div>
                `,
                iconSize: [170, 38],
                iconAnchor: [85, 38],
            });

            const googleRoute = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(item.latitude + ',' + item.longitude)}`;
            const detailUrl = detailUrlPattern.replace('__AGENDA_ID__', String(item.id));
            const popupHtml = `
                <article class="agenda-map-popup">
                    ${item.poster_url ? `<img src="${item.poster_url}" alt="${item.title}" loading="lazy" decoding="async">` : ''}
                    <h3>${item.title}</h3>
                    <p>${item.start_at || '-'}</p>
                    <p>${item.location || 'Lokasi akan diinformasikan'}</p>
                    <div class="agenda-map-popup__actions">
                        <a href="${detailUrl}">Detail Agenda</a>
                        <a href="${googleRoute}" target="_blank" rel="noopener">Buka Rute Google Maps</a>
                    </div>
                </article>
            `;

            const marker = L.marker([item.latitude, item.longitude], { icon: markerIcon }).addTo(map);
            marker.bindPopup(popupHtml, { maxWidth: 280 });
            bounds.push([item.latitude, item.longitude]);
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [22, 22] });
        }
    })();
</script>
@endsection
