<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Aset & Infografis Desa</h2>
            <a href="{{ route('admin.village-assets.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                Tambah Titik Aset
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-4 rounded-md border border-gray-200 bg-white p-4">
                <form method="GET" action="{{ route('admin.village-assets.index') }}" class="grid gap-3 md:grid-cols-[1fr_auto_auto_auto]">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama/sub kategori/alamat..." class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <select name="type" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="all" @selected($type === 'all')>Semua Tipe</option>
                        @foreach ($typeOptions as $key => $option)
                            <option value="{{ $key }}" @selected($type === $key)>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Filter</button>
                    <a href="{{ route('admin.village-assets.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 text-center">Reset</a>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="px-3 py-2 text-left font-semibold">Icon</th>
                                <th class="px-3 py-2 text-left font-semibold">Nama Titik</th>
                                <th class="px-3 py-2 text-left font-semibold">Tipe</th>
                                <th class="px-3 py-2 text-left font-semibold">Alamat</th>
                                <th class="px-3 py-2 text-left font-semibold">Koordinat</th>
                                <th class="px-3 py-2 text-left font-semibold">Status</th>
                                <th class="px-3 py-2 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assets as $asset)
                                <tr class="border-b">
                                    <td class="px-3 py-3">
                                        @if ($asset->icon_url)
                                            <img src="{{ $asset->icon_url }}" alt="{{ $asset->name }}" class="h-10 w-10 rounded object-contain border border-gray-200 bg-white p-1">
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        <strong class="text-gray-900">{{ $asset->name }}</strong>
                                        @if ($asset->subcategory)
                                            <p class="text-xs text-gray-500">{{ $asset->subcategory }}</p>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="rounded-full px-2 py-1 text-xs font-semibold text-white" style="background: {{ $asset->typeColor() }}">
                                            {{ $asset->typeLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">{{ $asset->address ?: '-' }}</td>
                                    <td class="px-3 py-3 text-xs text-gray-700">{{ $asset->latitude }}, {{ $asset->longitude }}</td>
                                    <td class="px-3 py-3">
                                        @if ($asset->is_published)
                                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">Publik</span>
                                        @else
                                            <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Draf</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <a href="{{ route('admin.village-assets.edit', $asset) }}" class="text-blue-700 hover:underline">Ubah</a>
                                        <form action="{{ route('admin.village-assets.destroy', $asset) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Hapus data aset ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-6 text-center text-gray-500">Belum ada data aset / UMKM / fasilitas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $assets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
