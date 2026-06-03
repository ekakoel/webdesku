@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid page-section-stack">
        <article class="page-hero section-card">
            <div>
                <small>Pengaduan Desa</small>
                <h1>Pengaduan Masyarakat</h1>
                <p>Sampaikan pengaduan secara langsung ke pemerintah desa. Nomor WhatsApp aktif dan email bersifat opsional sebagai sarana update status.</p>
            </div>
            <div class="page-hero__actions">
                <a href="{{ route('complaints.status') }}" class="text-link">Cek Status Pengaduan</a>
            </div>
        </article>

        @if (session('status'))
            <article class="section-card" style="padding: 1rem; border-color: #b6e7c6; background: #ecfdf3;">
                <p style="margin: 0; color: #166534; font-weight: 700;">{{ session('status') }}</p>
                @if (session('complaint_ticket'))
                    <p style="margin: .5rem 0 0; color: #14532d;">
                        Simpan kode tiket: <strong>{{ session('complaint_ticket') }}</strong>
                    </p>
                @endif
                @if (session('complaint_lookup_url'))
                    <a href="{{ session('complaint_lookup_url') }}" class="text-link" style="display: inline-block; margin-top: .45rem;">
                        Lihat status pengaduan sekarang
                    </a>
                @endif
            </article>
        @endif

        <article class="section-card" style="padding: 1rem;">
            <form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data" style="display: grid; gap: .85rem;">
                @csrf

                <div style="display:grid; gap:.35rem;">
                    <label for="name" style="font-weight:700;">Nama Pelapor</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required class="form-control">
                    @error('name')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
                </div>

                <div style="display:grid; gap:.35rem; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
                    <label for="email" style="font-weight:700; display:grid; gap:.35rem;">
                        Email (opsional)
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" class="form-control">
                    </label>
                    <label for="whatsapp" style="font-weight:700; display:grid; gap:.35rem;">
                        No. Handphone / WhatsApp Aktif (opsional)
                        <input id="whatsapp" type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="08xxxx / +62xxxx" class="form-control">
                    </label>
                </div>
                @error('email')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
                @error('whatsapp')<small style="color:#b91c1c;">{{ $message }}</small>@enderror

                <div style="display:grid; gap:.35rem; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
                    <label for="category" style="font-weight:700; display:grid; gap:.35rem;">
                        Kategori Pengaduan
                        <select id="category" name="category" required class="form-control">
                            @foreach ($categories as $value => $label)
                                <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label for="location" style="font-weight:700; display:grid; gap:.35rem;">
                        Lokasi Kejadian (opsional)
                        <input id="location" type="text" name="location" value="{{ old('location') }}" placeholder="Contoh: Banjar Kaja, depan balai banjar" class="form-control">
                    </label>
                </div>
                @error('category')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
                @error('location')<small style="color:#b91c1c;">{{ $message }}</small>@enderror

                <div style="display:grid; gap:.35rem;">
                    <label for="title" style="font-weight:700;">Judul Pengaduan</label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}" required class="form-control">
                    @error('title')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
                </div>

                <div style="display:grid; gap:.35rem;">
                    <label for="description" style="font-weight:700;">Isi Pengaduan</label>
                    <textarea id="description" name="description" rows="6" required class="form-control">{{ old('description') }}</textarea>
                    @error('description')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
                </div>

                <div style="display:grid; gap:.35rem;">
                    <label for="attachment" style="font-weight:700;">Lampiran Bukti (opsional)</label>
                    <input id="attachment" type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.webp" class="form-control">
                    <small style="color:#64748b;">Format: PDF/JPG/PNG/WEBP, maksimal 4MB.</small>
                    @error('attachment')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
                </div>

                <div style="display:flex; align-items:flex-start; gap:.5rem; color:#475569; font-size:.85rem;">
                    <i class="fa-solid fa-circle-info" style="margin-top:.2rem;"></i>
                    <span>Walaupun email atau WhatsApp tidak diisi, Anda tetap bisa memantau status pengaduan dengan kode tiket.</span>
                </div>

                <div>
                    <button type="submit" class="form-control-button">
                        Kirim Pengaduan
                    </button>
                </div>
            </form>
        </article>
    </div>
</section>
@endsection
