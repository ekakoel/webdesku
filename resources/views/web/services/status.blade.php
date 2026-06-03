@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid container-grid--compact page-section-stack">
        <article class="page-hero section-card">
            <div>
                <small>Layanan Desa</small>
                <h1>Cek Status Pengajuan Layanan</h1>
                <p>Masukkan nomor tiket untuk melihat progres pengajuan Anda.</p>
            </div>
        </article>

        <article class="section-card" style="padding: 1rem;">
            <form method="GET" action="{{ route('services.status') }}" style="display: grid; gap: .6rem; grid-template-columns: 1fr auto;">
                <input type="text" name="ticket" value="{{ $ticket }}" placeholder="Contoh: LYN-260222-ABCDE" class="form-control">
                <button type="submit" class="form-control-button">Cek Status</button>
            </form>
        </article>

        @if ($ticket !== '')
            <article class="section-card" style="padding: 1rem;">
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
