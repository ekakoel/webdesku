<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pengajuan {{ $serviceRequest->ticket_code }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        .wrap { border: 1px solid #d1d5db; border-radius: 8px; padding: 18px; }
        .head { margin-bottom: 14px; border-bottom: 1px dashed #9ca3af; padding-bottom: 10px; }
        .head h1 { margin: 0; font-size: 18px; color: #0c3f7f; }
        .head p { margin: 4px 0 0; font-size: 12px; color: #4b5563; }
        .grid { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .grid td { padding: 6px 0; vertical-align: top; }
        .grid td:first-child { width: 170px; color: #4b5563; }
        .ticket { display: inline-block; margin-top: 6px; padding: 4px 10px; background: #e6f0ff; color: #0c3f7f; border-radius: 999px; font-weight: bold; }
        .note { margin-top: 14px; font-size: 11px; color: #4b5563; }
        .qr { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="head">
            <h1>Bukti Pengajuan Layanan Desa</h1>
            <p>{{ $serviceRequest->village?->name ?? 'Website Desa' }}</p>
            <span class="ticket">No. Tiket: {{ $serviceRequest->ticket_code }}</span>
        </div>

        <table class="grid">
            <tr><td>Layanan</td><td>: {{ $serviceRequest->service?->name ?? '-' }}</td></tr>
            <tr><td>Nama Pemohon</td><td>: {{ $serviceRequest->applicant_name }}</td></tr>
            <tr><td>NIK</td><td>: {{ $serviceRequest->nik }}</td></tr>
            <tr><td>No. KK</td><td>: {{ $serviceRequest->kk_number ?: '-' }}</td></tr>
            <tr><td>Telepon</td><td>: {{ $serviceRequest->phone }}</td></tr>
            <tr><td>Email</td><td>: {{ $serviceRequest->email ?: '-' }}</td></tr>
            <tr><td>Tanggal Pengajuan</td><td>: {{ $serviceRequest->submitted_at?->format('d M Y H:i') ?: '-' }}</td></tr>
            <tr><td>Status</td><td>: {{ ucfirst($serviceRequest->status) }}</td></tr>
        </table>

        <div class="qr">{!! $qrSvg !!}</div>
        <div class="note">
            Simpan dokumen ini sebagai bukti pengajuan. Tunjukkan nomor tiket saat konfirmasi di kantor desa.
        </div>
    </div>
</body>
</html>
