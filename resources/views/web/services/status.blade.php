@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid" style="max-width: 860px;">
        <h1 style="font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 800; margin: 0;">Cek Status Pengajuan Layanan</h1>
        <p style="margin-top: .45rem; color: #4b5563;">Masukkan nomor tiket untuk melihat progres pengajuan Anda.</p>

        <article class="section-card" style="margin-top: 1rem; padding: 1rem;">
            <form method="GET" action="{{ route('services.status') }}" style="display: grid; gap: .6rem; grid-template-columns: 1fr auto;">
                <input type="text" name="ticket" value="{{ $ticket }}" placeholder="Contoh: LYN-260222-ABCDE" style="border:1px solid #cddff8; border-radius:10px; padding:.62rem .75rem;">
                <button type="submit" style="border:0; border-radius:10px; padding:.62rem 1rem; background:#0c3f7f; color:#fff; font-weight:700;">Cek Status</button>
            </form>
        </article>

        @if ($ticket !== '')
            <article class="section-card" style="margin-top: .9rem; padding: 1rem;">
                @if ($serviceRequest)
                    <h2 style="margin: 0; font-size: 1.05rem;">Nomor Tiket: {{ $serviceRequest->ticket_code }}</h2>
                    <p style="margin: .4rem 0 0; color:#4b5563;">Layanan: <strong>{{ $serviceRequest->service?->name ?? '-' }}</strong></p>
                    <p style="margin: .25rem 0 0; color:#4b5563;">Status: <strong>{{ ucfirst($serviceRequest->status) }}</strong></p>
                    <p style="margin: .25rem 0 0; color:#4b5563;">Tanggal Pengajuan: {{ $serviceRequest->submitted_at?->format('d M Y H:i') }}</p>
                    @if ($serviceRequest->status_note)
                        <p style="margin: .45rem 0 0; color:#1f2937;">Catatan Aparat: {{ $serviceRequest->status_note }}</p>
                    @endif
                    <div style="margin-top: .7rem;">
                        <a href="{{ route('services.receipt', $serviceRequest->public_token) }}" class="text-link" target="_blank">Cetak Bukti Pengajuan PDF</a>
                    </div>
                @else
                    <p style="margin: 0; color:#b91c1c;">Nomor tiket tidak ditemukan. Pastikan format tiket benar.</p>
                @endif
            </article>
        @endif
    </div>
</section>
@endsection
