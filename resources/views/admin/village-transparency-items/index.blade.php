<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Transparansi Desa</h2>
            <a href="{{ route('admin.village-transparency-items.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                Tambah Data Transparansi
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
            @endif

            <div class="mb-4 rounded-md border border-gray-200 bg-white p-4">
                <form method="GET" action="{{ route('admin.village-transparency-items.index') }}" class="grid gap-3 md:grid-cols-[1fr_220px_180px_auto_auto]">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul/deskripsi..." class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <select name="category" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="all" @selected($category === 'all')>Semua Kategori</option>
                        @foreach ($categories as $key => $label)
                            <option value="{{ $key }}" @selected($category === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="year" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Tahun</option>
                        @foreach ($years as $yearOption)
                            <option value="{{ $yearOption }}" @selected($year === (int) $yearOption)>{{ $yearOption }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Filter</button>
                    <a href="{{ route('admin.village-transparency-items.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 text-center">Reset</a>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="px-3 py-2 text-left font-semibold">Tahun</th>
                                <th class="px-3 py-2 text-left font-semibold">Kategori</th>
                                <th class="px-3 py-2 text-left font-semibold">Judul</th>
                                <th class="px-3 py-2 text-left font-semibold">Nominal</th>
                                <th class="px-3 py-2 text-left font-semibold">Status</th>
                                <th class="px-3 py-2 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $row)
                                <tr class="border-b">
                                    <td class="px-3 py-3">{{ $row->fiscal_year ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ $row->categoryLabel() }}</td>
                                    <td class="px-3 py-3">
                                        <strong class="text-gray-900">{{ $row->title }}</strong>
                                        @if ($row->document_url)
                                            <p class="text-xs text-blue-700 mt-1"><a href="{{ $row->document_url }}" target="_blank" rel="noopener" class="hover:underline">Dokumen/Link</a></p>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        @if ($row->amount !== null)
                                            Rp {{ number_format((int) $row->amount, 0, ',', '.') }}
                                        @else
                                            -
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
                                        <a href="{{ route('admin.village-transparency-items.edit', $row) }}" class="text-blue-700 hover:underline">Ubah</a>
                                        <form action="{{ route('admin.village-transparency-items.destroy', $row) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Hapus data transparansi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada data transparansi desa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

