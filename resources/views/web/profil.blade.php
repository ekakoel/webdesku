@extends('web.web')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">

<section class="section-wrap">
    <div class="container-grid">
        <div class="section-card" style="padding: .6rem;">
            @if ($village?->latitude && $village?->longitude)
                <div id="village-map" style="height: clamp(260px, 38vw, 440px); border-radius: 14px; overflow: hidden;"></div>
                <p style="margin: .6rem .6rem 0; color: #64748b; font-size: .9rem;">
                    Peta wilayah desa berbasis OpenStreetMap.
                </p>
            @else
                <div style="padding: 1rem;">
                    <p style="color: #64748b;">Koordinat desa belum tersedia sehingga peta belum dapat ditampilkan.</p>
                </div>
            @endif
        </div>
    </div>
</section>

<section class="section-wrap">
    <div class="container-grid">
        <div id="gambaran-umum" class="section-card" style="padding: 1.2rem 1.25rem;">
            <h1 style="font-size: clamp(1.35rem, 2.5vw, 1.9rem); margin: 0;">Profil {{ $village?->name ?? 'Desa' }}</h1>
            <p style="margin-top: .65rem; color: #475569;">
                {{ $village?->description ?? 'Profil desa belum tersedia.' }}
            </p>
            @if ($village?->address)
                <p style="margin-top: .5rem; color: #64748b; font-size: .95rem;">
                    {{ $village->address }}
                </p>
            @endif
        </div>
    </div>
</section>

<section class="section-wrap">
    <div class="container-grid split">
        <article id="sejarah-desa" class="section-card greeting-card">
            <h2>Sejarah Desa</h2>
            <p>{{ $village?->history ?? 'Data sejarah desa belum tersedia.' }}</p>
        </article>
        <article class="section-card greeting-card">
            <h2>Sambutan Kepala Desa</h2>
            <p>{{ $village?->head_greeting ?? 'Sambutan kepala desa belum tersedia.' }}</p>
            <div class="greeting-card__person">
                <strong>{{ $village?->head_name ?? '-' }}</strong>
                <small>Kepala Desa</small>
            </div>
        </article>
    </div>
</section>

<section class="section-wrap">
    <div id="visi-misi" class="container-grid split">
        <article class="section-card budget-card">
            <h2>Visi Desa</h2>
            <p style="margin-top: .8rem; color: #475569;">
                {{ $village?->vision ?? 'Visi desa belum tersedia.' }}
            </p>
        </article>

        <article class="section-card budget-card">
            <h2>Misi Desa</h2>
            @if (count($missions) > 0)
                <ol style="margin: .8rem 0 0 1rem; color: #475569; display: grid; gap: .4rem;">
                    @foreach ($missions as $mission)
                        <li>{{ $mission }}</li>
                    @endforeach
                </ol>
            @else
                <p style="margin-top: .8rem; color: #475569;">Misi desa belum tersedia.</p>
            @endif
        </article>
    </div>
</section>

<section class="section-wrap">
    <div id="susunan-organisasi" class="container-grid">
        <article class="section-card budget-card">
            <h2>Susunan Organisasi</h2>
            <p style="margin-top: .6rem; color: #64748b;">
                Struktur organisasi pemerintah desa yang ditampilkan pada website ini dapat disesuaikan melalui panel admin aparatur desa.
            </p>
            @if ($officials->isEmpty())
                <p style="margin-top: .75rem; color: #475569;">Data susunan organisasi belum tersedia.</p>
            @else
                <div class="official-grid" style="margin-top: .8rem;">
                    @foreach ($officials as $official)
                        <article class="section-card official-card">
                            <div class="official-card__image" @if($official->photo_path) style="background-image: url('{{ \Illuminate\Support\Facades\Storage::url($official->photo_path) }}');" @endif>
                                @if (!$official->photo_path)
                                    <span>{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($official->name, 0, 2)) }}</span>
                                @endif
                            </div>
                            <div class="official-card__meta">
                                <h3>{{ $official->name }}</h3>
                                <p>{{ $official->position }}</p>
                                @if ($official->unit)
                                    <small>{{ $official->unit }}</small>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
            <p style="margin-top: .75rem;">
                <a href="https://www.danginpurikauh.denpasarkota.go.id/" target="_blank" rel="noopener" class="text-link">Referensi profil resmi desa</a>
            </p>
        </article>
    </div>
</section>

<section class="section-wrap">
    <div class="container-grid split">
        <article class="section-card budget-card">
            <h2>Statistik Desa</h2>
            <div class="stats-grid" style="margin-top: .8rem;">
                @foreach ($stats as $item)
                    <article class="section-card stat-card">
                        <h3>{{ $item['value'] }}</h3>
                        <p>{{ $item['label'] }}</p>
                    </article>
                @endforeach
            </div>
        </article>

        <article class="section-card budget-card">
            <h2>Informasi Wilayah & Kontak</h2>
            <div class="potensi-list">
                <div>
                    <h3>Kecamatan / Kota</h3>
                    <p>{{ $village?->district ?? '-' }} / {{ $village?->city ?? '-' }}</p>
                </div>
                <div>
                    <h3>Provinsi / Negara</h3>
                    <p>{{ $village?->province ?? '-' }} / {{ $village?->country ?? '-' }}</p>
                </div>
                <div>
                    <h3>Kode Pos</h3>
                    <p>{{ $village?->postal_code ?? '-' }}</p>
                </div>
                <div>
                    <h3>Telepon</h3>
                    <p>{{ $village?->phone ?? '-' }}</p>
                </div>
                <div>
                    <h3>Email</h3>
                    <p>{{ $village?->email ?? '-' }}</p>
                </div>
                <div>
                    <h3>Website</h3>
                    <p>
                        @if ($village?->website)
                            <a href="{{ $village->website }}" target="_blank" rel="noopener" class="text-link">{{ $village->website }}</a>
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </article>
    </div>
</section>

@if ($village?->latitude && $village?->longitude)
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lat = Number(@json($village->latitude));
            const lng = Number(@json($village->longitude));
            const map = L.map('village-map').setView([lat, lng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            L.marker([lat, lng])
                .addTo(map)
                .bindPopup(@json($village->name ?? 'Lokasi Desa'))
                .openPopup();

            const boundary = @json($village->boundary_geojson);
            if (boundary) {
                const geoLayer = L.geoJSON(boundary, {
                    style: {
                        color: '#dc2626',
                        weight: 3,
                        dashArray: '10 8',
                        fillColor: '#ef4444',
                        fillOpacity: 0.08
                    }
                }).addTo(map);
                map.fitBounds(geoLayer.getBounds(), { padding: [16, 16] });
            }
        });
    </script>
@endif
@endsection


