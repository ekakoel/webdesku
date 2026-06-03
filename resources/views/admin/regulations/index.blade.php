<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Peraturan Desa</h2>
            <a href="{{ route('admin.regulations.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                Tambah Peraturan
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <form method="GET" action="{{ route('admin.regulations.index') }}" class="mb-4 grid gap-3 md:grid-cols-[1fr_auto_auto]">
                        <input type="text" name="q" value="{{ $keyword ?? '' }}" placeholder="Cari judul/isi/nama file..." class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Filter</button>
                        <a href="{{ route('admin.regulations.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Reset</a>
                    </form>

                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="px-3 py-2 text-left font-semibold">Judul</th>
                                <th class="px-3 py-2 text-left font-semibold">Lampiran</th>
                                <th class="px-3 py-2 text-left font-semibold">Status</th>
                                <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                                <th class="px-3 py-2 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($regulations as $regulation)
                                <tr class="border-b">
                                    <td class="px-3 py-3">{{ $regulation->title }}</td>
                                    <td class="px-3 py-3 text-gray-600">
                                        {{ $regulation->attachment_name ?: ($regulation->attachment_path ? basename((string) $regulation->attachment_path) : '-') }}
                                    </td>
                                    <td class="px-3 py-3">
                                        @if ($regulation->is_published)
                                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">Terbit</span>
                                        @else
                                            <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Draf</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-gray-600">{{ $regulation->created_at?->format('d M Y H:i') }}</td>
                                    <td class="px-3 py-3 text-right">
                                        <a href="{{ route('admin.regulations.edit', $regulation) }}" class="text-blue-700 hover:underline">Ubah</a>
                                        <form action="{{ route('admin.regulations.destroy', $regulation) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Hapus peraturan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-gray-500">Belum ada data peraturan desa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $regulations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

