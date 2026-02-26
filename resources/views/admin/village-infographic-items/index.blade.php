<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Infografis Lainnya</h2>
            <a href="{{ route('admin.village-infographic-items.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Tambah Data</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
            @endif
            <div class="mb-4 rounded-md border border-gray-200 bg-white p-4">
                <form method="GET" action="{{ route('admin.village-infographic-items.index') }}" class="grid gap-3 md:grid-cols-[1fr_220px_auto_auto]">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul/deskripsi/nilai..." class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <select name="category" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="all" @selected($category === 'all')>Semua Kategori</option>
                        @foreach ($categoryOptions as $key => $label)
                            <option value="{{ $key }}" @selected($category === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Filter</button>
                    <a href="{{ route('admin.village-infographic-items.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 text-center">Reset</a>
                </form>
            </div>
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead><tr class="border-b"><th class="px-3 py-2 text-left">Kategori</th><th class="px-3 py-2 text-left">Judul</th><th class="px-3 py-2 text-left">Nilai</th><th class="px-3 py-2 text-left">Icon</th><th class="px-3 py-2 text-left">Status</th><th class="px-3 py-2 text-right">Aksi</th></tr></thead>
                        <tbody>
                            @forelse ($items as $row)
                                <tr class="border-b">
                                    <td class="px-3 py-3">{{ $row->categoryLabel() }}</td>
                                    <td class="px-3 py-3">{{ $row->title }}</td>
                                    <td class="px-3 py-3">{{ $row->value }} {{ $row->unit }}</td>
                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center gap-2 rounded px-2 py-1 text-xs font-semibold text-white" style="background: {{ $row->color ?: '#64748b' }}">
                                            @if (\Illuminate\Support\Str::startsWith((string) $row->icon, 'fa-'))
                                                <i class="{{ $row->icon }}"></i>
                                            @else
                                                {{ $row->icon ?: '-' }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">{!! $row->is_published ? '<span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">Publik</span>' : '<span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Draf</span>' !!}</td>
                                    <td class="px-3 py-3 text-right">
                                        <a href="{{ route('admin.village-infographic-items.edit', $row) }}" class="text-blue-700 hover:underline">Ubah</a>
                                        <form action="{{ route('admin.village-infographic-items.destroy', $row) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:underline">Hapus</button></form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada data infografis lainnya.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $items->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
