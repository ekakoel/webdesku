<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Slider Beranda</h2>
            <a href="{{ route('admin.sliders.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                Tambah Slider
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
                                <th class="px-3 py-2 text-left font-semibold">Preview</th>
                                <th class="px-3 py-2 text-left font-semibold">Judul</th>
                                <th class="px-3 py-2 text-left font-semibold">Urutan</th>
                                <th class="px-3 py-2 text-left font-semibold">Status</th>
                                <th class="px-3 py-2 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sliders as $slider)
                                <tr class="border-b">
                                    <td class="px-3 py-3">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($slider->image_path) }}" alt="{{ $slider->image_alt ?: $slider->title }}" class="h-14 w-24 rounded object-cover">
                                    </td>
                                    <td class="px-3 py-3">{{ $slider->title ?: '-' }}</td>
                                    <td class="px-3 py-3">{{ $slider->sort_order }}</td>
                                    <td class="px-3 py-3">
                                        @if ($slider->is_active && $slider->is_published)
                                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">Tayang</span>
                                        @elseif ($slider->is_active)
                                            <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Draf</span>
                                        @else
                                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <a href="{{ route('admin.sliders.edit', $slider) }}" class="text-blue-700 hover:underline">Ubah</a>
                                        <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Hapus slider ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-gray-500">Belum ada slider. Tambahkan untuk menampilkan banner Beranda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $sliders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>



