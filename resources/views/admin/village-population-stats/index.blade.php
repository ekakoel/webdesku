<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Statistik Kategori Penduduk</h2>
            <a href="{{ route('admin.village-population-stats.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                Tambah Statistik
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.village-population-stats.index') }}" class="grid gap-3 md:grid-cols-[220px_1fr_auto_auto]">
                        <select name="year" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Tahun</option>
                            @foreach ($years as $yearOption)
                                <option value="{{ $yearOption }}" @selected($year === (int) $yearOption)>{{ $yearOption }}</option>
                            @endforeach
                        </select>
                        <select name="category" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all" @selected($category === 'all')>Semua Kategori</option>
                            @foreach ($categoryOptions as $key => $option)
                                <option value="{{ $key }}" @selected($category === $key)>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Filter</button>
                        <a href="{{ route('admin.village-population-stats.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Reset</a>
                    </form>
                </div>
            </div>

            <div class="mt-4 bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="px-3 py-2 text-left">Tahun</th>
                                <th class="px-3 py-2 text-left">Kategori</th>
                                <th class="px-3 py-2 text-left">Label</th>
                                <th class="px-3 py-2 text-left">Nilai</th>
                                <th class="px-3 py-2 text-left">Status</th>
                                <th class="px-3 py-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $row)
                                <tr class="border-b">
                                    <td class="px-3 py-3">{{ $row->year }}</td>
                                    <td class="px-3 py-3">{{ $row->categoryLabel() }}</td>
                                    <td class="px-3 py-3">{{ $row->label }}</td>
                                    <td class="px-3 py-3">
                                        {{ number_format((int) $row->value, 0, ',', '.') }}
                                        @if ($row->unit)
                                            {{ $row->unit }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        @if ($row->is_published)
                                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">Publik</span>
                                        @else
                                            <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Draf</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <a href="{{ route('admin.village-population-stats.edit', $row) }}" class="text-blue-700 hover:underline">Ubah</a>
                                        <form action="{{ route('admin.village-population-stats.destroy', $row) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Hapus statistik ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada data statistik kategori penduduk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $items->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

