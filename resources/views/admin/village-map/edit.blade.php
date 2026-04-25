<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Map Desa</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800">Import Otomatis dari BIG</h3>
                    <p class="mt-1 text-sm text-gray-600">Gunakan nama wilayah administrasi agar boundary desa diambil langsung dari layanan BIG. Anda juga bisa isi link Google Maps untuk bantu set titik pusat map.</p>

                    <form method="POST" action="{{ route('admin.village-map.import-big') }}" class="mt-4 space-y-4">
                        @csrf
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="desa" class="block text-sm font-medium text-gray-700">Desa/Kelurahan (opsional)</label>
                                <input id="desa" name="desa" type="text" value="{{ old('desa', $village ? preg_replace('/^(Desa|Kelurahan)\s+/i', '', $village->name) : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('desa')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="kecamatan" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                                <input id="kecamatan" name="kecamatan" type="text" value="{{ old('kecamatan', $village->district ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="kota" class="block text-sm font-medium text-gray-700">Kota/Kabupaten</label>
                                <input id="kota" name="kota" type="text" value="{{ old('kota', $village->city ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="provinsi" class="block text-sm font-medium text-gray-700">Provinsi</label>
                                <input id="provinsi" name="provinsi" type="text" value="{{ old('provinsi', $village->province ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="md:col-span-2">
                                <label for="map_url_import" class="block text-sm font-medium text-gray-700">Link Google Maps (opsional)</label>
                                <input id="map_url_import" name="map_url" type="url" value="{{ old('map_url') }}" placeholder="https://maps.app.goo.gl/..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Jika diisi, sistem akan mencoba mengekstrak koordinat dari link untuk titik pusat map.</p>
                            </div>
                        </div>

                        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                            Import Boundary dari BIG
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800">Ubah Manual Map Desa</h3>
                    <p class="mt-1 text-sm text-gray-600">Gunakan mode ini jika ingin Perbarui koordinat atau boundary GeoJSON secara manual. Bisa juga isi link Google Maps agar koordinat otomatis terbaca.</p>

                    <form method="POST" action="{{ route('admin.village-map.update') }}" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                                <input id="latitude" name="latitude" type="number" step="0.0000001" value="{{ old('latitude', $village->latitude ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('latitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                                <input id="longitude" name="longitude" type="number" step="0.0000001" value="{{ old('longitude', $village->longitude ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('longitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="map_url_manual" class="block text-sm font-medium text-gray-700">Link Google Maps (opsional)</label>
                                <input id="map_url_manual" name="map_url" type="url" value="{{ old('map_url') }}" placeholder="https://maps.app.goo.gl/..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Dukungan format: `maps.app.goo.gl`, `google.com/maps/@lat,lng`, atau embed yang mengandung `!3d...!4d...`.</p>
                            </div>
                        </div>

                        <div>
                            <label for="boundary_geojson" class="block text-sm font-medium text-gray-700">Boundary GeoJSON</label>
                            <textarea id="boundary_geojson" name="boundary_geojson" rows="14" class="mt-1 block w-full rounded-md border-gray-300 font-mono text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('boundary_geojson', $village && $village->boundary_geojson ? json_encode($village->boundary_geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea>
                            @error('boundary_geojson')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">
                            Simpan Data Map
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


