<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Sambutan Kepala Desa</h2>
            <a href="{{ route('admin.head-messages.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                Tambah Sambutan
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
                                <th class="px-3 py-2 text-left font-semibold">Foto</th>
                                <th class="px-3 py-2 text-left font-semibold">Nama</th>
                                <th class="px-3 py-2 text-left font-semibold">Jabatan</th>
                                <th class="px-3 py-2 text-left font-semibold">Status</th>
                                <th class="px-3 py-2 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($headMessages as $headMessage)
                                <tr class="border-b">
                                    <td class="px-3 py-3">
                                        @if ($headMessage->photo_path)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($headMessage->photo_path) }}" alt="{{ $headMessage->name }}" class="h-14 w-14 rounded object-cover">
                                        @else
                                            <div class="h-14 w-14 rounded bg-blue-100 text-blue-800 grid place-items-center font-bold">
                                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($headMessage->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">{{ $headMessage->name }}</td>
                                    <td class="px-3 py-3">{{ $headMessage->position }}</td>
                                    <td class="px-3 py-3">
                                        @if ($headMessage->is_published)
                                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">Tayang</span>
                                        @else
                                            <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Draf</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <a href="{{ route('admin.head-messages.edit', $headMessage) }}" class="text-blue-700 hover:underline">Ubah</a>
                                        <form action="{{ route('admin.head-messages.destroy', $headMessage) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Hapus sambutan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-gray-500">Belum ada data sambutan kepala desa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $headMessages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>



