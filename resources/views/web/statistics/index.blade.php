@extends('web.web')

@section('content')
@php
    $moduleStates = $moduleStates ?? [
        'complaints' => false,
        'news' => false,
        'agendas' => false,
        'infographics' => false,
    ];
@endphp
<section class="section-wrap">
    <div class="container-grid page-section-stack">
        <article class="page-hero section-card">
            <div>
                <small>Statistik Desa</small>
                <h1>Statistik {{ $village?->name ?? 'Desa' }}</h1>
                <p>Ringkasan indikator penting desa dan tren konten publik per tahun.</p>
            </div>
            <div class="page-hero__actions">
                <form method="GET" class="page-hero-filter page-hero-filter--year">
                    <label for="statisticYear">Tahun</label>
                    <div>
                        <select id="statisticYear" name="year">
                            @foreach ($yearOptions as $yearOption)
                                <option value="{{ $yearOption }}" @selected($year === (int) $yearOption)>{{ $yearOption }}</option>
                            @endforeach
                        </select>
                        <button type="submit">Tampilkan</button>
                    </div>
                </form>
            </div>
        </article>

        <div class="stats-grid stats-grid--wide stat-page-kpi">
            @foreach ($kpis as $kpi)
                <article class="section-card stat-card stat-page-reveal">
                    <p style="margin:0; color:#5d708b; font-size:.8rem;">{{ $kpi['label'] }}</p>
                    <h3 style="margin:.25rem 0 0;">{{ $kpi['value'] }}</h3>
                </article>
            @endforeach
        </div>

        <div class="split" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
            <article class="section-card stat-page-reveal" style="padding:1rem;">
                <h3 style="margin:0; color:#123e74;">Komposisi Penduduk</h3>
                <div style="margin-top:.6rem; min-height:220px;">
                    <canvas id="populationChart" height="220"></canvas>
                </div>
                <div style="margin-top:.6rem; display:grid; gap:.45rem;">
                    <p style="margin:0; font-size:.88rem; color:#334155;">Laki-laki: <strong>{{ number_format((int) ($population['male'] ?? 0), 0, ',', '.') }}</strong></p>
                    <p style="margin:0; font-size:.88rem; color:#334155;">Perempuan: <strong>{{ number_format((int) ($population['female'] ?? 0), 0, ',', '.') }}</strong></p>
                    <p style="margin:0; font-size:.88rem; color:#334155;">Total: <strong>{{ number_format((int) ($population['total'] ?? 0), 0, ',', '.') }}</strong></p>
                </div>
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
                <h3 style="margin:0 0 .6rem; color:#123e74;">Tren Bulanan {{ $year }}</h3>
                <canvas id="monthlyTrendChart" height="220"></canvas>
            </article>
        </div>
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
        const monthly = @json($monthly);
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
                        ...(@json($moduleStates['news'] ?? false) ? [{ label: 'Berita', data: monthly.map(item => item.news), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,.10)', tension: .35, fill: false }] : []),
                        ...(@json($moduleStates['agendas'] ?? false) ? [{ label: 'Agenda', data: monthly.map(item => item.agendas), borderColor: '#059669', backgroundColor: 'rgba(5,150,105,.10)', tension: .35, fill: false }] : []),
                    ]
                },
                options: { responsive: true, animation: { ...baseAnimation, delay: 450 } }
            });
        }
    })();
</script>
@endsection
