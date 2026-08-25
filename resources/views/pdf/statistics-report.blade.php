<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Statistik Pemerintahan Desa</title>
    <style>
        @page {
            margin: 24mm 16mm 22mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 11px;
            line-height: 1.45;
        }
        .letterhead {
            text-align: center;
            border-bottom: 3px double #111827;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }
        .letterhead-logo {
            width: 58px;
            height: 58px;
            object-fit: contain;
            margin-bottom: 6px;
        }
        .letterhead h1,
        .letterhead h2,
        .letterhead p {
            margin: 0;
        }
        .letterhead h1 {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: .4px;
        }
        .letterhead h2 {
            font-size: 14px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .letterhead p {
            font-size: 10px;
            margin-top: 4px;
        }
        .report-title {
            text-align: center;
            margin: 18px 0;
        }
        .report-title h3 {
            margin: 0;
            font-size: 14px;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .report-title p {
            margin: 5px 0 0;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-pill {
            display: inline-block;
            border: 1px solid #64748b;
            border-radius: 999px;
            padding: 3px 10px;
            font-size: 10px;
            font-weight: 700;
            color: #334155;
        }
        .section {
            margin-top: 16px;
            page-break-inside: avoid;
        }
        .section h4 {
            margin: 0 0 8px;
            font-size: 12px;
            text-transform: uppercase;
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 6px 7px;
            vertical-align: top;
        }
        th {
            background: #e2e8f0;
            font-weight: 700;
            text-align: left;
        }
        thead {
            display: table-header-group;
        }
        .meta-table th {
            width: 32%;
        }
        .text-right {
            text-align: right;
        }
        .note {
            border: 1px solid #fde68a;
            background: #fffbeb;
            padding: 8px 10px;
            margin-top: 8px;
        }
        .signature {
            width: 260px;
            margin-left: auto;
            margin-top: 28px;
            text-align: center;
            page-break-inside: avoid;
        }
        .signature-space {
            height: 58px;
        }
        .footer {
            position: fixed;
            bottom: -14mm;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #475569;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="footer">
        Laporan Statistik Pemerintahan Desa | Periode: {{ $periodDateLabel }} | Dicetak pada: {{ $generatedAt->translatedFormat('d F Y H:i') }}
    </div>

    <header class="letterhead">
        @if (!empty($villageLogoPath))
            <img class="letterhead-logo" src="{{ $villageLogoPath }}" alt="Logo Desa">
        @endif
        <h1>Pemerintah Desa {{ $village?->name ?? '[NAMA DESA]' }}</h1>
        <h2>Kecamatan {{ $village?->district ?? '[NAMA KECAMATAN]' }}</h2>
        <h2>{{ $village?->city ? 'Kabupaten/Kota '.$village->city : 'Kabupaten/Kota [NAMA KABUPATEN/KOTA]' }}</h2>
        <h2>Provinsi {{ $village?->province ?? '[NAMA PROVINSI]' }}</h2>
        <p>{{ $village?->address ?? '[ALAMAT KANTOR DESA]' }}</p>
    </header>

    <section class="report-title">
        <span class="status-pill">LAPORAN REKAPITULASI SISTEM</span>
        <h3>Laporan Statistik Pemerintahan Desa</h3>
        <p>Periode {{ $periodLabel }}</p>
    </section>

    <section class="section">
        <h4>Identitas Laporan</h4>
        <table class="meta-table">
            <tbody>
                <tr>
                    <th>Nama Desa</th>
                    <td>{{ $village?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Kecamatan</th>
                    <td>{{ $village?->district ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Kabupaten/Kota</th>
                    <td>{{ $village?->city ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Provinsi</th>
                    <td>{{ $village?->province ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Periode Laporan</th>
                    <td>{{ $periodDateLabel }}</td>
                </tr>
                <tr>
                    <th>Tanggal Cetak</th>
                    <td>{{ $generatedAt->translatedFormat('d F Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Sumber Data</th>
                    <td>Sistem Informasi Desa {{ $village?->name ?? 'Pemerintah Desa' }}</td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="section">
        <h4>A. Ringkasan Statistik</h4>
        <table>
            <thead>
                <tr>
                    <th>Indikator</th>
                    <th class="text-right">Nilai</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kpis as $kpi)
                    <tr>
                        <td>{{ $kpi['label'] }}</td>
                        <td class="text-right">{{ $kpi['value'] }}</td>
                        <td>{{ $kpi['scope'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if (!$hasPeriodData)
            <div class="note">Tidak terdapat data periodik pada periode yang dipilih.</div>
        @endif
    </section>

    <section class="section">
        <h4>B. Statistik Berdasarkan Periode</h4>
        <table>
            <thead>
                <tr>
                    <th>Periode</th>
                    @if ($moduleStates['complaints'] ?? false)
                        <th class="text-right">Pengaduan</th>
                    @endif
                    @if ($moduleStates['services'] ?? false)
                        <th class="text-right">Pengajuan Layanan</th>
                    @endif
                    @if ($moduleStates['news'] ?? false)
                        <th class="text-right">Berita</th>
                    @endif
                    @if ($moduleStates['agendas'] ?? false)
                        <th class="text-right">Agenda</th>
                    @endif
                    @if ($moduleStates['announcements'] ?? false)
                        <th class="text-right">Pengumuman</th>
                    @endif
                    @if ($moduleStates['galleries'] ?? false)
                        <th class="text-right">Galeri</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($trend as $row)
                    <tr>
                        <td>{{ $row['label'] }}</td>
                        @if ($moduleStates['complaints'] ?? false)
                            <td class="text-right">{{ number_format((int) $row['complaints'], 0, ',', '.') }}</td>
                        @endif
                        @if ($moduleStates['services'] ?? false)
                            <td class="text-right">{{ number_format((int) $row['service_requests'], 0, ',', '.') }}</td>
                        @endif
                        @if ($moduleStates['news'] ?? false)
                            <td class="text-right">{{ number_format((int) $row['news'], 0, ',', '.') }}</td>
                        @endif
                        @if ($moduleStates['agendas'] ?? false)
                            <td class="text-right">{{ number_format((int) $row['agendas'], 0, ',', '.') }}</td>
                        @endif
                        @if ($moduleStates['announcements'] ?? false)
                            <td class="text-right">{{ number_format((int) $row['announcements'], 0, ',', '.') }}</td>
                        @endif
                        @if ($moduleStates['galleries'] ?? false)
                            <td class="text-right">{{ number_format((int) $row['galleries'], 0, ',', '.') }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    @if (($moduleStates['complaints'] ?? false) && $complaintByStatus->isNotEmpty())
        <section class="section">
            <h4>C. Statistik Pengaduan</h4>
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $key => $label)
                        <tr>
                            <td>{{ $label }}</td>
                            <td class="text-right">{{ number_format((int) ($complaintByStatus[$key] ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($complaintByCategory->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($complaintByCategory as $row)
                            <tr>
                                <td>{{ \Illuminate\Support\Str::headline((string) $row->category) }}</td>
                                <td class="text-right">{{ number_format((int) $row->total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    @endif

    @if (($infographicIndicators ?? collect())->isNotEmpty())
        @foreach ($infographicIndicators as $indicatorGroup)
            <section class="section">
                <h4>Indikator {{ $indicatorGroup['label'] }}</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Indikator</th>
                            <th>Nilai</th>
                            <th>Tahun</th>
                            <th>Sumber</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($indicatorGroup['items'] as $indicator)
                            <tr>
                                <td>{{ $indicator['title'] }}</td>
                                <td>{{ ($indicator['value'] ?? '') !== '' ? $indicator['value'] : 'Data belum tersedia' }} {{ $indicator['unit'] ?? '' }}</td>
                                <td>{{ $indicator['year'] ?? 'Terkini' }}</td>
                                <td>{{ $indicator['source'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endforeach
    @endif

    <section class="section">
        <h4>Sumber Data dan Metodologi</h4>
        <p>Data dalam laporan ini diperoleh dari data yang tersimpan pada Sistem Informasi Desa {{ $village?->name ?? 'Pemerintah Desa' }} dan dihitung berdasarkan periode yang dipilih oleh pengguna.</p>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Metode Perhitungan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($methodology as $label => $description)
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="section">
        <h4>Dasar Penyusunan dan Catatan Penting</h4>
        <p>Data statistik disusun berdasarkan data yang tersedia dalam Sistem Informasi Desa dan digunakan sebagai bahan informasi serta administrasi Pemerintahan Desa.</p>
        <div class="note">Laporan ini merupakan hasil rekapitulasi data pada Sistem Informasi Desa berdasarkan periode yang dipilih. Keabsahan dan penggunaan laporan sebagai dokumen administrasi pemerintahan mengikuti proses verifikasi dan pengesahan oleh Pemerintah Desa sesuai ketentuan yang berlaku.</div>
    </section>

    <section class="signature">
        <p>{{ $village?->city ?? '[TEMPAT]' }}, {{ $generatedAt->translatedFormat('d F Y') }}</p>
        <p>Mengetahui,<br>Kepala Desa {{ $village?->name ?? '[NAMA DESA]' }}</p>
        <div class="signature-space"></div>
        <p><strong>{{ $village?->head_name ?: '[NAMA KEPALA DESA]' }}</strong></p>
    </section>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font('DejaVu Sans', 'normal');
            $pdf->page_text(500, 812, 'Halaman {PAGE_NUM} dari {PAGE_COUNT}', $font, 8, [71, 85, 105]);
        }
    </script>
</body>
</html>
