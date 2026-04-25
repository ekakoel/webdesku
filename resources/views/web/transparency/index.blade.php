@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid">
        <article class="transparency-hero section-card">
            <div>
                <small>Transparansi Desa</small>
                <h1>Informasi APBDes dan Laporan Publik</h1>
                <p>Publikasi data keuangan dan dokumen informasi desa sebagai bentuk akuntabilitas kepada masyarakat.</p>
            </div>
            <form method="GET" action="{{ route('transparansi') }}" class="transparency-year-filter">
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
        </article>

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
                <h3>{{ $transparencyItems->count() }}</h3>
            </article>
        </div>
    </div>
</section>

<section class="section-wrap">
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
    </div>
</section>

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
                            <h3>{{ \App\Models\VillageTransparencyItem::categoryOptions()[$category] ?? ucfirst($category) }}</h3>
                            <ul>
                                @foreach ($rows as $row)
                                    <li>
                                        <div>
                                            <strong>{{ $row->title }}</strong>
                                            @if ($row->fiscal_year)
                                                <small>Tahun {{ $row->fiscal_year }}</small>
                                            @endif
                                        </div>
                                        @if ($row->document_url)
                                            <a href="{{ $row->document_url }}" target="_blank" rel="noopener">Lihat</a>
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
@endsection

