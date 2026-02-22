<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengajuan Layanan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1 { margin: 0; font-size: 17px; }
        .meta { margin: 5px 0 10px; color: #4b5563; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background: #eff6ff; color: #1e3a8a; }
    </style>
</head>
<body>
    <h1>Laporan Pengajuan Layanan Desa</h1>
    <div class="meta">
        Periode:
        {{ $period['from'] ?: '-' }} s/d {{ $period['to'] ?: '-' }} |
        Status: {{ $period['status'] }} |
        Dicetak: {{ now()->format('d M Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No Tiket</th>
                <th>Layanan</th>
                <th>Pemohon</th>
                <th>NIK</th>
                <th>Status</th>
                <th>Target SLA</th>
                <th>Waktu Proses</th>
                <th>Tanggal Pengajuan</th>
                <th>Tanggal Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                @php
                    $sla = (int) ($row->service?->sla_target_hours ?? 72);
                    $hours = ($row->submitted_at && $row->processed_at) ? round($row->submitted_at->diffInMinutes($row->processed_at) / 60, 2) : 0;
                @endphp
                <tr>
                    <td>{{ $row->ticket_code }}</td>
                    <td>{{ $row->service?->name ?? '-' }}</td>
                    <td>{{ $row->applicant_name }}</td>
                    <td>{{ $row->nik }}</td>
                    <td>{{ ucfirst($row->status) }}</td>
                    <td>{{ $sla }} jam</td>
                    <td>{{ number_format($hours, 2, ',', '.') }} jam</td>
                    <td>{{ $row->submitted_at?->format('d-m-Y H:i') ?? '-' }}</td>
                    <td>{{ $row->processed_at?->format('d-m-Y H:i') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Tidak ada data pada periode/filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
