<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dokumen Transparansi</h2>
            <a href="{{ route('admin.village-transparency-documents.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Tambah Dokumen</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
            @endif
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('admin.village-transparency-documents.index') }}" class="grid gap-3 md:grid-cols-[1fr_220px_180px_auto_auto]">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul/deskripsi..." class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <select name="year" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Tahun</option>
                        @foreach ($years as $yearOption)
                            <option value="{{ $yearOption }}" @selected((int) $year === (int) $yearOption)>{{ $yearOption }}</option>
                        @endforeach
                    </select>
                    <select name="category" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="all">Semua Kategori</option>
                        @foreach ($categories as $key => $label)
                            <option value="{{ $key }}" @selected($category === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Filter</button>
                    <a href="{{ route('admin.village-transparency-documents.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 text-center">Reset</a>
                </form>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Tahun</th>
                                <th class="px-3 py-2 text-left">Kategori</th>
                                <th class="px-3 py-2 text-left">Judul</th>
                                <th class="px-3 py-2 text-left">Dokumen</th>
                                <th class="px-3 py-2 text-left">Publikasi</th>
                                <th class="px-3 py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($items as $row)
                                <tr>
                                    <td class="px-3 py-2">{{ $row->fiscal_year ?: '-' }}</td>
                                    <td class="px-3 py-2">{{ $row->categoryLabel() }}</td>
                                    <td class="px-3 py-2">{{ $row->title }}</td>
                                    <td class="px-3 py-2">
                                        @php
                                            $link = $row->documentLink();
                                        @endphp
                                        @if ($link)
                                            <a href="{{ $link }}" target="_blank" rel="noopener" class="text-blue-700 hover:underline">Lihat</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">{{ $row->is_published ? 'Tampil' : 'Draft' }}</td>
                                    <td class="px-3 py-2">
                                        <a href="{{ route('admin.village-transparency-documents.edit', $row) }}" class="text-blue-700 hover:underline">Ubah</a>
                                        <form action="{{ route('admin.village-transparency-documents.destroy', $row) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Hapus dokumen ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada dokumen transparansi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $items->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
