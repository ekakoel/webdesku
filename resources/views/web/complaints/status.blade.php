@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid page-section-stack">
        <article class="page-hero section-card">
            <div>
                <small>Pengaduan Desa</small>
                <h1>Cek Status Pengaduan</h1>
                <p>Masukkan kode tiket pengaduan untuk melihat progres tindak lanjut.</p>
            </div>
        </article>

        <article class="section-card" style="padding: 1rem;">
            <form method="GET" action="{{ route('complaints.status') }}" style="display: grid; gap: .6rem; grid-template-columns: 1fr auto;">
                <input type="text" name="ticket" value="{{ $ticket }}" placeholder="Contoh: ADU-260521-ABCDE" class="form-control">
                <button type="submit" class="form-control-button">Cek Status</button>
            </form>
        </article>

        @if ($ticket !== '')
            <article class="section-card" style="padding: 1rem;">
                @if ($complaint)
                    <h2 style="margin: 0; font-size: 1.05rem;">Kode Tiket: {{ $complaint->ticket_code }}</h2>
                    <p style="margin: .4rem 0 0; color:#4b5563;">Judul: <strong>{{ $complaint->title }}</strong></p>
                    <p style="margin: .25rem 0 0; color:#4b5563;">Kategori: <strong>{{ \Illuminate\Support\Str::headline($complaint->category) }}</strong></p>
                    <p style="margin: .25rem 0 0; color:#4b5563;">Status: <strong>{{ $complaint->statusLabel() }}</strong></p>
                    <p style="margin: .25rem 0 0; color:#4b5563;">Dikirim pada: {{ $complaint->submitted_at?->format('d M Y H:i') }}</p>
                    @if ($complaint->processed_at)
                        <p style="margin: .25rem 0 0; color:#4b5563;">Terakhir diproses: {{ $complaint->processed_at->format('d M Y H:i') }}</p>
                    @endif
                    @if ($complaint->status_note)
                        <p style="margin: .55rem 0 0; color:#1f2937;"><strong>Catatan Petugas:</strong> {{ $complaint->status_note }}</p>
                    @endif

                    @if (!empty($complaint->responses) && $complaint->responses->isNotEmpty())
                        <div style="margin-top: .8rem; border-top: 1px dashed #d7e5fb; padding-top: .7rem;">
                            <p style="margin:0; font-weight:700; color:#1f2937;">Riwayat Tindak Lanjut</p>
                            <div style="margin-top:.5rem; display:grid; gap:.5rem;">
                                @foreach ($complaint->responses as $response)
                                    <article style="border:1px solid #dbe7fb; border-radius:10px; padding:.55rem .65rem; background:#f8fbff;">
                                        <p style="margin:0; font-size:.78rem; color:#64748b;">
                                            {{ $response->created_at?->format('d M Y H:i') }}
                                        </p>
                                        <p style="margin:.2rem 0 0; font-size:.84rem; font-weight:700; color:#1f2937;">
                                            {{ ucfirst((string) ($response->from_status ?? 'baru')) }} → {{ ucfirst((string) $response->to_status) }}
                                        </p>
                                        @if ($response->note)
                                            <p style="margin:.2rem 0 0; font-size:.84rem; color:#334155;">{{ $response->note }}</p>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <p style="margin: 0; color:#b91c1c;">Kode tiket tidak ditemukan. Pastikan kode sudah benar.</p>
                @endif
            </article>
        @endif
    </div>
</section>
@endsection
