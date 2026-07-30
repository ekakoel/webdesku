@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid page-section-stack">
        <article class="page-hero section-card">
            <div>
                <small>Transparansi Desa</small>
                <h1>Informasi APBDes dan Laporan Publik</h1>
                <p>Publikasi data keuangan dan dokumen informasi desa sebagai bentuk akuntabilitas kepada masyarakat.</p>
            </div>
            @if ($tab === 'apbdes')
                <form method="GET" action="{{ route('transparansi') }}" class="transparency-year-filter page-hero-filter page-hero-filter--year">
                    <input type="hidden" name="tab" value="apbdes">
                    <label for="year">Tahun Anggaran</label>
                    <div>
                        <select id="year" name="year">
                            @foreach ($apbdesYears as $yearOption)
                                <option value="{{ $yearOption }}" @selected($selectedYear === (int) $yearOption)>{{ $yearOption }}</option>
                            @endforeach
                        </select>
                        <button type="submit">Tampilkan</button>
                    </div>
                </form>
            @endif
        </article>

        <div class="infographic-tabs">
            <a href="{{ route('transparansi', ['tab' => 'apbdes', 'year' => $selectedYear]) }}" class="{{ $tab === 'apbdes' ? 'is-active' : '' }}">APBDes</a>
            <a href="{{ route('transparansi', ['tab' => 'dokumen', 'year' => $selectedYear]) }}" class="{{ $tab === 'dokumen' ? 'is-active' : '' }}">Dokumen Transparansi</a>
        </div>

        @if ($tab === 'apbdes')
            <div class="transparency-kpi-grid">
                <article class="section-card transparency-kpi-card">
                    <small>Total Pendapatan</small>
                    <h3>Rp {{ number_format((int) ($apbdesSummary['pendapatan'] ?? 0), 0, ',', '.') }}</h3>
                </article>
                <article class="section-card transparency-kpi-card">
                    <small>Total Belanja</small>
                    <h3>Rp {{ number_format((int) ($apbdesSummary['belanja'] ?? 0), 0, ',', '.') }}</h3>
                </article>
                <article class="section-card transparency-kpi-card">
                    <small>Total Pembiayaan</small>
                    <h3>Rp {{ number_format((int) ($apbdesSummary['pembiayaan'] ?? 0), 0, ',', '.') }}</h3>
                </article>
                <article class="section-card transparency-kpi-card">
                    <small>Dokumen/Laporan</small>
                    <h3>{{ number_format((int) ($apbdesDocumentTotal ?? 0), 0, ',', '.') }}</h3>
                </article>
            </div>
        @endif
    </div>
</section>

@if ($tab === 'apbdes')
    <section class="section-wrap section-wrap--last">
        <div class="container-grid">
            <article class="section-card transparency-table-card">
                <div class="section-head">
                    <h2>Rincian APBDes {{ $selectedYear ?? '-' }}</h2>
                </div>
                @if ($apbdesItems->isEmpty())
                    <p class="transparency-empty">Data APBDes belum tersedia untuk tahun ini.</p>
                @else
                    <div class="transparency-table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tipe</th>
                                    <th>Kategori</th>
                                    <th>Nominal</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($apbdesItems as $item)
                                    <tr>
                                        <td>{{ $item->typeLabel() }}</td>
                                        <td>{{ $item->category }}</td>
                                        <td>Rp {{ number_format((int) $item->amount, 0, ',', '.') }}</td>
                                        <td>{{ $item->notes ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </article>
            <article class="section-card transparency-doc-card">
                <div class="section-head">
                    <h2>Dokumen/Laporan APBDes {{ $selectedYear ?? '-' }}</h2>
                </div>
                @if (($apbdesDocuments ?? collect())->isEmpty())
                    <p class="transparency-empty">Belum ada dokumen/laporan APBDes untuk tahun ini.</p>
                @else
                    <div class="transparency-doc-grid">
                        <article class="transparency-doc-group">
                            <h3>Arsip Dokumen APBDes</h3>
                            <ul>
                                @foreach ($apbdesDocuments as $row)
                                    @php
                                        $docLink = $row->documentLink();
                                    @endphp
                                    <li>
                                        <div>
                                            <strong>{{ $row->title }}</strong>
                                            @if ($row->fiscal_year)
                                                <small>Tahun {{ $row->fiscal_year }}</small>
                                            @endif
                                        </div>
                                        @if ($docLink)
                                            <a href="{{ $docLink }}" target="_blank" rel="noopener">Lihat</a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </article>
                    </div>
                @endif
            </article>
        </div>
    </section>
@endif

@if ($tab === 'dokumen')
    <section class="section-wrap section-wrap--last">
        <div class="container-grid">
            <article class="section-card transparency-doc-card">
                <div class="section-head">
                    <h2>Dokumen Transparansi</h2>
                </div>
                @if ($transparencyCategories->isEmpty())
                    <p class="transparency-empty">Belum ada publikasi dokumen transparansi.</p>
                @else
                    <div class="transparency-doc-grid">
                        @foreach ($transparencyCategories as $category => $rows)
                            <article class="transparency-doc-group">
                                <h3>{{ \App\Models\VillageTransparencyDocument::categoryOptions()[$category] ?? ucfirst($category) }}</h3>
                                <ul>
                                    @foreach ($rows as $row)
                                        <li>
                                            <div>
                                                <strong>{{ $row->title }}</strong>
                                                @if ($row->fiscal_year)
                                                    <small>Tahun {{ $row->fiscal_year }}</small>
                                                @endif
                                            </div>
                                            @php
                                                $documentLink = $row->documentLink();
                                            @endphp
                                            @if ($documentLink)
                                                <a href="{{ $documentLink }}" target="_blank" rel="noopener">Lihat</a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </article>
                        @endforeach
                    </div>
                @endif
            </article>
        </div>
    </section>
@endif
@endsection
