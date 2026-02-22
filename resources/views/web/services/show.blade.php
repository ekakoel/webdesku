@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid split">
        <article class="section-card service-detail-card">
            <a href="{{ route('services') }}" class="text-link">Kembali ke Daftar Layanan</a>
            <span style="margin-left: .7rem; color:#9ca3af;">|</span>
            <a href="{{ route('services.status') }}" class="text-link" style="margin-left: .7rem;">Cek Status Pengajuan</a>
            <div class="service-detail-card__header">
                <span>{{ $service->icon ?: 'SV' }}</span>
                <div>
                    <h1>{{ $service->name }}</h1>
                    <p>{{ $service->description ?: 'Informasi layanan administrasi desa.' }}</p>
                    <p style="margin-top: .35rem; font-weight: 700; color: #0c3f7f;">Target SLA: {{ (int) ($service->sla_target_hours ?? 72) }} jam kerja</p>
                </div>
            </div>

            <div class="service-detail-card__cols">
                <section>
                    <h2>Persyaratan</h2>
                    @php($requirements = $service->requirementsList())
                    @if (count($requirements))
                        <ol>
                            @foreach ($requirements as $line)
                                <li>{{ $line }}</li>
                            @endforeach
                        </ol>
                    @else
                        <p>Persyaratan akan diinformasikan petugas saat verifikasi awal.</p>
                    @endif
                </section>
                <section>
                    <h2>Prosedur</h2>
                    @php($process = $service->processList())
                    @if (count($process))
                        <ol>
                            @foreach ($process as $line)
                                <li>{{ $line }}</li>
                            @endforeach
                        </ol>
                    @else
                        <p>Prosedur layanan akan diproses sesuai SOP administrasi desa.</p>
                    @endif
                </section>
            </div>
        </article>

        <article class="section-card service-apply-card">
            <h2>Form Pengajuan Layanan</h2>
            <p>Isi data sesuai dokumen kependudukan agar proses verifikasi cepat.</p>

            @if (session('status'))
                <div class="service-apply-card__alert">
                    {{ session('status') }}
                    @if (session('receipt_url'))
                        <div style="margin-top: .45rem;">
                            <a href="{{ session('receipt_url') }}" target="_blank" class="text-link" style="font-size: .85rem;">Cetak Bukti PDF + QR Tiket</a>
                        </div>
                    @endif
                </div>
            @endif

            <form action="{{ route('services.apply', $service->slug) }}" method="POST" enctype="multipart/form-data" class="service-apply-form">
                @csrf
                <label>
                    Nama Pemohon
                    <input type="text" name="applicant_name" value="{{ old('applicant_name') }}" required>
                    @error('applicant_name') <small>{{ $message }}</small> @enderror
                </label>
                <label>
                    NIK
                    <input type="text" name="nik" value="{{ old('nik') }}" inputmode="numeric" maxlength="16" required>
                    @error('nik') <small>{{ $message }}</small> @enderror
                </label>
                <label>
                    Nomor KK
                    <input type="text" name="kk_number" value="{{ old('kk_number') }}" inputmode="numeric" maxlength="16">
                    @error('kk_number') <small>{{ $message }}</small> @enderror
                </label>
                <label>
                    Nomor HP
                    <input type="text" name="phone" value="{{ old('phone') }}" required>
                    @error('phone') <small>{{ $message }}</small> @enderror
                </label>
                <label>
                    Email (opsional)
                    <input type="email" name="email" value="{{ old('email') }}">
                    @error('email') <small>{{ $message }}</small> @enderror
                </label>
                <label>
                    Alamat
                    <textarea name="address" rows="3" required>{{ old('address') }}</textarea>
                    @error('address') <small>{{ $message }}</small> @enderror
                </label>
                <label>
                    Keterangan Pengajuan
                    <textarea name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description') <small>{{ $message }}</small> @enderror
                </label>
                <label>
                    Lampiran (PDF/JPG/PNG/WEBP max 4MB)
                    <input type="file" name="attachment" accept=".pdf,image/png,image/jpeg,image/webp">
                    @error('attachment') <small>{{ $message }}</small> @enderror
                </label>

                <button type="submit">Kirim Pengajuan</button>
            </form>
        </article>
    </div>
</section>
@endsection
