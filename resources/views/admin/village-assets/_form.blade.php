@csrf

<div class="space-y-5">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Aset / Titik</label>
            <input id="name" name="name" type="text" value="{{ old('name', $asset->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700">Tipe</label>
            <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach ($typeOptions as $key => $option)
                    <option value="{{ $key }}" @selected(old('type', $asset->type ?? 'aset_desa') === $key)>{{ $option['label'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="subcategory" class="block text-sm font-medium text-gray-700">Sub Kategori</label>
            <input id="subcategory" name="subcategory" type="text" value="{{ old('subcategory', $asset->subcategory ?? '') }}" placeholder="Contoh: Gedung Olahraga, Balai Banjar, Warung..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700">Urutan Tampil</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $asset->sort_order ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $asset->description ?? '') }}</textarea>
    </div>

    <div>
        <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
        <input id="address" name="address" type="text" value="{{ old('address', $asset->address ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div>
        <label for="icon" class="block text-sm font-medium text-gray-700">Icon Marker (Opsional)</label>
        <input id="icon" name="icon" type="file" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <p class="mt-1 text-xs text-gray-500">Disarankan ikon persegi (64x64 atau 128x128), transparan PNG/SVG.</p>
        @if (!empty($asset?->icon_url))
            <div class="mt-2 flex items-center gap-3">
                <img src="{{ $asset->icon_url }}" alt="{{ $asset->name }}" class="h-12 w-12 rounded object-contain border border-gray-200 bg-white p-1">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="remove_icon" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700">Hapus icon saat ini</span>
                </label>
            </div>
        @endif
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="contact_person" class="block text-sm font-medium text-gray-700">Penanggung Jawab</label>
            <input id="contact_person" name="contact_person" type="text" value="{{ old('contact_person', $asset->contact_person ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="contact_phone" class="block text-sm font-medium text-gray-700">Kontak</label>
            <input id="contact_phone" name="contact_phone" type="text" value="{{ old('contact_phone', $asset->contact_phone ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
            <input id="latitude" name="latitude" type="number" step="any" value="{{ old('latitude', $asset->latitude ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
            <input id="longitude" name="longitude" type="number" step="any" value="{{ old('longitude', $asset->longitude ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="map_url" class="block text-sm font-medium text-gray-700">Link Peta (Opsional)</label>
            <input id="map_url" name="map_url" type="url" value="{{ old('map_url', $asset->map_url ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <div class="rounded-md border border-blue-100 bg-blue-50 p-3">
        <label for="short_map_url" class="block text-sm font-medium text-gray-700">Shortlink Google Maps (opsional)</label>
        <div class="mt-1 grid gap-2 md:grid-cols-[1fr_auto]">
            <input id="short_map_url" type="url" placeholder="Contoh: https://maps.app.goo.gl/xxxxx" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <button id="resolve-map-btn" type="button" class="inline-flex items-center justify-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Gunakan Link</button>
        </div>
        <p class="mt-1 text-xs text-gray-500">Sistem akan mencoba mengisi otomatis nama lokasi, alamat, koordinat, dan kontak jika tersedia di sumber peta.</p>
        <p id="resolve-map-feedback" class="mt-1 hidden text-xs font-semibold"></p>
    </div>

    <div class="rounded-md border border-gray-200">
        <div id="asset-picker-map" class="h-72 w-full rounded-md"></div>
    </div>
    <p class="text-xs text-gray-500">Klik atau geser marker pada peta untuk otomatis mengisi latitude/longitude dan alamat.</p>

    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $asset->is_published ?? true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span class="text-sm text-gray-700">Tampilkan pada peta publik</span>
    </label>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.village-assets.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    (function () {
        const mapEl = document.getElementById('asset-picker-map');
        const latEl = document.getElementById('latitude');
        const lngEl = document.getElementById('longitude');
        const addressEl = document.getElementById('address');
        const nameEl = document.getElementById('name');
        const mapUrlEl = document.getElementById('map_url');
        const phoneEl = document.getElementById('contact_phone');
        const personEl = document.getElementById('contact_person');
        const shortUrlEl = document.getElementById('short_map_url');
        const resolveBtn = document.getElementById('resolve-map-btn');
        const feedbackEl = document.getElementById('resolve-map-feedback');
        if (!mapEl || !latEl || !lngEl || typeof L === 'undefined') return;

        const fallbackLat = {{ app()->bound('currentVillage') && app('currentVillage')?->latitude ? app('currentVillage')->latitude : -8.6512299 }};
        const fallbackLng = {{ app()->bound('currentVillage') && app('currentVillage')?->longitude ? app('currentVillage')->longitude : 115.2148033 }};
        const startLat = parseFloat(latEl.value || fallbackLat);
        const startLng = parseFloat(lngEl.value || fallbackLng);

        const map = L.map('asset-picker-map').setView([startLat, startLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const marker = L.marker([startLat, startLng], { draggable: true }).addTo(map);

        const setPoint = (lat, lng) => {
            latEl.value = Number(lat).toFixed(7);
            lngEl.value = Number(lng).toFixed(7);
            marker.setLatLng([lat, lng]);
        };

        const reverseGeocode = (lat, lng) => {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}`, {
                headers: { 'Accept': 'application/json' }
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data && data.display_name && (!addressEl.value || addressEl.dataset.manual !== '1')) {
                        addressEl.value = data.display_name;
                    }
                })
                .catch(() => null);
        };

        const showFeedback = (text, success = true) => {
            if (!feedbackEl) return;
            feedbackEl.textContent = text;
            feedbackEl.classList.remove('hidden', 'text-red-600', 'text-green-700');
            feedbackEl.classList.add(success ? 'text-green-700' : 'text-red-600');
        };

        map.on('click', (event) => {
            const { lat, lng } = event.latlng;
            setPoint(lat, lng);
            reverseGeocode(lat, lng);
        });

        marker.on('dragend', () => {
            const point = marker.getLatLng();
            setPoint(point.lat, point.lng);
            reverseGeocode(point.lat, point.lng);
        });

        latEl.addEventListener('change', () => {
            const lat = parseFloat(latEl.value);
            const lng = parseFloat(lngEl.value);
            if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 14);
            }
        });

        lngEl.addEventListener('change', () => {
            const lat = parseFloat(latEl.value);
            const lng = parseFloat(lngEl.value);
            if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 14);
            }
        });

        addressEl?.addEventListener('input', () => {
            addressEl.dataset.manual = '1';
        });

        resolveBtn?.addEventListener('click', async () => {
            const value = (shortUrlEl?.value || '').trim();
            if (!value) {
                showFeedback('Masukkan shortlink Google Maps terlebih dahulu.', false);
                shortUrlEl?.focus();
                return;
            }

            resolveBtn.disabled = true;
            const originalText = resolveBtn.textContent;
            resolveBtn.textContent = 'Memproses...';

            try {
                const csrf = document.querySelector('input[name=\"_token\"]')?.value || '';
                const response = await fetch(@json(route('admin.village-assets.resolve-map-link')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ map_url: value }),
                });

                const data = await response.json();
                if (!response.ok || !data?.ok) {
                    throw new Error(data?.message || 'Link peta tidak bisa diproses.');
                }

                if (typeof data.latitude === 'number' && typeof data.longitude === 'number') {
                    setPoint(data.latitude, data.longitude);
                    map.setView([data.latitude, data.longitude], 16);
                }

                if (mapUrlEl && data.final_url) {
                    mapUrlEl.value = data.final_url;
                }

                if (nameEl && data.name) {
                    nameEl.value = data.name;
                }

                if (addressEl && data.address) {
                    addressEl.value = data.address;
                    addressEl.dataset.manual = '0';
                }

                if (phoneEl && data.contact_phone) {
                    phoneEl.value = data.contact_phone;
                }

                if (personEl && data.contact_person) {
                    personEl.value = data.contact_person;
                }

                showFeedback('Data lokasi berhasil diisi otomatis dari shortlink.', true);
            } catch (error) {
                showFeedback(error?.message || 'Gagal memproses shortlink Google Maps.', false);
            } finally {
                resolveBtn.disabled = false;
                resolveBtn.textContent = originalText;
            }
        });
    })();
</script>
