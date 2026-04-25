@csrf
<input type="hidden" name="slug" value="{{ old('slug', $slug) }}">

<div class="space-y-5">
    <div>
        <label class="block text-sm font-medium text-gray-700">Halaman</label>
        <div class="mt-1 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-700">
            {{ $label }} (slug: {{ $slug }})
        </div>
    </div>
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
        <input id="title" name="title" type="text" value="{{ old('title', $profilePage->title ?? $label) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label for="subtitle" class="block text-sm font-medium text-gray-700">Subjudul</label>
        <textarea id="subtitle" name="subtitle" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('subtitle', $profilePage->subtitle ?? '') }}</textarea>
    </div>
    <div>
        <label for="content" class="block text-sm font-medium text-gray-700">Konten Utama</label>
        <textarea id="content" name="content" rows="8" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('content', $profilePage->content ?? '') }}</textarea>
    </div>
    <div>
        <label for="source_url" class="block text-sm font-medium text-gray-700">URL Referensi</label>
        <input id="source_url" name="source_url" type="url" value="{{ old('source_url', $profilePage->source_url ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label for="payload_json" class="block text-sm font-medium text-gray-700">Payload JSON (opsional, untuk data terstruktur/chart)</label>
        <textarea id="payload_json" name="payload_json" rows="10" class="mt-1 block w-full rounded-md border-gray-300 font-mono text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('payload_json', isset($profilePage) && $profilePage->payload ? json_encode($profilePage->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '') }}</textarea>
    </div>
    @if ($slug === \App\Models\VillageProfilePage::SLUG_GAMBARAN)
        <div class="rounded-lg border border-blue-100 bg-blue-50 p-4">
            <h3 class="text-sm font-semibold text-blue-900">Pengaturan Detail Gambaran Umum</h3>
            <p class="mt-1 text-xs text-blue-700">
                Isi form berikut agar konten halaman Gambaran Umum dapat diatur penuh dari admin.
                Data akan otomatis disimpan ke payload JSON.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="g_batas_utara" class="block text-sm font-medium text-gray-700">Batas Utara</label>
                <input id="g_batas_utara" type="text" class="g-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="g_batas_selatan" class="block text-sm font-medium text-gray-700">Batas Selatan</label>
                <input id="g_batas_selatan" type="text" class="g-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="g_batas_barat" class="block text-sm font-medium text-gray-700">Batas Barat</label>
                <input id="g_batas_barat" type="text" class="g-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="g_batas_timur" class="block text-sm font-medium text-gray-700">Batas Timur</label>
                <input id="g_batas_timur" type="text" class="g-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="g_penduduk_kk" class="block text-sm font-medium text-gray-700">Jumlah KK</label>
                <input id="g_penduduk_kk" type="number" min="0" class="g-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="g_penduduk_male" class="block text-sm font-medium text-gray-700">Penduduk Laki-laki</label>
                <input id="g_penduduk_male" type="number" min="0" class="g-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="g_penduduk_female" class="block text-sm font-medium text-gray-700">Penduduk Perempuan</label>
                <input id="g_penduduk_female" type="number" min="0" class="g-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="g_orbitasi" class="block text-sm font-medium text-gray-700">Orbitasi</label>
                <textarea id="g_orbitasi" rows="5" class="g-field mt-1 block w-full rounded-md border-gray-300 font-mono text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Format tiap baris: Label|Nilai&#10;Contoh: Jarak ke ibu kota kecamatan|1 KM"></textarea>
            </div>
            <div>
                <label for="g_luas" class="block text-sm font-medium text-gray-700">Luas Wilayah Menurut Penggunaan</label>
                <textarea id="g_luas" rows="5" class="g-field mt-1 block w-full rounded-md border-gray-300 font-mono text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Format tiap baris: Label|Nilai&#10;Contoh: Pemukiman|43,78 ha"></textarea>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label for="g_pendidikan" class="block text-sm font-medium text-gray-700">Data Pendidikan</label>
                <textarea id="g_pendidikan" rows="6" class="g-field mt-1 block w-full rounded-md border-gray-300 font-mono text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Format: Label|Angka"></textarea>
            </div>
            <div>
                <label for="g_agama" class="block text-sm font-medium text-gray-700">Data Agama</label>
                <textarea id="g_agama" rows="6" class="g-field mt-1 block w-full rounded-md border-gray-300 font-mono text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Format: Label|Angka"></textarea>
            </div>
            <div>
                <label for="g_pekerjaan" class="block text-sm font-medium text-gray-700">Data Pekerjaan</label>
                <textarea id="g_pekerjaan" rows="6" class="g-field mt-1 block w-full rounded-md border-gray-300 font-mono text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Format: Label|Angka"></textarea>
            </div>
        </div>

        <details class="rounded-lg border border-gray-200 bg-gray-50 p-3">
            <summary class="cursor-pointer text-sm font-medium text-gray-700">Editor JSON lanjutan (opsional)</summary>
            <p class="mt-2 text-xs text-gray-500">
                Jika diisi, form terstruktur di atas akan menimpa key yang sama saat disimpan.
            </p>
        </details>
    @endif
    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $profilePage->is_published ?? true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span class="text-sm text-gray-700">Publikasikan di website</span>
    </label>
    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">{{ $submitLabel }}</button>
        <a href="{{ route('admin.profile-pages.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>

@if ($slug === \App\Models\VillageProfilePage::SLUG_GAMBARAN)
<script>
    (function () {
        const form = document.currentScript.closest('form');
        if (!form) return;

        const payloadField = form.querySelector('#payload_json');
        if (!payloadField) return;

        const byId = (id) => form.querySelector('#' + id);
        const parseLines = (text) => String(text || '')
            .split(/\r?\n/)
            .map((line) => line.trim())
            .filter(Boolean)
            .map((line) => {
                const parts = line.split('|');
                if (parts.length < 2) return null;
                const label = parts.shift().trim();
                const valueRaw = parts.join('|').trim();
                if (!label || valueRaw === '') return null;
                const valueNum = Number(valueRaw);
                const value = Number.isFinite(valueNum) && valueRaw.match(/^\d+(\.\d+)?$/) ? valueNum : valueRaw;
                return { label, value };
            })
            .filter(Boolean);

        const stringifyLines = (items) => Array.isArray(items)
            ? items.map((row) => `${row.label ?? ''}|${row.value ?? ''}`).join('\n')
            : '';

        const safeParsePayload = () => {
            try {
                const parsed = JSON.parse(payloadField.value || '{}');
                return (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) ? parsed : {};
            } catch (_) {
                return {};
            }
        };

        const hydrate = () => {
            const payload = safeParsePayload();
            byId('g_batas_utara').value = payload?.batas?.utara ?? '';
            byId('g_batas_selatan').value = payload?.batas?.selatan ?? '';
            byId('g_batas_barat').value = payload?.batas?.barat ?? '';
            byId('g_batas_timur').value = payload?.batas?.timur ?? '';

            byId('g_penduduk_kk').value = payload?.penduduk?.kk ?? '';
            byId('g_penduduk_male').value = payload?.penduduk?.male ?? '';
            byId('g_penduduk_female').value = payload?.penduduk?.female ?? '';

            byId('g_orbitasi').value = stringifyLines(payload?.orbitasi);
            byId('g_luas').value = stringifyLines(payload?.luas);
            byId('g_pendidikan').value = stringifyLines(payload?.pendidikan);
            byId('g_agama').value = stringifyLines(payload?.agama);
            byId('g_pekerjaan').value = stringifyLines(payload?.pekerjaan);
        };

        const buildPayload = () => {
            const payload = safeParsePayload();

            payload.batas = {
                utara: byId('g_batas_utara').value.trim(),
                selatan: byId('g_batas_selatan').value.trim(),
                barat: byId('g_batas_barat').value.trim(),
                timur: byId('g_batas_timur').value.trim(),
            };

            payload.penduduk = {
                kk: Number(byId('g_penduduk_kk').value || 0),
                male: Number(byId('g_penduduk_male').value || 0),
                female: Number(byId('g_penduduk_female').value || 0),
            };

            payload.orbitasi = parseLines(byId('g_orbitasi').value);
            payload.luas = parseLines(byId('g_luas').value);
            payload.pendidikan = parseLines(byId('g_pendidikan').value);
            payload.agama = parseLines(byId('g_agama').value);
            payload.pekerjaan = parseLines(byId('g_pekerjaan').value);

            payloadField.value = JSON.stringify(payload, null, 2);
        };

        hydrate();
        form.addEventListener('submit', buildPayload);
    })();
</script>
@endif
