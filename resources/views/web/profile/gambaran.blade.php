@extends('web.web')

@section('content')
@php
    $g = $gambaran ?? [];
    $pageMeta = $g['_page'] ?? [];
    $penduduk = $g['penduduk'] ?? ['kk' => 0, 'male' => 0, 'female' => 0];
    $jumlahPenduduk = (int) ($penduduk['male'] ?? 0) + (int) ($penduduk['female'] ?? 0);
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
        <div class="stats-grid stats-grid--wide">
            <article class="section-card stat-card">
                <h3>{{ number_format($jumlahPenduduk, 0, ',', '.') }} Jiwa</h3>
                <p>Total Penduduk</p>
            </article>
            <article class="section-card stat-card">
                <h3>{{ number_format((int) ($penduduk['male'] ?? 0), 0, ',', '.') }} Jiwa</h3>
                <p>Penduduk Laki-laki</p>
            </article>
            <article class="section-card stat-card">
                <h3>{{ number_format((int) ($penduduk['female'] ?? 0), 0, ',', '.') }} Jiwa</h3>
                <p>Penduduk Perempuan</p>
            </article>
            <article class="section-card stat-card">
                <h3>{{ number_format((int) ($penduduk['kk'] ?? 0), 0, ',', '.') }} KK</h3>
                <p>Kepala Keluarga</p>
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
    <div class="container-grid split">
        <article class="section-card budget-card">
            <h2>Grafik Komposisi Penduduk</h2>
            <div class="demographics-card__chart-wrap" style="min-height:260px; margin-top:.65rem;">
                <canvas id="populationCompositionChart"></canvas>
            </div>
        </article>
        <article class="section-card budget-card">
            <h2>Grafik Pendidikan</h2>
            <div class="demographics-card__chart-wrap" style="min-height:260px; margin-top:.65rem;">
                <canvas id="educationChart"></canvas>
            </div>
        </article>
    </div>
</section>

<section class="section-wrap">
    <div class="container-grid split">
        <article class="section-card budget-card">
            <h2>Grafik Agama</h2>
            <div class="demographics-card__chart-wrap" style="min-height:260px; margin-top:.65rem;">
                <canvas id="religionChart"></canvas>
            </div>
        </article>
        <article class="section-card budget-card">
            <h2>Grafik Mata Pencaharian</h2>
            <div class="demographics-card__chart-wrap" style="min-height:260px; margin-top:.65rem;">
                <canvas id="jobsChart"></canvas>
            </div>
        </article>
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

        const penduduk = @json($g['penduduk'] ?? ['male' => 0, 'female' => 0, 'kk' => 0]);
        const pendidikan = @json($g['pendidikan'] ?? []);
        const agama = @json($g['agama'] ?? []);
        const pekerjaan = @json($g['pekerjaan'] ?? []);

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

        createBar('educationChart', pendidikan.map((x) => x.label), pendidikan.map((x) => x.value), '#0f5e9f');
        createBar('jobsChart', pekerjaan.map((x) => x.label), pekerjaan.map((x) => x.value), '#16a34a');

        const religionEl = document.getElementById('religionChart');
        if (religionEl) {
            new Chart(religionEl, {
                type: 'polarArea',
                data: {
                    labels: agama.map((x) => x.label),
                    datasets: [{
                        data: agama.map((x) => x.value),
                        backgroundColor: ['#f59e0b', '#06b6d4', '#10b981', '#3b82f6', '#8b5cf6'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${format(ctx.parsed.r)} orang` } }
                    }
                }
            });
        }
    })();
</script>
@endsection
