@extends('web.web')

@section('content')
@php
    $moduleStates = $moduleStates ?? [
        'complaints' => false,
        'news' => false,
        'agendas' => false,
        'announcements' => false,
        'services' => false,
        'galleries' => false,
        'infographics' => false,
    ];
@endphp
<section class="section-wrap">
    <div class="container-grid page-section-stack">
        <article class="page-hero section-card">
            <div>
                <small>Statistik Desa</small>
                <h1>Statistik {{ $village?->name ?? 'Desa' }}</h1>
                <p>Ringkasan indikator penting desa berdasarkan {{ $periodLabel ?? 'periode terpilih' }}.</p>
            </div>
            <div class="page-hero__actions">
                <form method="GET" class="page-hero-filter page-hero-filter--statistics">
                    <div class="stat-filter-grid">
                        <label for="start_year">
                            Dari Tahun
                            <select id="start_year" name="start_year">
                                @foreach ($yearOptions as $yearOption)
                                    <option value="{{ $yearOption }}" @selected((int) $startYear === (int) $yearOption)>{{ $yearOption }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label for="end_year">
                            Sampai Tahun
                            <select id="end_year" name="end_year">
                                @foreach ($yearOptions as $yearOption)
                                    <option value="{{ $yearOption }}" @selected((int) $endYear === (int) $yearOption)>{{ $yearOption }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button type="submit">Tampilkan Statistik</button>
                        <a href="{{ route('statistik') }}">Reset Filter</a>
                        <a href="{{ route('statistik.pdf', ['start_year' => $startYear, 'end_year' => $endYear]) }}">Download PDF</a>
                        <a href="{{ route('statistik.excel', ['start_year' => $startYear, 'end_year' => $endYear]) }}">Download Excel</a>
                    </div>
                </form>
            </div>
        </article>

        @if ($errors->any())
            <article class="section-card" style="padding:1rem; border-color:#fecaca; background:#fff1f2;">
                <strong style="color:#991b1b;">Filter statistik belum valid.</strong>
                <ul style="margin:.5rem 0 0; color:#7f1d1d;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </article>
        @endif

        <article class="section-card" style="padding:1rem;">
            <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:.75rem;">
                <div>
                    <h2 style="margin:0; color:#123e74; font-size:1.05rem;">Periode Statistik</h2>
                    <p style="margin:.25rem 0 0; color:#5d708b;">{{ $periodDateLabel }}</p>
                </div>
                @if (!($hasPeriodData ?? false))
                    <p style="margin:0; color:#9a3412; background:#fff7ed; border:1px solid #fed7aa; border-radius:999px; padding:.45rem .75rem; font-size:.85rem;">
                        Tidak terdapat data periodik pada periode yang dipilih.
                    </p>
                @endif
            </div>
        </article>

        @if (request()->has('year'))
            <article class="section-card" style="padding:1rem; border-color:#bfdbfe; background:#eff6ff;">
                <p style="margin:0; color:#1e3a8a;">Parameter lama <code>year</code> sudah digantikan oleh <code>start_year</code> dan <code>end_year</code>. Gunakan filter periode di atas untuk hasil terbaru.</p>
            </article>
        @endif

        @if (empty($yearOptions))
            <article class="section-card" style="padding:1rem;">
                <p style="margin:0; color:#64748b;">Belum ada tahun statistik yang tersedia.</p>
            </article>
        @endif

        <div class="stats-grid stats-grid--wide stat-page-kpi">
            @foreach ($kpis as $kpi)
                <article class="section-card stat-card stat-page-reveal">
                    <p style="margin:0; color:#5d708b; font-size:.8rem;">{{ $kpi['label'] }}</p>
                    <h3 style="margin:.25rem 0 0;">{{ $kpi['value'] }}</h3>
                    <p style="margin:.25rem 0 0; color:#64748b; font-size:.76rem;">{{ $kpi['scope'] ?? '' }}</p>
                </article>
            @endforeach
        </div>

        <div class="split" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
            <article class="section-card stat-page-reveal" style="padding:1rem;">
                <h3 style="margin:0; color:#123e74;">Komposisi Penduduk</h3>
                @if ($population['available'] ?? false)
                    <div style="margin-top:.6rem; min-height:220px;">
                        <canvas id="populationChart" height="220"></canvas>
                    </div>
                    <div style="margin-top:.6rem; display:grid; gap:.45rem;">
                        <p style="margin:0; font-size:.88rem; color:#334155;">Laki-laki: <strong>{{ number_format((int) ($population['male'] ?? 0), 0, ',', '.') }}</strong></p>
                        <p style="margin:0; font-size:.88rem; color:#334155;">Perempuan: <strong>{{ number_format((int) ($population['female'] ?? 0), 0, ',', '.') }}</strong></p>
                        <p style="margin:0; font-size:.88rem; color:#334155;">Total: <strong>{{ number_format((int) ($population['total'] ?? 0), 0, ',', '.') }}</strong></p>
                    </div>
                @else
                    <p style="margin:.6rem 0 0; color:#64748b; font-size:.86rem;">
                        Data penduduk belum tersedia untuk periode ini.
                    </p>
                @endif
            </article>
            @if (($moduleStates['complaints'] ?? false) === true)
                <article class="section-card stat-page-reveal" style="padding:1rem;">
                    <h3 style="margin:0; color:#123e74;">Status Pengaduan</h3>
                    <div style="margin-top:.6rem; min-height:220px;">
                        <canvas id="complaintStatusChart" height="220"></canvas>
                    </div>
                    <div style="margin-top:.6rem; display:grid; gap:.45rem;">
                        @foreach (['baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $key => $label)
                            <p style="margin:0; font-size:.88rem; color:#334155;">{{ $label }}: <strong>{{ number_format((int) ($complaintByStatus[$key] ?? 0), 0, ',', '.') }}</strong></p>
                        @endforeach
                    </div>
                </article>
                <article class="section-card stat-page-reveal" style="padding:1rem;">
                    <h3 style="margin:0 0 .6rem; color:#123e74;">Kategori Pengaduan Teratas</h3>
                    <canvas id="complaintCategoryChart" height="220"></canvas>
                </article>
            @endif
            @if (($moduleStates['infographics'] ?? false) === true)
                <article class="section-card stat-page-reveal" style="padding:1rem;">
                    <h3 style="margin:0; color:#123e74;">Komposisi Aset Desa</h3>
                    <div style="margin-top:.6rem; min-height:220px;">
                        <canvas id="assetTypeChart" height="220"></canvas>
                    </div>
                    @if (collect($assetTypeStats ?? [])->isNotEmpty())
                        <div style="margin-top:.6rem; display:grid; gap:.45rem;">
                            @foreach (($assetTypeStats ?? []) as $asset)
                                <p style="margin:0; font-size:.88rem; color:#334155; display:flex; align-items:center; justify-content:space-between; gap:.5rem;">
                                    <span style="display:inline-flex; align-items:center; gap:.45rem;">
                                        <i style="width:10px; height:10px; border-radius:999px; background:{{ $asset['color'] }}; display:inline-block;"></i>
                                        {{ $asset['label'] }}
                                    </span>
                                    <strong>{{ number_format((int) $asset['total'], 0, ',', '.') }}</strong>
                                </p>
                            @endforeach
                        </div>
                    @else
                        <p style="margin-top:.6rem; color:#64748b; font-size:.86rem;">Belum ada data aset desa yang dipublikasikan.</p>
                    @endif
                </article>
            @endif
        </div>

        <div class="split">
            <article class="section-card stat-page-reveal" style="padding:1rem;">
                <h3 style="margin:0 0 .6rem; color:#123e74;">{{ $startYear === $endYear ? 'Tren Bulanan '.$startYear : 'Tren Tahunan '.$startYear.' - '.$endYear }}</h3>
                <canvas id="monthlyTrendChart" height="220"></canvas>
            </article>
        </div>

        @if (($infographicIndicators ?? collect())->isNotEmpty())
            <div class="page-section-stack">
                @foreach ($infographicIndicators as $indicatorGroup)
                    <article class="section-card stat-page-reveal" style="padding:1rem;">
                        <h3 style="margin:0; color:#123e74;">{{ $indicatorGroup['label'] }}</h3>
                        <p style="margin:.25rem 0 .75rem; color:#64748b; font-size:.86rem;">Indikator agregat desa berdasarkan data yang dipublikasikan.</p>
                        <div style="overflow-x:auto;">
                            <table style="width:100%; border-collapse:collapse; font-size:.88rem;">
                                <thead>
                                    <tr style="border-bottom:1px solid #e2e8f0;">
                                        <th style="text-align:left; padding:.6rem;">Indikator</th>
                                        <th style="text-align:left; padding:.6rem;">Nilai</th>
                                        <th style="text-align:left; padding:.6rem;">Tahun</th>
                                        <th style="text-align:left; padding:.6rem;">Sumber</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($indicatorGroup['items'] as $indicator)
                                        <tr style="border-bottom:1px solid #f1f5f9;">
                                            <td style="padding:.6rem;">{{ $indicator['title'] }}</td>
                                            <td style="padding:.6rem;">
                                                {{ ($indicator['value'] ?? '') !== '' ? $indicator['value'] : 'Data belum tersedia' }}
                                                {{ $indicator['unit'] ?? '' }}
                                            </td>
                                            <td style="padding:.6rem;">{{ $indicator['year'] ?? 'Terkini' }}</td>
                                            <td style="padding:.6rem;">{{ $indicator['source'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
<style>
    .stat-page-reveal {
        opacity: 0;
        transform: translateY(14px);
        animation: statFadeIn .65s ease forwards;
    }
    .stat-page-kpi .stat-page-reveal:nth-child(1) { animation-delay: .05s; }
    .stat-page-kpi .stat-page-reveal:nth-child(2) { animation-delay: .09s; }
    .stat-page-kpi .stat-page-reveal:nth-child(3) { animation-delay: .13s; }
    .stat-page-kpi .stat-page-reveal:nth-child(4) { animation-delay: .17s; }
    .stat-page-kpi .stat-page-reveal:nth-child(5) { animation-delay: .21s; }
    .stat-page-kpi .stat-page-reveal:nth-child(6) { animation-delay: .25s; }
    .stat-page-kpi .stat-page-reveal:nth-child(7) { animation-delay: .29s; }
    .page-hero-filter--statistics {
        min-width: min(680px, 100%);
    }
    .page-hero-filter--statistics .stat-filter-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(120px, 1fr)) auto auto auto auto;
        align-items: end;
        gap: .55rem;
    }
    .page-hero-filter--statistics label {
        color: #dbeaff;
    }
    .page-hero-filter--statistics select {
        margin-top: .35rem;
        width: 100%;
        border: 0;
        border-radius: 10px;
        padding: .5rem .6rem;
        color: #123e74;
        background: #fff;
        font-size: .8rem;
    }
    .page-hero-filter--statistics button,
    .page-hero-filter--statistics a {
        border: 0;
        border-radius: 10px;
        padding: .5rem .72rem;
        background: #fff;
        color: #0d3f7d;
        font-size: .8rem;
        font-weight: 800;
        text-decoration: none;
        min-height: 38px;
        white-space: nowrap;
    }
    .page-hero-filter--statistics a {
        background: #ffc107;
        color: #123e74;
    }
    @media (max-width: 760px) {
        .page-hero-filter--statistics .stat-filter-grid {
            grid-template-columns: 1fr;
        }
    }
    @keyframes statFadeIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

@php
    $complaintCategoryChartData = $complaintByCategory
        ->map(function ($row) {
            return [
                'label' => \Illuminate\Support\Str::headline((string) $row->category),
                'value' => (int) $row->total,
            ];
        })
        ->values();

    $complaintStatusChartData = [
        'baru' => (int) ($complaintByStatus['baru'] ?? 0),
        'diproses' => (int) ($complaintByStatus['diproses'] ?? 0),
        'selesai' => (int) ($complaintByStatus['selesai'] ?? 0),
        'ditolak' => (int) ($complaintByStatus['ditolak'] ?? 0),
    ];

    $populationChartData = [
        'male' => (int) ($population['male'] ?? 0),
        'female' => (int) ($population['female'] ?? 0),
    ];
    $assetTypeChartData = collect($assetTypeStats ?? [])->values();
@endphp
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    (function () {
        if (typeof Chart === 'undefined') return;

        const populationCanvas = document.getElementById('populationChart');
        const complaintStatusCanvas = document.getElementById('complaintStatusChart');
        const categoryCanvas = document.getElementById('complaintCategoryChart');
        const assetTypeCanvas = document.getElementById('assetTypeChart');
        const trendCanvas = document.getElementById('monthlyTrendChart');

        const categoryData = @json($complaintCategoryChartData);
        const monthly = @json($trend ?? $monthly);
        const complaintStatus = @json($complaintStatusChartData);
        const populationData = @json($populationChartData);
        const assetTypeData = @json($assetTypeChartData);

        const baseAnimation = {
            duration: 1200,
            easing: 'easeOutQuart',
        };

        if (populationCanvas) {
            new Chart(populationCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Laki-laki', 'Perempuan'],
                    datasets: [{
                        data: [populationData.male, populationData.female],
                        backgroundColor: ['#1d4ed8', '#60a5fa'],
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } },
                    animation: baseAnimation,
                    cutout: '62%',
                }
            });
        }

        if (complaintStatusCanvas) {
            new Chart(complaintStatusCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Baru', 'Diproses', 'Selesai', 'Ditolak'],
                    datasets: [{
                        data: [complaintStatus.baru, complaintStatus.diproses, complaintStatus.selesai, complaintStatus.ditolak],
                        backgroundColor: ['#f59e0b', '#2563eb', '#059669', '#dc2626'],
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } },
                    animation: { ...baseAnimation, delay: 180 },
                    cutout: '58%',
                }
            });
        }

        if (categoryCanvas) {
            new Chart(categoryCanvas, {
                type: 'bar',
                data: {
                    labels: categoryData.map(item => item.label),
                    datasets: [{
                        label: 'Jumlah Aduan',
                        data: categoryData.map(item => item.value),
                        backgroundColor: '#1b63bf',
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    animation: { ...baseAnimation, delay: 320 },
                }
            });
        }

        if (assetTypeCanvas) {
            new Chart(assetTypeCanvas, {
                type: 'doughnut',
                data: {
                    labels: assetTypeData.map(item => item.label),
                    datasets: [{
                        data: assetTypeData.map(item => item.total),
                        backgroundColor: assetTypeData.map(item => item.color || '#64748b'),
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } },
                    animation: { ...baseAnimation, delay: 360 },
                    cutout: '58%',
                }
            });
        }

        if (trendCanvas) {
            new Chart(trendCanvas, {
                type: 'line',
                data: {
                    labels: monthly.map(item => item.label),
                    datasets: [
                        ...(@json($moduleStates['complaints'] ?? false) ? [{ label: 'Pengaduan', data: monthly.map(item => item.complaints), borderColor: '#dc2626', backgroundColor: 'rgba(220,38,38,.12)', tension: .35, fill: true }] : []),
                        ...(@json($moduleStates['services'] ?? false) ? [{ label: 'Pengajuan Layanan', data: monthly.map(item => item.service_requests), borderColor: '#7c3aed', backgroundColor: 'rgba(124,58,237,.10)', tension: .35, fill: false }] : []),
                        ...(@json($moduleStates['news'] ?? false) ? [{ label: 'Berita', data: monthly.map(item => item.news), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,.10)', tension: .35, fill: false }] : []),
                        ...(@json($moduleStates['agendas'] ?? false) ? [{ label: 'Agenda', data: monthly.map(item => item.agendas), borderColor: '#059669', backgroundColor: 'rgba(5,150,105,.10)', tension: .35, fill: false }] : []),
                        ...(@json($moduleStates['announcements'] ?? false) ? [{ label: 'Pengumuman', data: monthly.map(item => item.announcements), borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,.10)', tension: .35, fill: false }] : []),
                        ...(@json($moduleStates['galleries'] ?? false) ? [{ label: 'Galeri', data: monthly.map(item => item.galleries), borderColor: '#0ea5e9', backgroundColor: 'rgba(14,165,233,.10)', tension: .35, fill: false }] : []),
                    ]
                },
                options: { responsive: true, animation: { ...baseAnimation, delay: 450 } }
            });
        }
    })();
</script>
@endsection
