<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Data Penduduk</h2>
            <a href="{{ route('admin.village-populations.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Tambah Data</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
            @endif
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead><tr class="border-b"><th class="px-3 py-2 text-left">Tahun</th><th class="px-3 py-2 text-left">Laki-laki</th><th class="px-3 py-2 text-left">Perempuan</th><th class="px-3 py-2 text-left">Total</th><th class="px-3 py-2 text-left">Status</th><th class="px-3 py-2 text-right">Aksi</th></tr></thead>
                        <tbody>
                            @forelse ($items as $row)
                                <tr class="border-b">
                                    <td class="px-3 py-3">{{ $row->year }}</td>
                                    <td class="px-3 py-3">{{ number_format($row->male, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3">{{ number_format($row->female, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3">{{ number_format($row->total(), 0, ',', '.') }}</td>
                                    <td class="px-3 py-3">{!! $row->is_published ? '<span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">Publik</span>' : '<span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Draf</span>' !!}</td>
                                    <td class="px-3 py-3 text-right">
                                        <a href="{{ route('admin.village-populations.edit', $row) }}" class="text-blue-700 hover:underline">Ubah</a>
                                        <form action="{{ route('admin.village-populations.destroy', $row) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:underline">Hapus</button></form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada data penduduk.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $items->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

