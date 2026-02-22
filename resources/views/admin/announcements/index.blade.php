<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Pengumuman</h2>
            <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                Tambah Pengumuman
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
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="px-3 py-2 text-left font-semibold">Judul</th>
                                <th class="px-3 py-2 text-left font-semibold">Status</th>
                                <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                                <th class="px-3 py-2 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($announcements as $announcement)
                                <tr class="border-b">
                                    <td class="px-3 py-3">{{ $announcement->title }}</td>
                                    <td class="px-3 py-3">
                                        @if ($announcement->is_published)
                                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">Terbit</span>
                                        @else
                                            <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Draf</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-gray-600">{{ $announcement->created_at?->format('d M Y H:i') }}</td>
                                    <td class="px-3 py-3 text-right">
                                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="text-blue-700 hover:underline">Ubah</a>
                                        <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Hapus pengumuman ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-6 text-center text-gray-500">Belum ada data pengumuman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $announcements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


