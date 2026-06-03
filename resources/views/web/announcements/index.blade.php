@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid">
        <article class="page-hero section-card">
            <div>
                <small>Pengumuman Desa</small>
                <h1>Pengumuman Desa</h1>
                <p>Informasi resmi terbaru dari {{ $village?->name ?? 'pemerintah desa' }}.</p>
            </div>
            <div class="page-hero__actions">
                <form method="GET" action="{{ route('pengumuman') }}" class="page-hero-filter page-hero-filter--search-type">
                    <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari judul atau isi pengumuman...">
                    <select name="type">
                        <option value="all" @selected(($selectedType ?? 'all') === 'all')>Semua Tipe</option>
                        @foreach (($typeOptions ?? \App\Models\Announcement::typeOptions()) as $key => $meta)
                            <option value="{{ $key }}" @selected(($selectedType ?? 'all') === $key)>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                    <button type="submit">Cari</button>
                    <a href="{{ route('pengumuman') }}">Reset</a>
                </form>
            </div>
        </article>
    </div>
</section>

<section class="section-wrap section-wrap--last">
    <div class="container-grid">
        @if ($announcements->isEmpty())
            <article class="section-card announcement-page-empty">
                <h3>Belum ada pengumuman</h3>
                <p>Pengumuman resmi desa yang dipublikasikan admin akan muncul di halaman ini.</p>
            </article>
        @else
            <div class="announcement-grid announcement-grid--page">
                @foreach ($announcements as $item)
                    @php
                        $images = $item->relationLoaded('images')
                            ? $item->images->take(3)->map(fn ($image) => ['url' => $image->image_url])->values()->all()
                            : [];
                        $hasCoordinates = $item->latitude !== null && $item->longitude !== null;
                        $hasMapUrl = filled($item->map_url);
                        $hasLocation = $hasCoordinates || $hasMapUrl;
                        $locationSummary = filled($item->location_name)
                            ? \Illuminate\Support\Str::limit(trim((string) $item->location_name), 46)
                            : ($hasCoordinates
                                ? ('Lat '.number_format((float) $item->latitude, 5, '.', '').', Lng '.number_format((float) $item->longitude, 5, '.', ''))
                                : 'Lihat titik pada peta');
                        $detailPayload = [
                            'title' => $item->title,
                            'typeLabel' => $item->typeLabel(),
                            'typeColor' => $item->typeColor(),
                            'typeIcon' => $item->typeIcon(),
                            'content' => trim(strip_tags((string) $item->content)) !== '' ? trim(strip_tags((string) $item->content)) : 'Isi pengumuman belum tersedia.',
                            'publishedAt' => $item->published_at?->translatedFormat('d M Y H:i') ?? $item->created_at?->translatedFormat('d M Y H:i'),
                            'referenceUrl' => $item->reference_url,
                            'images' => $images,
                            'attachmentUrl' => $item->attachment_url,
                            'attachmentName' => $item->attachment_name ?: ($item->attachment_path ? basename((string) $item->attachment_path) : null),
                            'locationName' => $item->location_name,
                            'latitude' => $item->latitude,
                            'longitude' => $item->longitude,
                            'mapUrl' => $hasMapUrl ? $item->map_url : null,
                            'hasLocation' => $hasLocation,
                        ];
                    @endphp
                    <article
                        class="section-card announcement-card announcement-card--page announcement-card--interactive"
                        role="button"
                        tabindex="0"
                        data-announcement-detail='@json($detailPayload)'>
                        <div class="announcement-card__cover announcement-card__cover--side">
                            @if (!empty($images[0]['url']))
                                <img src="{{ $images[0]['url'] }}" alt="{{ $item->title }}" loading="lazy">
                            @else
                                <div class="announcement-card__cover-fallback">
                                    <div class="announcement-card__fallback-icon" aria-hidden="true">📢</div>
                                </div>
                            @endif
                        </div>
                        <div class="announcement-card__body">
                            <span class="announcement-card__type" style="background: {{ $item->typeColor() }}">
                                <i class="{{ $item->typeIcon() }}" aria-hidden="true"></i> {{ $item->typeLabel() }}
                            </span>
                            <h3>{{ $item->title }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags((string) $item->content), 220) }}</p>
                            <div class="announcement-card__meta">
                                <small>
                                    Dipublikasikan:
                                    {{ $item->published_at?->translatedFormat('d M Y H:i') ?? $item->created_at?->translatedFormat('d M Y H:i') }}
                                </small>
                                @if ($hasLocation)
                                    <small>
                                        Lokasi:
                                        {{ $locationSummary }}
                                    </small>
                                @endif
                                @if ($item->reference_url)
                                    <a href="{{ $item->reference_url }}" target="_blank" rel="noopener">Buka Referensi</a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div style="margin-top: 1rem;">
                {{ $announcements->links() }}
            </div>

            <div id="announcement-detail-modal" class="announcement-modal" hidden>
                <div class="announcement-modal__backdrop" data-announcement-close></div>
                <div class="announcement-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="announcement-modal-title">
                    <button type="button" class="announcement-modal__close" data-announcement-close aria-label="Tutup detail">&times;</button>
                    <span id="announcement-modal-type" class="announcement-modal__type"></span>
                    <h3 id="announcement-modal-title"></h3>
                    <small id="announcement-modal-date"></small>
                    <div id="announcement-modal-gallery" class="announcement-modal__gallery" hidden></div>
                    <div class="announcement-modal__content" id="announcement-modal-content"></div>
                    <div id="announcement-modal-map-wrap" class="announcement-modal__map-wrap" hidden>
                        <h4>Lokasi Pengumuman</h4>
                        <div class="announcement-modal__map-meta" id="announcement-modal-location"></div>
                        <iframe id="announcement-modal-map" title="Peta lokasi pengumuman" loading="lazy"></iframe>
                    </div>
                    <div class="announcement-modal__actions" hidden>
                        <a id="announcement-modal-attachment" target="_blank" rel="noopener" download hidden>Unduh Lampiran</a>
                        <a id="announcement-modal-route" target="_blank" rel="noopener" hidden>Buka Rute</a>
                        <a id="announcement-modal-reference" target="_blank" rel="noopener" hidden>Buka Referensi</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<script>
    (function () {
        const modal = document.getElementById('announcement-detail-modal');
        if (!modal) return;

        const typeEl = document.getElementById('announcement-modal-type');
        const titleEl = document.getElementById('announcement-modal-title');
        const dateEl = document.getElementById('announcement-modal-date');
        const contentEl = document.getElementById('announcement-modal-content');
        const galleryEl = document.getElementById('announcement-modal-gallery');
        const mapWrapEl = document.getElementById('announcement-modal-map-wrap');
        const mapLocationEl = document.getElementById('announcement-modal-location');
        const mapIframeEl = document.getElementById('announcement-modal-map');
        const refEl = document.getElementById('announcement-modal-reference');
        const attachmentEl = document.getElementById('announcement-modal-attachment');
        const routeEl = document.getElementById('announcement-modal-route');
        const actionsEl = modal.querySelector('.announcement-modal__actions');
        const cards = document.querySelectorAll('.announcement-card--interactive');

        const toNumber = (value) => {
            const n = Number(value);
            return Number.isFinite(n) ? n : null;
        };

        const hasText = (value) => typeof value === 'string' && value.trim() !== '';

        const buildOsmEmbedUrl = (lat, lng) => {
            const delta = 0.008;
            const minLng = (lng - delta).toFixed(6);
            const minLat = (lat - delta).toFixed(6);
            const maxLng = (lng + delta).toFixed(6);
            const maxLat = (lat + delta).toFixed(6);
            return `https://www.openstreetmap.org/export/embed.html?bbox=${minLng}%2C${minLat}%2C${maxLng}%2C${maxLat}&layer=mapnik&marker=${lat.toFixed(6)}%2C${lng.toFixed(6)}`;
        };

        const closeModal = () => {
            modal.setAttribute('hidden', 'hidden');
            document.body.classList.remove('is-modal-open');
            mapIframeEl.removeAttribute('src');
        };

        const openModal = (payload) => {
            galleryEl.setAttribute('hidden', 'hidden');
            galleryEl.innerHTML = '';
            mapWrapEl.setAttribute('hidden', 'hidden');
            mapLocationEl.textContent = '';
            mapIframeEl.removeAttribute('src');
            actionsEl.setAttribute('hidden', 'hidden');
            attachmentEl.setAttribute('hidden', 'hidden');
            attachmentEl.removeAttribute('href');
            attachmentEl.textContent = 'Unduh Lampiran';
            routeEl.setAttribute('hidden', 'hidden');
            routeEl.removeAttribute('href');
            routeEl.textContent = 'Buka Rute';
            refEl.setAttribute('hidden', 'hidden');
            refEl.removeAttribute('href');

            typeEl.textContent = payload.typeLabel || 'Pengumuman';
            typeEl.style.background = payload.typeColor || '#0f5e9f';
            titleEl.textContent = payload.title || '-';
            dateEl.textContent = payload.publishedAt ? `Dipublikasikan: ${payload.publishedAt}` : '';
            contentEl.textContent = payload.content || '-';
            const images = Array.isArray(payload.images) ? payload.images.filter((item) => item && item.url) : [];
            if (images.length > 0) {
                images.forEach((image) => {
                    const img = document.createElement('img');
                    img.src = image.url;
                    img.loading = 'lazy';
                    galleryEl.appendChild(img);
                });
                galleryEl.removeAttribute('hidden');
            }

            const latitude = toNumber(payload.latitude);
            const longitude = toNumber(payload.longitude);
            const hasCoordinates = latitude !== null && longitude !== null;
            const mapUrl = hasText(payload.mapUrl) ? payload.mapUrl.trim() : null;
            const hasLocation = Boolean(payload.hasLocation);

            if (hasLocation && hasCoordinates) {
                mapWrapEl.removeAttribute('hidden');
                mapLocationEl.textContent = payload.locationName || `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
                mapIframeEl.src = buildOsmEmbedUrl(latitude, longitude);
                routeEl.removeAttribute('hidden');
                routeEl.href = `https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}`;
                routeEl.textContent = 'Buka Rute';
            } else {
                if (hasLocation && mapUrl) {
                    routeEl.removeAttribute('hidden');
                    routeEl.href = mapUrl;
                    routeEl.textContent = 'Buka Lokasi';
                }
            }

            if (hasText(payload.attachmentUrl) && payload.attachmentUrl.trim() !== '#') {
                attachmentEl.removeAttribute('hidden');
                attachmentEl.href = payload.attachmentUrl.trim();
                attachmentEl.textContent = payload.attachmentName
                    ? `Unduh Lampiran (${payload.attachmentName})`
                    : 'Unduh Lampiran';
            }

            if (hasText(payload.referenceUrl) && payload.referenceUrl.trim() !== '#') {
                refEl.removeAttribute('hidden');
                refEl.href = payload.referenceUrl.trim();
            }

            const hasVisibleAction = [attachmentEl, routeEl, refEl].some((el) => !el.hasAttribute('hidden'));
            if (hasVisibleAction) {
                actionsEl.removeAttribute('hidden');
            } else {
                actionsEl.setAttribute('hidden', 'hidden');
            }

            modal.removeAttribute('hidden');
            document.body.classList.add('is-modal-open');
        };

        cards.forEach((card) => {
            const trigger = () => {
                const raw = card.getAttribute('data-announcement-detail');
                if (!raw) return;
                try {
                    openModal(JSON.parse(raw));
                } catch (_) {}
            };

            card.addEventListener('click', trigger);
            card.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    trigger();
                }
            });
        });

        modal.querySelectorAll('[data-announcement-close]').forEach((el) => {
            el.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.hasAttribute('hidden')) {
                closeModal();
            }
        });
    })();
</script>
@endsection
