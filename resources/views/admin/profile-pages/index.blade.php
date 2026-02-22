<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Halaman Profil Desa</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="px-3 py-2 text-left font-semibold">Halaman</th>
                                <th class="px-3 py-2 text-left font-semibold">Judul Aktif</th>
                                <th class="px-3 py-2 text-left font-semibold">Status</th>
                                <th class="px-3 py-2 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pages as $row)
                                @php $item = $row['page']; @endphp
                                <tr class="border-b">
                                    <td class="px-3 py-3">{{ $row['label'] }}</td>
                                    <td class="px-3 py-3">{{ $item?->title ?? '-' }}</td>
                                    <td class="px-3 py-3">
                                        @if ($item?->is_published)
                                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">Publik</span>
                                        @elseif($item)
                                            <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Draf</span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">Belum dibuat</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        @if ($item)
                                            <a href="{{ route('admin.profile-pages.edit', $item) }}" class="text-blue-700 hover:underline">Ubah</a>
                                            <form action="{{ route('admin.profile-pages.destroy', $item) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Hapus konten halaman ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.profile-pages.create', ['slug' => $row['slug']]) }}" class="text-blue-700 hover:underline">Buat Konten</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

