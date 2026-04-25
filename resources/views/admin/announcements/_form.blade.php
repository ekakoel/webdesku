@csrf

<div class="space-y-5">
    <div>
        <label for="type" class="block text-sm font-medium text-gray-700">Tipe Pengumuman</label>
        <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @foreach (($typeOptions ?? \App\Models\Announcement::typeOptions()) as $key => $meta)
                <option value="{{ $key }}" @selected(old('type', $announcement->type ?? \App\Models\Announcement::TYPE_UMUM) === $key)>{{ $meta['label'] }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Judul Pengumuman</label>
        <input id="title" name="title" type="text" value="{{ old('title', $announcement->title ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div>
        <label for="content" class="block text-sm font-medium text-gray-700">Isi Pengumuman</label>
        <textarea id="content" name="content" rows="7" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('content', $announcement->content ?? '') }}</textarea>
    </div>

    <div>
        <label for="reference_url" class="block text-sm font-medium text-gray-700">Link Referensi (opsional)</label>
        <input id="reference_url" name="reference_url" type="url" value="{{ old('reference_url', $announcement->reference_url ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label for="location_name" class="block text-sm font-medium text-gray-700">Nama Lokasi (opsional)</label>
            <input id="location_name" name="location_name" type="text" value="{{ old('location_name', $announcement->location_name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude (opsional)</label>
            <input id="latitude" name="latitude" type="number" step="0.0000001" value="{{ old('latitude', $announcement->latitude ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude (opsional)</label>
            <input id="longitude" name="longitude" type="number" step="0.0000001" value="{{ old('longitude', $announcement->longitude ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <div>
        <label for="map_url" class="block text-sm font-medium text-gray-700">Link Lokasi Map (opsional)</label>
        <div class="mt-1 flex gap-2">
            <input id="map_url" name="map_url" type="url" value="{{ old('map_url', $announcement->map_url ?? '') }}" placeholder="https://maps.app.goo.gl/..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <button type="button" id="announcement-resolve-link" class="whitespace-nowrap rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100">Gunakan Link</button>
        </div>
        <p id="announcement-map-status" class="mt-1 text-xs text-gray-500">Tempel short link Google Maps lalu klik "Gunakan Link" agar koordinat dan lokasi terisi otomatis.</p>
    </div>

    <div>
        <label for="images" class="block text-sm font-medium text-gray-700">Gambar Pengumuman (maks 3)</label>
        <input id="images" name="images[]" type="file" accept="image/*" multiple class="mt-1 block w-full text-sm text-gray-700">
        <p id="images-help" class="mt-1 text-xs text-gray-500">Format: JPG/PNG/WEBP, maksimal 4MB per file, total maksimal 3 gambar.</p>
        <div id="announcement-image-preview" class="mt-3 hidden">
            <p class="text-xs font-semibold text-blue-700">Preview gambar baru</p>
            <div id="announcement-image-preview-grid" class="mt-2 grid gap-3 md:grid-cols-3"></div>
        </div>
        @if (!empty($announcement) && method_exists($announcement, 'images'))
            @php $existingImages = $announcement->images ?? collect(); @endphp
            @if ($existingImages->isNotEmpty())
                <div class="mt-3 grid gap-3 md:grid-cols-3">
                    @foreach ($existingImages as $img)
                        <label class="rounded border border-gray-200 p-2">
                            <img src="{{ $img->image_url }}" alt="Gambar pengumuman" class="h-24 w-full rounded object-cover">
                            <span class="mt-2 inline-flex items-center gap-2 text-xs text-red-700">
                                <input type="checkbox" name="remove_image_ids[]" value="{{ $img->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                Hapus gambar ini
                            </span>
                        </label>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

    <div>
        <label for="attachment" class="block text-sm font-medium text-gray-700">Lampiran Dokumen (opsional)</label>
        <input id="attachment" name="attachment" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv" class="mt-1 block w-full text-sm text-gray-700">
        <p class="mt-1 text-xs text-gray-500">Dokumen dapat diunduh warga. Format: PDF, Word, Excel, CSV.</p>
        @if (!empty($announcement?->attachment_url))
            <div class="mt-2 rounded border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700">
                <p class="font-semibold">Lampiran saat ini: {{ $announcement->attachment_name ?: basename((string) $announcement->attachment_path) }}</p>
                <a href="{{ $announcement->attachment_url }}" target="_blank" rel="noopener" class="text-blue-700 hover:underline">Lihat file</a>
                <label class="ml-3 inline-flex items-center gap-2 text-red-700">
                    <input type="checkbox" name="remove_attachment" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    Hapus lampiran
                </label>
            </div>
        @endif
    </div>

    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $announcement->is_published ?? false)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span class="text-sm text-gray-700">Tampilkan di website publik</span>
    </label>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.announcements.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>

<script>
    (function () {
        const input = document.getElementById('images');
        const previewWrap = document.getElementById('announcement-image-preview');
        const previewGrid = document.getElementById('announcement-image-preview-grid');
        const helpText = document.getElementById('images-help');

        if (!input || !previewWrap || !previewGrid || !helpText) return;

        const form = input.closest('form');
        if (!form) return;

        const removeChecks = Array.from(form.querySelectorAll('input[name="remove_image_ids[]"]'));
        const existingTotal = removeChecks.length;
        const maxTotal = 3;

        const activeExistingCount = () => {
            if (removeChecks.length === 0) return 0;
            const markedForRemove = removeChecks.filter((checkbox) => checkbox.checked).length;
            return Math.max(existingTotal - markedForRemove, 0);
        };

        const syncPreview = () => {
            const files = Array.from(input.files || []);
            const availableSlots = Math.max(maxTotal - activeExistingCount(), 0);
            const filesToPreview = files.slice(0, availableSlots);

            previewGrid.innerHTML = '';
            filesToPreview.forEach((file, idx) => {
                const url = URL.createObjectURL(file);
                const card = document.createElement('div');
                card.className = 'rounded border border-gray-200 p-2';
                card.innerHTML = `
                    <img src="${url}" alt="Preview ${idx + 1}" class="h-24 w-full rounded object-cover">
                    <p class="mt-2 truncate text-xs text-gray-600">${file.name}</p>
                `;
                previewGrid.appendChild(card);
            });

            previewWrap.classList.toggle('hidden', filesToPreview.length === 0);

            if (files.length > availableSlots) {
                helpText.textContent = `Maksimal 3 gambar. Slot tersedia saat ini: ${availableSlots}. File selebihnya tidak akan diproses.`;
                helpText.classList.add('text-amber-600');
            } else {
                helpText.textContent = 'Format: JPG/PNG/WEBP, maksimal 4MB per file, total maksimal 3 gambar.';
                helpText.classList.remove('text-amber-600');
            }
        };

        input.addEventListener('change', syncPreview);
        removeChecks.forEach((checkbox) => checkbox.addEventListener('change', syncPreview));
    })();
</script>

<script>
    (function () {
        const mapUrlEl = document.getElementById('map_url');
        const resolveLinkBtn = document.getElementById('announcement-resolve-link');
        const latEl = document.getElementById('latitude');
        const lngEl = document.getElementById('longitude');
        const locationEl = document.getElementById('location_name');
        const statusEl = document.getElementById('announcement-map-status');

        if (!mapUrlEl || !resolveLinkBtn || !latEl || !lngEl) return;

        const setStatus = (text, kind = 'normal') => {
            if (!statusEl) return;
            statusEl.textContent = text;
            statusEl.classList.remove('text-gray-500', 'text-emerald-700', 'text-red-600');
            if (kind === 'ok') statusEl.classList.add('text-emerald-700');
            else if (kind === 'error') statusEl.classList.add('text-red-600');
            else statusEl.classList.add('text-gray-500');
        };

        const reverseGeocode = async (lat, lng) => {
            const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}&accept-language=id`;
            try {
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!response.ok) return null;
                const data = await response.json();
                return data?.display_name || null;
            } catch (_) {
                return null;
            }
        };

        resolveLinkBtn.addEventListener('click', async () => {
            const mapUrl = mapUrlEl.value.trim();
            if (!mapUrl) {
                setStatus('Isi link Google Maps terlebih dahulu.', 'error');
                return;
            }

            resolveLinkBtn.disabled = true;
            resolveLinkBtn.textContent = 'Memproses...';
            setStatus('Memproses link Google Maps...');

            try {
                const response = await fetch(@json(route('admin.announcements.resolve-map-link')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token()),
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({ map_url: mapUrl }),
                });

                const payload = await response.json();
                if (!response.ok || !payload.ok) {
                    throw new Error(payload.message || 'Gagal memproses link peta.');
                }

                mapUrlEl.value = payload.final_url || mapUrl;
                latEl.value = Number(payload.latitude).toFixed(7);
                lngEl.value = Number(payload.longitude).toFixed(7);

                if (locationEl && !locationEl.value.trim()) {
                    const locationName = await reverseGeocode(payload.latitude, payload.longitude);
                    if (locationName) {
                        locationEl.value = locationName;
                    }
                }

                setStatus('Koordinat berhasil diisi otomatis dari link Google Maps.', 'ok');
            } catch (error) {
                setStatus(error.message || 'Gagal memproses link Google Maps.', 'error');
            } finally {
                resolveLinkBtn.disabled = false;
                resolveLinkBtn.textContent = 'Gunakan Link';
            }
        });
    })();
</script>


