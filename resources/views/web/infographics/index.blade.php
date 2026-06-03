@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid page-section-stack">
        <article class="page-hero section-card">
            <div>
                <small>Infografis Desa</small>
                <h1>Infografis Desa</h1>
                <p>Data visual desa meliputi aset, penduduk, dan indikator penting lainnya.</p>
            </div>
            @if ($tab === 'aset')
                <div class="page-hero__actions">
                    <form method="GET" action="{{ route('infografis') }}" class="page-hero-filter page-hero-filter--search-type">
                        <input type="hidden" name="tab" value="aset">
                        <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari aset/UMKM/fasilitas...">
                        <select name="type">
                            <option value="all" @selected($type === 'all')>Semua Tipe</option>
                            @foreach ($typeOptions as $key => $option)
                                <option value="{{ $key }}" @selected($type === $key)>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        <button type="submit">Filter</button>
                        <a href="{{ route('infografis', ['tab' => 'aset']) }}">Reset</a>
                    </form>
                </div>
            @elseif ($tab === 'penduduk')
                <div class="page-hero__actions">
                    <form method="GET" action="{{ route('infografis') }}" class="page-hero-filter page-hero-filter--year">
                        <input type="hidden" name="tab" value="penduduk">
                        <label for="populationYear">Tahun Data</label>
                        <div>
                            <select id="populationYear" name="year">
                                <option value="">Semua Tahun</option>
                                @foreach (($populationYears ?? collect()) as $yearOption)
                                    <option value="{{ $yearOption }}" @selected((int) ($selectedPopulationYear ?? 0) === (int) $yearOption)>{{ $yearOption }}</option>
                                @endforeach
                            </select>
                            <button type="submit">Tampilkan</button>
                        </div>
                    </form>
                </div>
            @endif
        </article>

        <div class="infographic-tabs">
            <a href="{{ route('infografis', ['tab' => 'aset']) }}" class="{{ $tab === 'aset' ? 'is-active' : '' }}">Aset Desa</a>
            <a href="{{ route('infografis', ['tab' => 'penduduk']) }}" class="{{ $tab === 'penduduk' ? 'is-active' : '' }}">Penduduk</a>
            <a href="{{ route('infografis', ['tab' => 'lainnya']) }}" class="{{ $tab === 'lainnya' ? 'is-active' : '' }}">Lainnya</a>
        </div>

    </div>
</section>

@if ($tab === 'aset')
    <section class="section-wrap infographic-map-full">
        <article class="infographic-map-shell">
            <div class="container-grid">
                <div class="infographic-map-shell__head">
                    <h2>Peta Aset Desa</h2>
                    <p>Klik marker untuk melihat detail lokasi dan buka rute Google Maps.</p>
                </div>
            </div>
            <div class="container-grid">
                <article class="section-card infographic-map-card-shell">
                    <div class="infographic-map-stage">
                        <div id="asset-map" class="infographic-map infographic-map--full"></div>

                        <div class="map-search-box">
                            <label for="asset-map-search">Cari lokasi/aset</label>
                            <div class="map-search-box__row">
                                <input id="asset-map-search" type="text" placeholder="Contoh: Balai Banjar, Pasar..." autocomplete="off">
                                <button type="button" id="asset-map-search-clear" aria-label="Bersihkan pencarian">Reset</button>
                            </div>
                            <div id="asset-map-search-results" class="map-search-results" hidden></div>
                        </div>
                    </div>
                    <aside class="map-side-panel map-side-panel--bottom" id="asset-map-info-panel">
                        <div class="map-side-panel__head">
                            <div>
                                <h3>Ringkasan Aset</h3>
                                <p>Jumlah titik sesuai layer aktif.</p>
                            </div>
                        </div>
                        <div class="infographic-legend">
                            <span class="infographic-legend__total">
                                <i style="background:#0c3f7f"></i>
                                Total Aset: <strong id="asset-map-total">0</strong>
                            </span>
                            @foreach ($typeOptions as $key => $option)
                                <span data-asset-legend-type="{{ $key }}">
                                    <i style="background: {{ $option['color'] }}"></i>
                                    {{ $option['label'] }}
                                    <strong id="asset-map-count-{{ $key }}">0</strong>
                                </span>
                            @endforeach
                        </div>
                    </aside>
                </article>
            </div>
        </article>
    </section>

    <section class="section-wrap section-wrap--last">
        <div class="container-grid">
            @if ($assets->isEmpty())
                <article class="section-card" style="padding: 1rem;">
                    <p style="margin: 0; color: #6b7280;">Belum ada data aset desa yang sesuai filter.</p>
                </article>
            @else
                @php
                    $assetTypeIcons = [
                        'aset_desa' => 'fa-solid fa-landmark',
                        'umkm' => 'fa-solid fa-store',
                        'fasilitas_umum' => 'fa-solid fa-building',
                        'pasar' => 'fa-solid fa-shop',
                        'pendidikan' => 'fa-solid fa-school',
                        'kesehatan' => 'fa-solid fa-hospital',
                        'lainnya' => 'fa-solid fa-location-dot',
                    ];
                @endphp
                <div class="infographic-grid">
                    @foreach ($assets as $asset)
                        @php
                            $shortDesc = \Illuminate\Support\Str::limit(strip_tags((string) $asset->description), 120);
                            $detailPayload = [
                                'id' => $asset->id,
                                'name' => $asset->name,
                                'typeLabel' => $asset->typeLabel(),
                                'typeColor' => $asset->typeColor(),
                                'icon' => $assetTypeIcons[$asset->type] ?? 'fa-solid fa-location-dot',
                                'subcategory' => $asset->subcategory,
                                'description' => (string) $asset->description,
                                'address' => $asset->address,
                                'contactPerson' => $asset->contact_person,
                                'contactPhone' => $asset->contact_phone,
                                'latitude' => $asset->latitude,
                                'longitude' => $asset->longitude,
                                'mapUrl' => $asset->map_url,
                            ];
                        @endphp
                        <article class="section-card infographic-card infographic-card--asset infographic-card--asset-interactive"
                            role="button"
                            tabindex="0"
                            data-asset-detail='@json($detailPayload)'>
                            <div class="infographic-card__asset-icon" style="--type-color: {{ $asset->typeColor() }}">
                                <i class="{{ $assetTypeIcons[$asset->type] ?? 'fa-solid fa-location-dot' }}" aria-hidden="true"></i>
                            </div>
                            <h3 class="infographic-card__asset-title">{{ $asset->name }}</h3>
                            <span class="infographic-card__type" style="--type-color: {{ $asset->typeColor() }}">{{ $asset->typeLabel() }}</span>
                            <p>{{ $shortDesc ?: 'Deskripsi belum tersedia.' }}</p>
                        </article>
                    @endforeach
                </div>

                <div style="margin-top: 1rem;">
                    {{ $assets->links() }}
                </div>

                <div id="asset-detail-modal" class="asset-detail-modal" hidden>
                    <div class="asset-detail-modal__backdrop" data-close-asset-modal></div>
                    <div class="asset-detail-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="assetDetailTitle">
                        <button type="button" class="asset-detail-modal__close" data-close-asset-modal aria-label="Tutup detail">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <div class="asset-detail-modal__icon" id="assetDetailIconWrap">
                            <i id="assetDetailIcon" class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        </div>
                        <h3 id="assetDetailTitle">Detail Aset</h3>
                        <span id="assetDetailType" class="asset-detail-modal__type">-</span>
                        <div class="asset-detail-modal__content">
                            <p id="assetDetailDescription">-</p>
                            <dl>
                                <div><dt>Subkategori</dt><dd id="assetDetailSubcategory">-</dd></div>
                                <div><dt>Alamat</dt><dd id="assetDetailAddress">-</dd></div>
                                <div><dt>Kontak</dt><dd id="assetDetailContact">-</dd></div>
                                <div><dt>Koordinat</dt><dd id="assetDetailCoords">-</dd></div>
                            </dl>
                            <div class="asset-detail-modal__actions">
                                <a id="assetDetailMapLink" href="#" target="_blank" rel="noopener">Buka Lokasi</a>
                                <a id="assetDetailRouteLink" href="#" target="_blank" rel="noopener">Rute Google Maps</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@elseif ($tab === 'penduduk')
    <section class="section-wrap section-wrap--last">
        <div class="container-grid">
            @if ($populations->isEmpty())
                <article class="section-card infographic-population-card">
                    <div class="section-head">
                        <h2>Infografis Penduduk</h2>
                    </div>
                    <p style="margin: 0; color: #6b7280;">Data penduduk belum tersedia.</p>
                </article>
            @else
                @php
                    $latestPopulation = $latestPopulation ?? $populations->first();
                    $comparisonRows = ($populationVisibleRows ?? collect())->values();
                    $previousPopulation = $comparisonRows->skip(1)->first();
                    $latestTotal = $latestPopulation?->total() ?? 0;
                    $previousTotal = $previousPopulation?->total() ?? 0;
                    $growth = $latestTotal - $previousTotal;
                    $growthPercent = $previousTotal > 0 ? ($growth / $previousTotal) * 100 : null;
                    $malePercent = $latestTotal > 0 ? (($latestPopulation?->male ?? 0) / $latestTotal) * 100 : 0;
                    $femalePercent = $latestTotal > 0 ? (($latestPopulation?->female ?? 0) / $latestTotal) * 100 : 0;
                @endphp

                <div class="population-kpi-grid">
                    <article class="section-card population-kpi-card">
                        <small>Total Penduduk</small>
                        <h3>{{ number_format($latestTotal, 0, ',', '.') }}</h3>
                        <p>Jiwa</p>
                    </article>
                    <article class="section-card population-kpi-card">
                        <small>Kepala Keluarga</small>
                        <h3>{{ number_format((int) ($latestPopulation?->households ?? 0), 0, ',', '.') }}</h3>
                        <p>KK</p>
                    </article>
                    <article class="section-card population-kpi-card">
                        <small>Laki-laki</small>
                        <h3>{{ number_format((int) ($latestPopulation?->male ?? 0), 0, ',', '.') }}</h3>
                        <p>{{ number_format($malePercent, 1, ',', '.') }}%</p>
                    </article>
                    <article class="section-card population-kpi-card">
                        <small>Perempuan</small>
                        <h3>{{ number_format((int) ($latestPopulation?->female ?? 0), 0, ',', '.') }}</h3>
                        <p>{{ number_format($femalePercent, 1, ',', '.') }}%</p>
                    </article>
                </div>

                <div class="population-layout">
                    <article class="section-card population-chart-card">
                        <div class="population-chart-card__head">
                            <h3>Tren Penduduk per Tahun</h3>
                            <small>Laki-laki dan Perempuan</small>
                        </div>
                        <div class="population-chart-card__canvas-wrap">
                            <canvas id="populationTrendChart"></canvas>
                        </div>
                    </article>

                    <div class="population-side-grid">
                        <article class="section-card population-chart-card">
                            <div class="population-chart-card__head">
                                <h3>Komposisi Jenis Kelamin</h3>
                                <small>Data {{ $latestPopulation?->year ?? '-' }}</small>
                            </div>
                            <div class="population-chart-card__canvas-wrap population-chart-card__canvas-wrap--sm">
                                <canvas id="populationGenderChart"></canvas>
                            </div>
                        </article>

                        <article class="section-card population-insight-card">
                            <h3>Ringkasan</h3>
                            <ul>
                                <li>
                                    <span>Perubahan Tahunan</span>
                                    <strong>{{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 0, ',', '.') }} Jiwa
                                        @if ($growthPercent !== null)
                                            ({{ $growthPercent >= 0 ? '+' : '' }}{{ number_format($growthPercent, 2, ',', '.') }}%)
                                        @endif
                                    </strong>
                                </li>
                                <li>
                                    <span>Rasio L/P</span>
                                    <strong>{{ number_format((float) ($latestPopulation?->male ?? 0), 0, ',', '.') }} : {{ number_format((float) ($latestPopulation?->female ?? 0), 0, ',', '.') }}</strong>
                                </li>
                                <li>
                                    <span>Rata-rata Anggota KK</span>
                                    <strong>
                                        @if (($latestPopulation?->households ?? 0) > 0)
                                            {{ number_format($latestTotal / (int) $latestPopulation->households, 2, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </strong>
                                </li>
                            </ul>
                        </article>
                    </div>
                </div>

                <article class="section-card population-category-card">
                    <div class="population-chart-card__head">
                        <h3>Statistik Kategori Penduduk</h3>
                        <small>Umur, Pendidikan, Pekerjaan, Agama, dan Status Perkawinan</small>
                    </div>
                    @if ($populationStatsByCategory->isEmpty())
                        <p class="population-category-card__empty">Data statistik kategori belum tersedia. Silakan input dari backend menu Kelola Statistik Penduduk.</p>
                    @else
                        @php
                            $categoryMeta = \App\Models\VillagePopulationStat::categoryOptions();
                        @endphp
                        <div class="population-category-grid">
                            @foreach ($populationStatsByCategory as $category => $rows)
                                @php
                                    $meta = $categoryMeta[$category] ?? ['label' => ucfirst(str_replace('_', ' ', $category)), 'icon' => 'fa-solid fa-chart-pie', 'color' => '#0c3f7f'];
                                    $maxValue = (int) $rows->max('value');
                                    $canvasId = 'populationCategoryChart'.\Illuminate\Support\Str::studly($category);
                                @endphp
                                <article class="population-category-panel" style="--category-color: {{ $meta['color'] }}">
                                    <header>
                                        <span><i class="{{ $meta['icon'] }}"></i></span>
                                        <div>
                                            <h4>{{ $meta['label'] }}</h4>
                                            <small>{{ $rows->count() }} indikator</small>
                                        </div>
                                    </header>
                                    <ul>
                                        @foreach ($rows as $row)
                                            @php
                                                $percent = $maxValue > 0 ? ($row->value / $maxValue) * 100 : 0;
                                            @endphp
                                            <li>
                                                <div class="population-category-panel__label">
                                                    <span>{{ $row->label }}</span>
                                                    <strong>{{ number_format((int) $row->value, 0, ',', '.') }}{{ $row->unit ? ' '.$row->unit : '' }}</strong>
                                                </div>
                                                <div class="population-category-panel__bar">
                                                    <i style="width: {{ number_format($percent, 2, '.', '') }}%"></i>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="population-category-panel__chart-wrap">
                                        <canvas id="{{ $canvasId }}"></canvas>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </article>

                <article class="section-card population-table-card">
                    <div class="population-chart-card__head">
                        <h3>Riwayat Data Penduduk</h3>
                        <small>Menampilkan {{ ($populationVisibleRows ?? collect())->count() }} tahun data</small>
                    </div>
                    <div class="population-table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tahun</th>
                                    <th>Laki-laki</th>
                                    <th>Perempuan</th>
                                    <th>Total</th>
                                    <th>Kepala Keluarga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (($populationVisibleRows ?? $populations) as $item)
                                    <tr>
                                        <td>{{ $item->year }}</td>
                                        <td>{{ number_format((int) $item->male, 0, ',', '.') }}</td>
                                        <td>{{ number_format((int) $item->female, 0, ',', '.') }}</td>
                                        <td>{{ number_format($item->total(), 0, ',', '.') }}</td>
                                        <td>{{ number_format((int) $item->households, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
            @endif
        </div>
    </section>
@else
    <section class="section-wrap section-wrap--last">
        <div class="container-grid">
            @if ($otherInfographics->isEmpty())
                <article class="section-card" style="padding: 1rem;">
                    <p style="margin: 0; color: #6b7280;">Data infografis tambahan belum tersedia.</p>
                </article>
            @else
                <div class="infographic-grid">
                    @foreach ($otherInfographics as $item)
                        <article class="section-card infographic-card">
                            <div class="infographic-card__head">
                                <span style="background: {{ $item->color ?: '#64748b' }}">
                                    @if (\Illuminate\Support\Str::startsWith((string) $item->icon, 'fa-'))
                                        <i class="{{ $item->icon }}"></i>
                                    @else
                                        {{ $item->icon ?: 'INFO' }}
                                    @endif
                                </span>
                                <h3>{{ $item->title }}</h3>
                            </div>
                            <p>
                                <strong style="color:#0c3f7f;">{{ $item->value }}</strong>
                                @if ($item->unit)
                                    {{ $item->unit }}
                                @endif
                            </p>
                            @if (!empty($item->category))
                                <div class="infographic-card__meta">
                                    <small>Kategori: {{ $item->categoryLabel() }}</small>
                                </div>
                            @endif
                            @if ($item->description)
                                <div class="infographic-card__meta"><small>{{ $item->description }}</small></div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endif

@if ($tab === 'aset')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script>
        (function () {
            const mapEl = document.getElementById('asset-map');
            if (!mapEl || typeof L === 'undefined') return;

            const items = @json($assetMapItems);
            const typeOptions = @json($typeOptions);
            const fallbackLat = {{ $village?->latitude ?? -2.548926 }};
            const fallbackLng = {{ $village?->longitude ?? 118.0148634 }};
            const villageBoundary = @json($village?->boundary_geojson);
            const hasItems = Array.isArray(items) && items.length > 0;

            const map = L.map('asset-map').setView(
                hasItems ? [items[0].latitude, items[0].longitude] : [fallbackLat, fallbackLng],
                hasItems ? 13 : 12
            );

            const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            const cartoLightLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
            });
            const topoLayer = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                maxZoom: 17,
                attribution: '&copy; OpenStreetMap contributors, SRTM | OpenTopoMap',
            });

            const makeLayerGroup = () => typeof L.markerClusterGroup === 'function'
                ? L.markerClusterGroup({
                    showCoverageOnHover: false,
                    spiderfyOnMaxZoom: true,
                    disableClusteringAtZoom: 16,
                })
                : L.layerGroup();
            const allAssetsLayer = makeLayerGroup();
            const byTypeLayers = {};
            const markerById = new Map();
            const itemById = new Map();
            const typeKeys = Object.keys(typeOptions || {});
            const typeFaIcon = {
                aset_desa: 'fa-solid fa-landmark',
                umkm: 'fa-solid fa-store',
                fasilitas_umum: 'fa-solid fa-building',
                pasar: 'fa-solid fa-shop',
                pendidikan: 'fa-solid fa-school',
                kesehatan: 'fa-solid fa-hospital',
                lainnya: 'fa-solid fa-location-dot',
            };
            const typeCount = {};
            typeKeys.forEach((key) => {
                typeCount[key] = 0;
            });

            Object.keys(typeOptions || {}).forEach((typeKey) => {
                byTypeLayers[typeKey] = makeLayerGroup();
            });

            const bounds = [];

            if (villageBoundary && typeof villageBoundary === 'object') {
                try {
                    const boundaryStyle = {
                        color: '#1d4ed8',
                        weight: 3,
                        opacity: 1,
                        dashArray: '14 10',
                        dashOffset: '0',
                        lineCap: 'round',
                        lineJoin: 'round',
                        fillColor: '#1d4ed8',
                        fillOpacity: 0.04,
                        className: 'village-boundary-stroke',
                    };

                    const boundaryLayer = L.geoJSON(villageBoundary, {
                        style: () => boundaryStyle,
                    }).addTo(map);
                    boundaryLayer.eachLayer((layer) => {
                        if (layer && typeof layer.setStyle === 'function') {
                            layer.setStyle(boundaryStyle);
                        }
                    });
                    if (typeof boundaryLayer.bringToFront === 'function') {
                        boundaryLayer.bringToFront();
                    }

                    const boundaryBounds = boundaryLayer.getBounds();
                    if (boundaryBounds && boundaryBounds.isValid()) {
                        map.fitBounds(boundaryBounds, { padding: [18, 18] });
                    }
                } catch (error) {
                    // Ignore invalid boundary payload and continue with marker bounds.
                }
            }

            items.forEach((item) => {
                if (!item.latitude || !item.longitude) return;
                itemById.set(item.id, item);
                if (item.type && typeof typeCount[item.type] === 'number') {
                    typeCount[item.type] += 1;
                }

                const iconClass = typeFaIcon[item.type] || 'fa-solid fa-location-dot';
                const iconHtml = `<span class="asset-marker__icon asset-marker__icon--fa"><i class="${iconClass}" aria-hidden="true"></i></span>`;

                const markerIcon = L.divIcon({
                    className: 'asset-marker-badge-wrap',
                    html: `
                        <div class="asset-marker-badge" style="--asset-color:${item.color || '#1d4ed8'}">
                            <div class="asset-marker-badge__pin">
                                <span class="asset-marker-badge__glyph">${iconHtml}</span>
                            </div>
                            <div class="asset-marker-badge__label">${item.name || '-'}</div>
                        </div>
                    `,
                    iconSize: [232, 44],
                    iconAnchor: [16, 38],
                    popupAnchor: [0, -34],
                });

                const googleRoute = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(item.latitude + ',' + item.longitude)}`;
                const popupHtml = `
                    <article class="asset-map-popup">
                        <h3>${item.name}</h3>
                        <small style="background:${item.color || '#1d4ed8'}">${item.type_label || '-'}</small>
                        ${item.subcategory ? `<p>${item.subcategory}</p>` : ''}
                        ${item.address ? `<p>${item.address}</p>` : ''}
                        ${item.description ? `<p>${item.description}</p>` : ''}
                        <div class="asset-map-popup__actions">
                            <a href="${item.map_url || googleRoute}" target="_blank" rel="noopener">Lihat Lokasi</a>
                            <a href="${googleRoute}" target="_blank" rel="noopener">Buka Rute</a>
                        </div>
                    </article>
                `;
                const baseMarker = L.marker([item.latitude, item.longitude], { icon: markerIcon });
                baseMarker.bindPopup(popupHtml, { maxWidth: 290 });
                allAssetsLayer.addLayer(baseMarker);
                markerById.set(item.id, baseMarker);
                if (item.type && byTypeLayers[item.type]) {
                    const typedMarker = L.marker([item.latitude, item.longitude], { icon: markerIcon });
                    typedMarker.bindPopup(popupHtml, { maxWidth: 290 });
                    byTypeLayers[item.type].addLayer(typedMarker);
                }
                bounds.push([item.latitude, item.longitude]);
            });

            map.addLayer(allAssetsLayer);

            const baseMaps = {
                'Peta Standar': osmLayer,
                'Peta Terang': cartoLightLayer,
                'Topografi': topoLayer,
            };

            const overlayMaps = {
                'Semua Aset': allAssetsLayer,
            };
            Object.entries(typeOptions || {}).forEach(([key, option]) => {
                if (!byTypeLayers[key]) return;
                const label = `<span style="display:inline-flex;align-items:center;gap:.35rem;"><i style="width:9px;height:9px;border-radius:999px;display:inline-block;background:${option.color}"></i>${option.label}</span>`;
                overlayMaps[label] = byTypeLayers[key];
            });

            L.control.layers(baseMaps, overlayMaps, {
                collapsed: true,
                position: 'topright',
            }).addTo(map);

            const typedLayers = Object.values(byTypeLayers);
            const sideTotalEl = document.getElementById('asset-map-total');
            const searchInputEl = document.getElementById('asset-map-search');
            const searchResultsEl = document.getElementById('asset-map-search-results');
            const searchClearEl = document.getElementById('asset-map-search-clear');
            const legendTypeEls = new Map(typeKeys.map((key) => [key, document.querySelector(`[data-asset-legend-type="${key}"]`)]));
            const legendCountEls = new Map(typeKeys.map((key) => [key, document.getElementById(`asset-map-count-${key}`)]));

            const normalizeText = (value) => String(value || '').toLowerCase().trim();
            const getActiveTypes = () => {
                if (map.hasLayer(allAssetsLayer)) {
                    return typeKeys;
                }
                const active = typeKeys.filter((key) => byTypeLayers[key] && map.hasLayer(byTypeLayers[key]));
                return active.length ? active : typeKeys;
            };
            const updateSidePanel = () => {
                if (!sideTotalEl) return;
                const activeTypes = getActiveTypes();
                const activeSet = new Set(activeTypes);
                const total = activeTypes.reduce((sum, key) => sum + (typeCount[key] || 0), 0);
                sideTotalEl.textContent = String(total);

                typeKeys.forEach((key) => {
                    const countEl = legendCountEls.get(key);
                    const legendEl = legendTypeEls.get(key);
                    if (countEl) {
                        countEl.textContent = String(typeCount[key] || 0);
                    }
                    if (legendEl) {
                        legendEl.classList.toggle('is-muted', !activeSet.has(key));
                    }
                });
            };

            const focusAsset = (id) => {
                const marker = markerById.get(id);
                const item = itemById.get(id);
                if (!marker || !item) return;

                typedLayers.forEach((layer) => {
                    if (map.hasLayer(layer)) map.removeLayer(layer);
                });
                if (!map.hasLayer(allAssetsLayer)) {
                    map.addLayer(allAssetsLayer);
                }
                updateSidePanel();

                map.setView([item.latitude, item.longitude], 17, { animate: true });
                if (typeof allAssetsLayer.zoomToShowLayer === 'function') {
                    allAssetsLayer.zoomToShowLayer(marker, () => marker.openPopup());
                } else {
                    marker.openPopup();
                }
            };

            const renderSearchResults = (query) => {
                if (!searchResultsEl) return;
                const keyword = normalizeText(query);
                if (!keyword) {
                    searchResultsEl.hidden = true;
                    searchResultsEl.innerHTML = '';
                    return;
                }

                const matches = items
                    .filter((item) => {
                        const combined = normalizeText([item.name, item.address, item.subcategory, item.type_label].join(' '));
                        return combined.includes(keyword);
                    })
                    .slice(0, 8);

                if (!matches.length) {
                    searchResultsEl.hidden = false;
                    searchResultsEl.innerHTML = '<div class="map-search-results__empty">Data tidak ditemukan.</div>';
                    return;
                }

                searchResultsEl.hidden = false;
                searchResultsEl.innerHTML = matches.map((item) => `
                    <button type="button" class="map-search-results__item" data-id="${item.id}">
                        <span>${item.name}</span>
                        <small>${item.address || (item.type_label || '-')}</small>
                    </button>
                `).join('');
            };

            map.on('overlayadd', (event) => {
                if (event.layer === allAssetsLayer) {
                    typedLayers.forEach((layer) => {
                        if (map.hasLayer(layer)) map.removeLayer(layer);
                    });
                    updateSidePanel();
                    return;
                }

                if (typedLayers.includes(event.layer) && map.hasLayer(allAssetsLayer)) {
                    map.removeLayer(allAssetsLayer);
                }
                updateSidePanel();
            });

            map.on('overlayremove', (event) => {
                if (event.layer === allAssetsLayer) return;
                const hasTypedActive = typedLayers.some((layer) => map.hasLayer(layer));
                if (!hasTypedActive && !map.hasLayer(allAssetsLayer)) {
                    map.addLayer(allAssetsLayer);
                }
                updateSidePanel();
            });

            if (bounds.length > 1) {
                map.fitBounds(bounds, { padding: [22, 22] });
            }

            if (searchInputEl && searchResultsEl) {
                searchInputEl.addEventListener('input', (event) => {
                    renderSearchResults(event.target.value);
                });
                searchInputEl.addEventListener('keydown', (event) => {
                    if (event.key !== 'Enter') return;
                    event.preventDefault();
                    const keyword = normalizeText(searchInputEl.value);
                    if (!keyword) return;
                    const first = items.find((item) => normalizeText([item.name, item.address, item.subcategory, item.type_label].join(' ')).includes(keyword));
                    if (first) {
                        focusAsset(first.id);
                        searchResultsEl.hidden = true;
                    }
                });
                searchResultsEl.addEventListener('click', (event) => {
                    const target = event.target.closest('[data-id]');
                    if (!target) return;
                    const id = Number(target.getAttribute('data-id'));
                    focusAsset(id);
                    searchResultsEl.hidden = true;
                });
            }

            if (searchClearEl && searchInputEl && searchResultsEl) {
                searchClearEl.addEventListener('click', () => {
                    searchInputEl.value = '';
                    searchResultsEl.hidden = true;
                    searchResultsEl.innerHTML = '';
                });
            }

            updateSidePanel();
        })();
    </script>
@endif

@if ($tab === 'penduduk')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
@endif

@if ($tab === 'aset')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
@endif

@if ($tab === 'lainnya')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
@endif

@if ($tab === 'penduduk' && $populationChartItems->isNotEmpty())
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const trendCanvas = document.getElementById('populationTrendChart');
            const genderCanvas = document.getElementById('populationGenderChart');
            if ((!trendCanvas && !genderCanvas) || typeof Chart === 'undefined') return;

            const rows = (@json($populationChartItems) || []).slice().sort((a, b) => Number(a.year) - Number(b.year));
            const labels = rows.map((row) => row.year);
            const male = rows.map((row) => row.male);
            const female = rows.map((row) => row.female);
            const latest = rows[rows.length - 1] || { male: 0, female: 0 };
            const categoryRows = @json($populationStatsByCategory);
            const categoryMeta = @json(\App\Models\VillagePopulationStat::categoryOptions());

            if (trendCanvas) {
                new Chart(trendCanvas, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Laki-laki', data: male, borderColor: '#1b63bf', backgroundColor: 'rgba(27,99,191,.14)', tension: .3, fill: true },
                            { label: 'Perempuan', data: female, borderColor: '#ec4899', backgroundColor: 'rgba(236,72,153,.1)', tension: .3, fill: true },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } },
                    }
                });
            }

            if (genderCanvas) {
                new Chart(genderCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Laki-laki', 'Perempuan'],
                        datasets: [{
                            data: [latest.male || 0, latest.female || 0],
                            backgroundColor: ['#1b63bf', '#ec4899'],
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: { legend: { position: 'bottom' } },
                    },
                });
            }

            Object.entries(categoryRows || {}).forEach(([key, rows]) => {
                const canvasId = `populationCategoryChart${key.replace(/(^|_)([a-z])/g, (_, __, chr) => chr.toUpperCase())}`;
                const canvas = document.getElementById(canvasId);
                if (!canvas || !Array.isArray(rows) || !rows.length) return;

                const labels = rows.map((row) => row.label);
                const values = rows.map((row) => Number(row.value || 0));
                const color = (categoryMeta[key] && categoryMeta[key].color) ? categoryMeta[key].color : '#0c3f7f';
                const bgColor = `${color}33`;

                new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            backgroundColor: bgColor,
                            borderColor: color,
                            borderWidth: 1.2,
                            borderRadius: 6,
                            maxBarThickness: 18,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            x: {
                                ticks: { display: false },
                                grid: { display: false },
                            },
                            y: {
                                ticks: { display: false },
                                grid: { color: '#e9eff9' },
                                beginAtZero: true,
                            },
                        },
                    },
                });
            });
        })();
    </script>
@endif

@if ($tab === 'aset')
    <script>
        (function () {
            const modal = document.getElementById('asset-detail-modal');
            if (!modal) return;

            const closeTargets = modal.querySelectorAll('[data-close-asset-modal]');
            const titleEl = document.getElementById('assetDetailTitle');
            const typeEl = document.getElementById('assetDetailType');
            const descEl = document.getElementById('assetDetailDescription');
            const subcategoryEl = document.getElementById('assetDetailSubcategory');
            const addressEl = document.getElementById('assetDetailAddress');
            const contactEl = document.getElementById('assetDetailContact');
            const coordsEl = document.getElementById('assetDetailCoords');
            const mapLinkEl = document.getElementById('assetDetailMapLink');
            const routeLinkEl = document.getElementById('assetDetailRouteLink');
            const iconWrapEl = document.getElementById('assetDetailIconWrap');
            const iconEl = document.getElementById('assetDetailIcon');

            const cards = document.querySelectorAll('.infographic-card--asset-interactive');
            const escapeText = (value) => String(value || '').trim() || '-';

            const closeModal = () => {
                modal.setAttribute('hidden', 'hidden');
                document.body.classList.remove('is-modal-open');
            };

            const openModal = (payload) => {
                titleEl.textContent = escapeText(payload.name);
                typeEl.textContent = escapeText(payload.typeLabel);
                typeEl.style.background = payload.typeColor || '#0c3f7f';
                descEl.textContent = escapeText(payload.description);
                subcategoryEl.textContent = escapeText(payload.subcategory);
                addressEl.textContent = escapeText(payload.address);
                contactEl.textContent = [payload.contactPerson, payload.contactPhone].filter(Boolean).join(' - ') || '-';

                const lat = payload.latitude;
                const lng = payload.longitude;
                const hasCoords = lat !== null && lat !== undefined && lng !== null && lng !== undefined;
                coordsEl.textContent = hasCoords ? `${lat}, ${lng}` : '-';

                iconWrapEl.style.background = payload.typeColor || '#0c3f7f';
                iconEl.className = payload.icon || 'fa-solid fa-location-dot';
                iconEl.classList.add('fa-solid');

                const routeUrl = hasCoords
                    ? `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(lat + ',' + lng)}`
                    : '#';
                mapLinkEl.href = payload.mapUrl || routeUrl;
                routeLinkEl.href = routeUrl;

                modal.removeAttribute('hidden');
                document.body.classList.add('is-modal-open');
            };

            cards.forEach((card) => {
                const show = () => {
                    const raw = card.getAttribute('data-asset-detail');
                    if (!raw) return;
                    try {
                        openModal(JSON.parse(raw));
                    } catch (_) {}
                };
                card.addEventListener('click', show);
                card.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        show();
                    }
                });
            });

            closeTargets.forEach((el) => el.addEventListener('click', closeModal));
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.hasAttribute('hidden')) {
                    closeModal();
                }
            });
        })();
    </script>
@endif
@endsection
