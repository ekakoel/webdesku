@csrf

<div class="space-y-5">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Judul Agenda</label>
        <input id="title" name="title" type="text" value="{{ old('title', $agenda->title ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea id="description" name="description" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $agenda->description ?? '') }}</textarea>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="location" class="block text-sm font-medium text-gray-700">Lokasi</label>
            <input id="location" name="location" type="text" value="{{ old('location', $agenda->location ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="map_url" class="block text-sm font-medium text-gray-700">Link Google Maps / Short Link</label>
            <div class="mt-1 flex gap-2">
                <input id="map_url" name="map_url" type="url" value="{{ old('map_url', $agenda->map_url ?? '') }}" placeholder="https://maps.app.goo.gl/..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="button" id="agenda-picker-resolve-link" class="whitespace-nowrap rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100">Gunakan Link</button>
            </div>
            <p class="mt-1 text-xs text-gray-500">Tempel short link Google Maps, klik "Gunakan Link", koordinat akan terisi otomatis.</p>
            @error('map_url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="poster" class="block text-sm font-medium text-gray-700">Poster Agenda</label>
            <input id="poster" name="poster" type="file" accept="image/png,image/jpeg,image/webp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <p class="mt-1 text-xs text-gray-500">Format JPG/PNG/WEBP, maksimal 4MB.</p>
            @if (!empty($agenda?->poster_url))
                <div class="mt-2 space-y-2">
                    <img src="{{ $agenda->poster_url }}" alt="{{ $agenda->title }}" class="h-28 w-48 rounded-md object-cover">
                    <label class="inline-flex items-center gap-2 text-sm text-red-700">
                        <input type="hidden" name="remove_poster" value="0">
                        <input type="checkbox" name="remove_poster" value="1" class="rounded border-red-300 text-red-600 focus:ring-red-500">
                        Hapus poster saat simpan
                    </label>
                </div>
            @else
                <input type="hidden" name="remove_poster" value="0">
            @endif
            @error('poster')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="start_at" class="block text-sm font-medium text-gray-700">Mulai</label>
            <input id="start_at" name="start_at" type="datetime-local" value="{{ old('start_at', isset($agenda->start_at) ? $agenda->start_at->format('Y-m-d\\TH:i') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="end_at" class="block text-sm font-medium text-gray-700">Selesai</label>
            <input id="end_at" name="end_at" type="datetime-local" value="{{ old('end_at', isset($agenda->end_at) ? $agenda->end_at->format('Y-m-d\\TH:i') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude Lokasi Agenda</label>
            <input id="latitude" name="latitude" type="number" step="0.0000001" value="{{ old('latitude', $agenda->latitude ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude Lokasi Agenda</label>
            <input id="longitude" name="longitude" type="number" step="0.0000001" value="{{ old('longitude', $agenda->longitude ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <p class="mt-1 text-xs text-gray-500">Gunakan koordinat desimal, contoh: -8.6512299 dan 115.2148033</p>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Pilih Titik Lokasi di Peta</label>
        <p class="mt-1 text-xs text-gray-500">Klik pada peta untuk mengisi otomatis latitude dan longitude agenda.</p>
        <div id="agenda-picker-map" class="mt-2 h-72 w-full rounded-md border border-gray-300"></div>
        <p id="agenda-picker-geocode-status" class="mt-2 text-xs text-gray-500"></p>
        <div class="mt-2 flex flex-wrap gap-2">
            <button type="button" id="agenda-picker-center-village" class="rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                Pusatkan ke Koordinat Desa
            </button>
            <button type="button" id="agenda-picker-use-location" class="rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">
                Gunakan Lokasi Saya
            </button>
        </div>
    </div>

    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $agenda->is_published ?? false)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span class="text-sm text-gray-700">Tampilkan di website publik</span>
    </label>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.agendas.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>

@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
@endonce

<script>
    (function () {
        const mapEl = document.getElementById('agenda-picker-map');
        const latEl = document.getElementById('latitude');
        const lngEl = document.getElementById('longitude');
        const locationEl = document.getElementById('location');
        const centerVillageBtn = document.getElementById('agenda-picker-center-village');
        const useLocationBtn = document.getElementById('agenda-picker-use-location');
        const mapUrlEl = document.getElementById('map_url');
        const resolveLinkBtn = document.getElementById('agenda-picker-resolve-link');
        const geocodeStatusEl = document.getElementById('agenda-picker-geocode-status');
        if (!mapEl || !latEl || !lngEl || typeof L === 'undefined') return;

        const villageLat = {{ app()->bound('currentVillage') ? (app('currentVillage')->latitude ?? -8.6512299) : -8.6512299 }};
        const villageLng = {{ app()->bound('currentVillage') ? (app('currentVillage')->longitude ?? 115.2148033) : 115.2148033 }};

        const hasInitialPoint = latEl.value !== '' && lngEl.value !== '' && !Number.isNaN(Number(latEl.value)) && !Number.isNaN(Number(lngEl.value));
        const initialLat = hasInitialPoint ? Number(latEl.value) : Number(villageLat);
        const initialLng = hasInitialPoint ? Number(lngEl.value) : Number(villageLng);

        const map = L.map(mapEl).setView([initialLat, initialLng], hasInitialPoint ? 15 : 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const marker = L.marker([initialLat, initialLng], {
            draggable: true,
        }).addTo(map);
        let geocodeTimer = null;
        let geocodeAbort = null;

        const syncInputs = (lat, lng) => {
            latEl.value = Number(lat).toFixed(7);
            lngEl.value = Number(lng).toFixed(7);
        };

        const reverseGeocode = (lat, lng) => {
            if (!locationEl) return;
            if (geocodeAbort) geocodeAbort.abort();
            geocodeAbort = new AbortController();

            if (geocodeStatusEl) geocodeStatusEl.textContent = 'Mencari nama lokasi...';
            const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}&accept-language=id`;

            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                signal: geocodeAbort.signal
            })
                .then((response) => response.ok ? response.json() : Promise.reject(new Error('Gagal reverse geocode')))
                .then((data) => {
                    if (data && data.display_name) {
                        locationEl.value = data.display_name;
                        if (geocodeStatusEl) geocodeStatusEl.textContent = 'Lokasi otomatis diperbarui dari peta.';
                    } else if (geocodeStatusEl) {
                        geocodeStatusEl.textContent = 'Lokasi tidak ditemukan, silakan isi manual.';
                    }
                })
                .catch((error) => {
                    if (error.name === 'AbortError') return;
                    if (geocodeStatusEl) geocodeStatusEl.textContent = 'Gagal mengambil nama lokasi, silakan isi manual.';
                });
        };

        const scheduleReverseGeocode = (lat, lng) => {
            if (geocodeTimer) clearTimeout(geocodeTimer);
            geocodeTimer = setTimeout(() => reverseGeocode(lat, lng), 600);
        };

        const moveMarker = (lat, lng, shouldPan = true) => {
            marker.setLatLng([lat, lng]);
            if (shouldPan) map.panTo([lat, lng]);
            syncInputs(lat, lng);
            scheduleReverseGeocode(lat, lng);
        };

        map.on('click', (e) => {
            moveMarker(e.latlng.lat, e.latlng.lng, false);
        });

        marker.on('dragend', () => {
            const pos = marker.getLatLng();
            syncInputs(pos.lat, pos.lng);
            scheduleReverseGeocode(pos.lat, pos.lng);
        });

        const refreshFromInputs = () => {
            const lat = Number(latEl.value);
            const lng = Number(lngEl.value);
            if (Number.isFinite(lat) && Number.isFinite(lng)) {
                moveMarker(lat, lng);
            }
        };

        latEl.addEventListener('change', refreshFromInputs);
        lngEl.addEventListener('change', refreshFromInputs);

        if (centerVillageBtn) {
            centerVillageBtn.addEventListener('click', () => {
                moveMarker(villageLat, villageLng);
                map.setZoom(14);
            });
        }

        if (useLocationBtn && navigator.geolocation) {
            useLocationBtn.addEventListener('click', () => {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        moveMarker(pos.coords.latitude, pos.coords.longitude);
                        map.setZoom(16);
                    },
                    () => {
                        alert('Lokasi perangkat tidak dapat diakses. Periksa izin lokasi browser Anda.');
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            });
        }

        if (resolveLinkBtn && mapUrlEl) {
            resolveLinkBtn.addEventListener('click', async () => {
                const mapUrl = mapUrlEl.value.trim();
                if (!mapUrl) {
                    if (geocodeStatusEl) geocodeStatusEl.textContent = 'Isi link Google Maps terlebih dahulu.';
                    return;
                }

                resolveLinkBtn.disabled = true;
                resolveLinkBtn.textContent = 'Memproses...';
                if (geocodeStatusEl) geocodeStatusEl.textContent = 'Membaca koordinat dari link Google Maps...';

                try {
                    const response = await fetch(@json(route('admin.agendas.resolve-map-link')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token()),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ map_url: mapUrl })
                    });
                    const payload = await response.json();
                    if (!response.ok || !payload.ok) {
                        throw new Error(payload.message || 'Gagal memproses link peta.');
                    }

                    mapUrlEl.value = payload.final_url || mapUrl;
                    moveMarker(Number(payload.latitude), Number(payload.longitude));
                    if (geocodeStatusEl) geocodeStatusEl.textContent = 'Koordinat berhasil diisi dari link Google Maps.';
                } catch (error) {
                    if (geocodeStatusEl) geocodeStatusEl.textContent = error.message || 'Gagal memproses link peta.';
                } finally {
                    resolveLinkBtn.disabled = false;
                    resolveLinkBtn.textContent = 'Gunakan Link';
                }
            });
        }

        if (hasInitialPoint && !locationEl?.value) {
            scheduleReverseGeocode(initialLat, initialLng);
        }
    })();
</script>


