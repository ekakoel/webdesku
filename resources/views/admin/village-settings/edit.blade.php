<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengaturan Desa</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif
            <form id="instagram-sync-form" action="{{ route('admin.village-settings.sync-instagram') }}" method="POST" class="hidden">
                @csrf
            </form>

            <form action="{{ route('admin.village-settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900">Identitas Desa</h3>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Desa</label>
                            <input type="text" name="name" value="{{ old('name', $village->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Slug Desa</label>
                            <input type="text" name="slug" value="{{ old('slug', $village->slug ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Kepala Desa</label>
                            <input type="text" name="head_name" value="{{ old('head_name', $village->head_name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('head_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Logo Desa</label>
                            <input type="file" name="logo" accept="image/*" class="mt-1 block w-full text-sm text-gray-700">
                            @if (!empty($village?->logo_url))
                                <div class="mt-2 flex items-center gap-3">
                                    <img src="{{ $village->logo_url }}" alt="Logo desa" class="h-12 w-12 rounded object-cover">
                                    <label class="inline-flex items-center gap-2 text-xs text-gray-600">
                                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        Hapus logo saat simpan
                                    </label>
                                </div>
                            @endif
                            @error('logo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Deskripsi Singkat Desa</label>
                        <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $village->description ?? '') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900">Kontak & Wilayah</h3>
                    <div class="mt-4 grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $village->phone ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email', $village->email ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Website</label>
                            <input type="url" name="website" value="{{ old('website', $village->website ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kode Pos</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $village->postal_code ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
                            <input type="text" name="district" value="{{ old('district', $village->district ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kota/Kabupaten</label>
                            <input type="text" name="city" value="{{ old('city', $village->city ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Provinsi</label>
                            <input type="text" name="province" value="{{ old('province', $village->province ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Negara</label>
                            <input type="text" name="country" value="{{ old('country', $village->country ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Luas Wilayah (km2)</label>
                            <input type="number" step="0.01" min="0" name="area_km2" value="{{ old('area_km2', $village->area_km2 ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea name="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address', $village->address ?? '') }}</textarea>
                    </div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900">Statistik Ringkas (Sumber Tunggal)</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Data penduduk & KK otomatis diambil dari menu <strong>Kelola Penduduk (Infografis)</strong> pada tahun terbaru yang dipublikasikan.
                    </p>
                    <div class="mt-4 grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Penduduk</label>
                            <input type="text" value="{{ number_format((int) ($summaryPopulation['total'] ?? 0), 0, ',', '.') }}" readonly class="mt-1 block w-full rounded-md border-gray-200 bg-slate-50 text-slate-700 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Penduduk Laki-laki</label>
                            <input type="text" value="{{ number_format((int) ($summaryPopulation['male'] ?? 0), 0, ',', '.') }}" readonly class="mt-1 block w-full rounded-md border-gray-200 bg-slate-50 text-slate-700 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Penduduk Perempuan</label>
                            <input type="text" value="{{ number_format((int) ($summaryPopulation['female'] ?? 0), 0, ',', '.') }}" readonly class="mt-1 block w-full rounded-md border-gray-200 bg-slate-50 text-slate-700 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah KK</label>
                            <input type="text" value="{{ number_format((int) ($summaryPopulation['households'] ?? 0), 0, ',', '.') }}" readonly class="mt-1 block w-full rounded-md border-gray-200 bg-slate-50 text-slate-700 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah RT</label>
                            <input type="number" min="0" name="rt_count" value="{{ old('rt_count', $village->rt_count ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah RW</label>
                            <input type="number" min="0" name="rw_count" value="{{ old('rw_count', $village->rw_count ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.village-populations.index') }}" class="text-xs font-medium text-blue-700 hover:underline">
                            Kelola data sumber penduduk
                        </a>
                    </div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900">Ringkasan Aset & APBDes (Otomatis)</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Data di bawah ini otomatis dihitung dari modul sumber. Tidak perlu input ulang.
                    </p>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <h4 class="text-sm font-semibold text-slate-800">Aset Desa (Published)</h4>
                            <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format((int) ($summaryAssets['total'] ?? 0), 0, ',', '.') }}</p>
                            <p class="mt-1 text-xs text-slate-500">Total titik/aset publik</p>
                            <a href="{{ route('admin.village-assets.index') }}" class="mt-2 inline-block text-xs font-medium text-blue-700 hover:underline">Kelola data sumber aset</a>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <h4 class="text-sm font-semibold text-slate-800">APBDes Tahun {{ $summaryApbdes['year'] ?? '-' }}</h4>
                            <div class="mt-2 space-y-1 text-sm text-slate-700">
                                <p>Pendapatan: <strong>Rp {{ number_format((int) ($summaryApbdes['pendapatan'] ?? 0), 0, ',', '.') }}</strong></p>
                                <p>Belanja: <strong>Rp {{ number_format((int) ($summaryApbdes['belanja'] ?? 0), 0, ',', '.') }}</strong></p>
                                <p>Pembiayaan: <strong>Rp {{ number_format((int) ($summaryApbdes['pembiayaan'] ?? 0), 0, ',', '.') }}</strong></p>
                            </div>
                            <a href="{{ route('admin.village-apbdes-items.index') }}" class="mt-2 inline-block text-xs font-medium text-blue-700 hover:underline">Kelola data sumber APBDes</a>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900">Konten Profil Dasar</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sejarah Singkat</label>
                            <textarea name="history" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('history', $village->history ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Visi</label>
                            <textarea name="vision" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('vision', $village->vision ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Misi (pisahkan per baris)</label>
                            <textarea name="mission" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('mission', $village->mission ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sambutan Kepala Desa</label>
                            <textarea name="head_greeting" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('head_greeting', $village->head_greeting ?? '') }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-base font-semibold text-slate-900">Integrasi Instagram</h3>
                        <button type="submit" form="instagram-sync-form" class="inline-flex items-center rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                            Sinkronkan Postingan Sekarang
                        </button>
                    </div>
                    @error('instagram')
                        <p class="mt-3 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                                <input type="checkbox" name="instagram_enabled" value="1" @checked(old('instagram_enabled', (bool) ($village->instagram_enabled ?? false))) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                Aktifkan tampilan postingan Instagram di beranda
                            </label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Username Instagram</label>
                            <input type="text" name="instagram_username" value="{{ old('instagram_username', $village->instagram_username ?? '') }}" placeholder="contoh: desadigital" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('instagram_username') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Instagram User ID (opsional)</label>
                            <input type="text" name="instagram_user_id" value="{{ old('instagram_user_id', $village->instagram_user_id ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('instagram_user_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                <span>Access Token Instagram Graph API</span>
                                <button type="button" data-open-instagram-token-help class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-blue-200 bg-blue-50 text-xs font-bold text-blue-700 hover:bg-blue-100" title="Cara mendapatkan token" aria-label="Cara mendapatkan token Instagram">
                                    i
                                </button>
                            </label>
                            <textarea name="instagram_access_token" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('instagram_access_token', $village->instagram_access_token ?? '') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Gunakan long-lived access token dari akun Instagram profesional yang terhubung Facebook Page.</p>
                            @error('instagram_access_token') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mt-4 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                        <div>Terakhir sinkron: {{ optional($village?->instagram_last_sync_at)->format('d M Y H:i') ?? '-' }}</div>
                        <div>Terakhir terhubung: {{ optional($village?->instagram_connected_at)->format('d M Y H:i') ?? '-' }}</div>
                        @if (!empty($village?->instagram_last_error))
                            <div class="mt-1 text-red-600">Error terakhir: {{ $village->instagram_last_error }}</div>
                        @endif
                    </div>
                </section>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                        Simpan Pengaturan Desa
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:underline">Kembali</a>
                </div>
            </form>

            <div data-instagram-token-help-modal class="fixed inset-0 z-50 hidden">
                <div data-close-instagram-token-help class="absolute inset-0 bg-slate-900/50"></div>
                <div class="relative mx-auto mt-12 w-[92%] max-w-2xl rounded-xl bg-white p-5 shadow-xl">
                    <div class="flex items-start justify-between gap-3">
                        <h4 class="text-base font-semibold text-slate-900">Cara Mendapatkan Long-Lived Access Token Instagram</h4>
                        <button type="button" data-close-instagram-token-help class="rounded-md border border-slate-200 px-2 py-1 text-xs text-slate-600 hover:bg-slate-50">Tutup</button>
                    </div>
                    <ol class="mt-4 list-decimal space-y-2 pl-5 text-sm text-slate-700">
                        <li>Pastikan akun Instagram sudah tipe Profesional (Business/Creator).</li>
                        <li>Hubungkan akun Instagram ke Facebook Page yang aktif.</li>
                        <li>Buka Facebook Developer dan buat App dengan produk Facebook Login + Instagram Graph API.</li>
                        <li>Minta izin minimum: <code>instagram_basic</code> dan <code>pages_show_list</code> (tambahkan izin lain sesuai kebutuhan).</li>
                        <li>Generate short-lived user token melalui Graph API Explorer/OAuth flow.</li>
                        <li>Tukar token tersebut menjadi long-lived token via endpoint <code>/oauth/access_token</code> Graph API.</li>
                        <li>Gunakan long-lived token yang sudah didapat pada kolom ini, lalu klik "Sinkronkan Postingan Sekarang".</li>
                    </ol>
                    <p class="mt-3 text-xs text-slate-500">Catatan: long-lived token tetap punya masa berlaku, jadi lakukan refresh token berkala sesuai dokumentasi Meta.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const openButton = document.querySelector('[data-open-instagram-token-help]');
            const modal = document.querySelector('[data-instagram-token-help-modal]');
            if (!openButton || !modal) return;

            const closeButtons = modal.querySelectorAll('[data-close-instagram-token-help]');

            const openModal = () => modal.classList.remove('hidden');
            const closeModal = () => modal.classList.add('hidden');

            openButton.addEventListener('click', openModal);
            closeButtons.forEach((button) => button.addEventListener('click', closeModal));
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeModal();
            });
        })();
    </script>
</x-app-layout>
