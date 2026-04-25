@extends('web.web')

@section('content')
@php
    $g = $gambaran ?? [];
    $pageMeta = $g['_page'] ?? [];
    $penduduk = $g['penduduk'] ?? ['kk' => 0, 'male' => 0, 'female' => 0];
    $male = (int) ($village?->population_male ?? ($penduduk['male'] ?? 0));
    $female = (int) ($village?->population_female ?? ($penduduk['female'] ?? 0));
    $kk = (int) ($village?->households ?? ($penduduk['kk'] ?? 0));
    $jumlahPenduduk = (int) ($village?->population ?? ((int) ($penduduk['total'] ?? 0)));
    if ($jumlahPenduduk <= 0) {
        $jumlahPenduduk = $male + $female;
    }
    $pendudukChart = [
        'male' => $male,
        'female' => $female,
        'kk' => $kk,
        'total' => $jumlahPenduduk,
    ];
    $pendudukTrend = collect($g['penduduk_per_tahun'] ?? [])
        ->map(function ($row) {
            return [
                'year' => (int) ($row['year'] ?? 0),
                'total' => (int) ($row['total'] ?? 0),
                'male' => (int) ($row['male'] ?? 0),
                'female' => (int) ($row['female'] ?? 0),
                'kk' => (int) ($row['kk'] ?? 0),
            ];
        })
        ->filter(fn ($row) => $row['year'] > 0)
        ->values();

    if ($pendudukTrend->isEmpty()) {
        $pendudukTrend = collect([[
            'year' => (int) now()->year,
            'total' => $jumlahPenduduk,
            'male' => $male,
            'female' => $female,
            'kk' => $kk,
        ]]);
    }
    $statKategori = $g['statistik_kategori_penduduk'] ?? [];
    $categoryMeta = \App\Models\VillagePopulationStat::categoryOptions();
@endphp

<section class="section-wrap">
    <div class="container-grid">
        <article class="section-card budget-card">
            <h1 style="margin:0; font-size: clamp(1.35rem, 2.5vw, 1.9rem);">{{ $pageMeta['title'] ?? ('Gambaran Umum '.($village?->name ?? 'Desa')) }}</h1>
            @if (!empty($pageMeta['subtitle']))
                <p style="margin-top:.45rem; color:#64748b;">{{ $pageMeta['subtitle'] }}</p>
            @endif
            <p style="margin-top:.65rem; color:#475569;">{{ $g['deskripsi'] ?? ($village?->description ?? 'Data gambaran umum desa belum tersedia.') }}</p>
            {{-- @if (!empty($g['sumber']))
                <p style="margin-top:.5rem; color:#64748b; font-size:.86rem;">
                    Sumber data: <a href="{{ $g['sumber'] }}" target="_blank" rel="noopener" class="text-link">{{ $g['sumber'] }}</a>
                </p>
            @endif --}}
        </article>
    </div>
</section>

<section class="section-wrap">
    <div class="container-grid">
        <div class="stats-grid stats-grid--wide stats-grid--gambaran">
            <article class="section-card stat-card stat-card--with-chart">
                <h3>{{ number_format($jumlahPenduduk, 0, ',', '.') }} Jiwa</h3>
                <p>Total Penduduk</p>
                <div class="stat-card__mini-chart">
                    <canvas id="populationTotalTrendChart" aria-label="Tren total penduduk per tahun"></canvas>
                </div>
            </article>
            <article class="section-card stat-card stat-card--with-chart">
                <h3>{{ number_format($male, 0, ',', '.') }} Jiwa</h3>
                <p>Penduduk Laki-laki</p>
                <div class="stat-card__mini-chart">
                    <canvas id="populationMaleTrendChart" aria-label="Tren penduduk laki-laki per tahun"></canvas>
                </div>
            </article>
            <article class="section-card stat-card stat-card--with-chart">
                <h3>{{ number_format($female, 0, ',', '.') }} Jiwa</h3>
                <p>Penduduk Perempuan</p>
                <div class="stat-card__mini-chart">
                    <canvas id="populationFemaleTrendChart" aria-label="Tren penduduk perempuan per tahun"></canvas>
                </div>
            </article>
            <article class="section-card stat-card stat-card--with-chart">
                <h3>{{ number_format($kk, 0, ',', '.') }} KK</h3>
                <p>Kepala Keluarga</p>
                <div class="stat-card__mini-chart">
                    <canvas id="populationKkTrendChart" aria-label="Tren kepala keluarga per tahun"></canvas>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="section-wrap">
    <div class="container-grid split">
        <article class="section-card budget-card">
            <h2>Batas Wilayah</h2>
            <div class="potensi-list" style="margin-top:.7rem;">
                <div><h3>Utara</h3><p>{{ $g['batas']['utara'] ?? '-' }}</p></div>
                <div><h3>Selatan</h3><p>{{ $g['batas']['selatan'] ?? '-' }}</p></div>
                <div><h3>Barat</h3><p>{{ $g['batas']['barat'] ?? '-' }}</p></div>
                <div><h3>Timur</h3><p>{{ $g['batas']['timur'] ?? '-' }}</p></div>
            </div>
        </article>
        <article class="section-card budget-card">
            <h2>Orbitasi</h2>
            <div class="potensi-list" style="margin-top:.7rem;">
                @foreach (($g['orbitasi'] ?? []) as $row)
                    <div><h3>{{ $row['label'] }}</h3><p>{{ $row['value'] }}</p></div>
                @endforeach
            </div>
        </article>
    </div>
</section>

<section class="section-wrap">
    <div class="container-grid">
        <div class="split">
            <article class="section-card budget-card">
                <h2>Grafik Komposisi Penduduk</h2>
                <div class="demographics-card__chart-wrap" style="min-height:260px; margin-top:.65rem;">
                    <canvas id="populationCompositionChart"></canvas>
                </div>
            </article>
            {{-- <article class="section-card budget-card">
                <h2>Grafik Agama</h2>
                <div class="demographics-card__chart-wrap" style="min-height:260px; margin-top:.65rem;">
                    <canvas id="religionChart"></canvas>
                </div>
                <div id="religionChartSummary" class="religion-chart-summary"></div>
            </article> --}}
        </div>
    </div>
</section>

<section class="section-wrap">
    <div class="container-grid">
        <div class="section-head section-head--stacked">
            <h2>Grafik Statistik Penduduk per Kategori</h2>
            <p>Data kategori diambil dari tabel statistik penduduk tahun terbaru.</p>
        </div>
        <div class="split">
            @forelse ($statKategori as $key => $rows)
                @php
                    $meta = $categoryMeta[$key] ?? ['label' => ucfirst(str_replace('_', ' ', (string) $key)), 'color' => '#0c3f7f'];
                    $canvasId = 'populationCategoryChart'.\Illuminate\Support\Str::studly((string) $key);
                @endphp
                <article class="section-card budget-card">
                    <h2>{{ $meta['label'] }}</h2>
                    <div class="demographics-card__chart-wrap" style="min-height:260px; margin-top:.65rem;">
                        <canvas id="{{ $canvasId }}"></canvas>
                    </div>
                </article>
            @empty
                <article class="section-card budget-card">
                    <h2>Grafik Statistik Penduduk per Kategori</h2>
                    <p style="margin-top:.65rem; color:#64748b;">Data kategori penduduk belum tersedia.</p>
                </article>
            @endforelse
        </div>
    </div>
</section>

<section class="section-wrap section-wrap--last">
    <div class="container-grid">
        <article class="section-card budget-card">
            <h2>Luas Wilayah Menurut Penggunaan</h2>
            <div class="infographic-grid" style="margin-top:.7rem;">
                @foreach (($g['luas'] ?? []) as $row)
                    <article class="section-card infographic-card">
                        <div class="infographic-card__head">
                            <span style="background:#0f5e9f">Luas</span>
                            <h3>{{ $row['label'] }}</h3>
                        </div>
                        <p>{{ $row['value'] }}</p>
                    </article>
                @endforeach
            </div>
        </article>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    (function () {
        if (typeof Chart === 'undefined') return;

        const penduduk = @json($pendudukChart);
        const pendudukTrend = @json($pendudukTrend);
        const agamaRaw = @json($g['agama'] ?? []);
        const agama = Array.isArray(agamaRaw) ? agamaRaw : Object.values(agamaRaw || {});
        const statKategori = @json($statKategori);
        const categoryMeta = @json($categoryMeta);

        const format = (n) => new Intl.NumberFormat('id-ID').format(Number(n || 0));

        const createBar = (id, labels, values, color) => {
            const el = document.getElementById(id);
            if (!el) return;
            new Chart(el, {
                type: 'bar',
                data: { labels, datasets: [{ data: values, backgroundColor: color, borderRadius: 6 }] },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: (ctx) => `${format(ctx.parsed.y)} orang` } }
                    },
                }
            });
        };

        const createMiniTrend = (id, dataKey, lineColor, labelText) => {
            const el = document.getElementById(id);
            if (!el) return;

            const labels = pendudukTrend.map((x) => String(x.year));
            const values = pendudukTrend.map((x) => Number(x[dataKey] || 0));

            new Chart(el, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        borderColor: lineColor,
                        backgroundColor: `${lineColor}22`,
                        borderWidth: 2,
                        tension: 0.32,
                        pointRadius: values.length > 1 ? 2.5 : 3.5,
                        pointHoverRadius: 4.5,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: (items) => `Tahun ${items[0]?.label || '-'}`,
                                label: (ctx) => `${labelText}: ${format(ctx.parsed.y)}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: { display: false },
                            ticks: {
                                color: '#5d708b',
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 6,
                                font: { size: 10, weight: '600' }
                            }
                        },
                        y: {
                            display: true,
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.2)' },
                            ticks: {
                                color: '#5d708b',
                                callback: (value) => format(value),
                                maxTicksLimit: 4,
                                font: { size: 10, weight: '600' }
                            }
                        }
                    },
                    elements: {
                        line: {
                            capBezierPoints: true,
                        }
                    }
                }
            });
        };

        const popEl = document.getElementById('populationCompositionChart');
        if (popEl) {
            new Chart(popEl, {
                type: 'doughnut',
                data: {
                    labels: ['Laki-laki', 'Perempuan'],
                    datasets: [{
                        data: [penduduk.male || 0, penduduk.female || 0],
                        backgroundColor: ['#1b63bf', '#ec4899'],
                        borderColor: '#fff',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${format(ctx.parsed)} orang` } }
                    }
                }
            });
        }

        createMiniTrend('populationTotalTrendChart', 'total', '#1b63bf', 'Total Penduduk');
        createMiniTrend('populationMaleTrendChart', 'male', '#0f766e', 'Penduduk Laki-laki');
        createMiniTrend('populationFemaleTrendChart', 'female', '#db2777', 'Penduduk Perempuan');
        createMiniTrend('populationKkTrendChart', 'kk', '#f59e0b', 'Kepala Keluarga');

        const religionEl = document.getElementById('religionChart');
        const religionSummaryEl = document.getElementById('religionChartSummary');
        if (religionEl) {
            const religionRows = agama
                .map((x) => ({
                    label: String(x.label || '').trim(),
                    value: Number(x.value || 0),
                }))
                .filter((x) => x.label !== '' && x.value > 0);
            const religionTotal = religionRows.reduce((sum, item) => sum + item.value, 0);
            const religionColors = ['#f59e0b', '#06b6d4', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899', '#64748b', '#14b8a6'];
            const hasReligionData = religionRows.length > 0;
            const chartLabels = hasReligionData ? religionRows.map((x) => x.label) : ['Data agama belum tersedia'];
            const chartValues = hasReligionData ? religionRows.map((x) => x.value) : [1];
            const chartColors = hasReligionData
                ? religionRows.map((_, idx) => religionColors[idx % religionColors.length])
                : ['#cbd5e1'];

            new Chart(religionEl, {
                type: 'doughnut',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartValues,
                        backgroundColor: chartColors,
                        borderColor: '#fff',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => {
                                    if (!hasReligionData) return 'Data agama belum tersedia';
                                    const value = Number(ctx.parsed ?? 0);
                                    const percent = religionTotal > 0 ? ((value / religionTotal) * 100).toFixed(1) : '0.0';
                                    return `${ctx.label}: ${format(value)} orang (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });

            if (religionSummaryEl) {
                religionSummaryEl.innerHTML = '';
                if (!hasReligionData) {
                    religionSummaryEl.textContent = 'Data agama belum tersedia.';
                } else {
                    religionRows.forEach((row, idx) => {
                        const percent = religionTotal > 0 ? ((row.value / religionTotal) * 100).toFixed(1) : '0.0';
                        const item = document.createElement('div');
                        item.className = 'religion-chart-summary__item';
                        item.innerHTML = `
                            <span class="religion-chart-summary__dot" style="background:${religionColors[idx % religionColors.length]}"></span>
                            <strong>${row.label}</strong>
                            <small>${format(row.value)} orang (${percent}%)</small>
                        `;
                        religionSummaryEl.appendChild(item);
                    });
                }
            }
        }

        Object.entries(statKategori || {}).forEach(([key, rows]) => {
            if (!Array.isArray(rows) || rows.length === 0) return;
            const canvasId = `populationCategoryChart${String(key)
                .split('_')
                .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
                .join('')}`;
            const labels = rows.map((x) => x.label);
            const values = rows.map((x) => Number(x.value || 0));
            const meta = categoryMeta?.[key] || {};
            const color = meta.color || '#0c3f7f';

            // Kategori agama/status kawin lebih mudah dibaca sebagai komposisi persentase.
            const chartType = (key === 'agama' || key === 'status_kawin') ? 'doughnut' : 'bar';
            const el = document.getElementById(canvasId);
            if (!el) return;

            if (chartType === 'doughnut') {
                const total = values.reduce((sum, val) => sum + val, 0);
                const palette = ['#1b63bf', '#ec4899', '#0f766e', '#f59e0b', '#8b5cf6', '#06b6d4', '#64748b', '#16a34a'];
                new Chart(el, {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            backgroundColor: values.map((_, idx) => palette[idx % palette.length]),
                            borderColor: '#fff',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const value = Number(ctx.parsed ?? 0);
                                        const pct = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                                        return `${ctx.label}: ${format(value)} (${pct}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                createBar(canvasId, labels, values, color);
            }
        });
    })();
</script>
@endsection
