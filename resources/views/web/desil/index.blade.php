@extends('web.web')
@section('content')
    <section class="section-wrap">
        <div class="container-grid page-section-stack">
            <header class="page-hero section-card">
                <div><small>Data Kesejahteraan</small>
                    <h1>Analisis Desil</h1>
                    <p>Rekapitulasi KK berdasarkan desil kesejahteraan. Analisis ini bukan penetapan penerima bantuan.</p>
                </div>
                <div class="page-hero__actions"><a href="{{ route('desil.pdf', request()->query()) }}">Download PDF</a><a
                        href="{{ route('desil.excel', request()->query()) }}">Download Excel</a></div>
            </header>
            <form class="section-card grid gap-3 md:grid-cols-4" method="GET"><select class="form-control" name="start_year">
                    <option value="">Tahun awal</option>
                    @foreach ($yearOptions as $year)
                        <option value="{{ $year }}" @selected($startYear === $year)>{{ $year }}</option>
                    @endforeach
                </select>
                <select class="form-control" name="end_year">
                    <option value="">Tahun terbaru</option>
                    @foreach ($yearOptions as $year)
                        <option value="{{ $year }}" @selected($endYear === $year)>{{ $year }}</option>
                    @endforeach
                </select>
                <select class="form-control" name="hamlet_id">
                    <option value="">Semua Banjar</option>
                    @foreach ($hamlets as $hamlet)
                        <option value="{{ $hamlet->id }}" @selected(($filters['hamlet_id'] ?? null) == $hamlet->id)>{{ $hamlet->name }}</option>
                    @endforeach
                </select>
                <button class="form-control-button">Terapkan</button>
            </form>
            <div class="grid gap-4 md:grid-cols-4">
                @foreach ([['Total KK', $totalHouseholds], ['D1-D3 Prioritas', $priorityHouseholds], ['D1-D4 Rentan', $vulnerableHouseholds], ['Data perlu verifikasi', $qualityTotal]] as [$label, $value])
                    <article class="section-card">
                        <small>{{ $label }}</small><strong>{{ number_format($value, 0, ',', '.') }} KK</strong>
                    </article>
                @endforeach
            </div>
            <article class="section-card">
                <h2>Distribusi D1-D5</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Desil</th>
                            <th>Keterangan</th>
                            <th>KK</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($distribution as $row)
                            <tr>
                                <td>{{ $row['decile'] }}</td>
                                <td>{{ $row['label'] }}</td>
                                <td>{{ $row['total'] }}</td>
                                <td>{{ $row['percentage'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </article>
            <article class="section-card">
                <h2>Profil Kepala Keluarga</h2>
                @if ($genderDistribution->isNotEmpty())
                    <table>
                        <thead>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <th>Total KK</th>
                                <th>Persentase</th>
                                @foreach (['D1', 'D2', 'D3', 'D4', 'D5'] as $decile)
                                    <th>{{ $decile }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($genderDistribution as $row)
                                <tr>
                                    <td>{{ $row['gender'] }}</td>
                                    <td>{{ $row['total'] }}</td>
                                    <td>{{ $row['percentage'] }}%</td>
                                    @foreach (['D1', 'D2', 'D3', 'D4', 'D5'] as $decile)
                                        <td>{{ $row['items'][$decile] ?? 0 }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                </table>@else<p>Data jenis kelamin kepala keluarga belum tersedia.</p>
                @endif
            </article>
            <article class="section-card">
                <h2>Analisis Dusun/Banjar</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Banjar</th>
                            <th>D1</th>
                            <th>D2</th>
                            <th>D3</th>
                            <th>D4</th>
                            <th>D5</th>
                            <th>Total</th>
                            <th>D1-D3</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hamletDistribution as $row)
                            <tr>
                                <td>{{ $row->hamlet }}</td>
                                <td>{{ $row->d1 }}</td>
                                <td>{{ $row->d2 }}</td>
                                <td>{{ $row->d3 }}</td>
                                <td>{{ $row->d4 }}</td>
                                <td>{{ $row->d5 }}</td>
                                <td>{{ $row->total }}</td>
                                <td>{{ $row->priority_percentage }}%</td>
                        </tr>@empty<tr>
                                <td colspan="8">Data belum tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </article>
            @if ($comparison)
                <article class="section-card">
                    <h2>Perubahan Distribusi Data Desil</h2>
                    <p>Perbandingan {{ $startYear }} ke {{ $endYear }}; perubahan ini tidak menunjukkan sebab atau
                        perpindahan kondisi individual.</p>
                    <table>
                        <thead>
                            <tr>
                                <th>Desil</th>
                                <th>{{ $startYear }}</th>
                                <th>{{ $endYear }}</th>
                                <th>Perubahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($comparison as $row)
                                <tr>
                                    <td>{{ $row['decile'] }}</td>
                                    <td>{{ $row['from'] }}</td>
                                    <td>{{ $row['to'] }}</td>
                                    <td>{{ $row['change'] > 0 ? '+' : '' }}{{ $row['change'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </article>
            @endif
            <article class="section-card">
                <h2>Kualitas Data</h2>
                <p>Banjar kosong: {{ $quality['missing_hamlet'] }} | Desil kosong/tidak valid:
                    {{ $quality['invalid_decile'] }} | Jenis kelamin tidak valid:
                    {{ $quality['invalid_gender'] }} | Luar wilayah: {{ $quality['outside_village'] }} | Ditandai perlu
                    verifikasi: {{ $quality['requires_verification'] }}</p>
            </article>
        </div>
    </section>
@endsection
