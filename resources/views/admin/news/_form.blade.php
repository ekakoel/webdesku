@csrf

<div class="space-y-5">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
        <input id="title" name="title" type="text" value="{{ old('title', $news->title ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="content" class="block text-sm font-medium text-gray-700">Konten</label>
        <textarea id="content" name="content" rows="8" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('content', $news->content ?? '') }}</textarea>
        @error('content')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="thumbnail" class="block text-sm font-medium text-gray-700">Cover Berita (Upload Gambar)</label>
        <input id="thumbnail" name="thumbnail" type="file" accept="image/png,image/jpeg,image/webp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <p class="mt-1 text-xs text-gray-500">Format: JPG/PNG/WEBP, maksimal 4MB. Sistem akan konversi otomatis ke WEBP terkompresi.</p>
        @if (!empty($news?->thumbnail_url))
            <div class="mt-2 space-y-2">
                <img src="{{ $news->thumbnail_url }}" alt="{{ $news->title }}" class="h-28 w-48 rounded-md object-cover">
                <label class="inline-flex items-center gap-2 text-sm text-red-700">
                    <input type="hidden" name="remove_cover" value="0">
                    <input type="checkbox" name="remove_cover" value="1" class="rounded border-red-300 text-red-600 focus:ring-red-500">
                    Hapus cover saat simpan
                </label>
            </div>
        @else
            <input type="hidden" name="remove_cover" value="0">
        @endif
        @error('thumbnail')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('remove_cover')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="gallery_images" class="block text-sm font-medium text-gray-700">Gambar Tambahan Berita (maks 4)</label>
        <input id="gallery_images" name="gallery_images[]" type="file" accept="image/png,image/jpeg,image/webp" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <p class="mt-1 text-xs text-gray-500">Bisa upload beberapa gambar sekaligus. Sistem menyimpan maksimal 4 gambar per berita.</p>
        @error('gallery_images')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('gallery_images.*')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    @if (!empty($news?->images) && $news->images->isNotEmpty())
        <div>
            <p class="block text-sm font-medium text-gray-700">Gambar Tambahan Saat Ini</p>
            <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ($news->images as $image)
                    <label class="rounded-md border border-gray-200 p-2">
                        <img src="{{ $image->image_url }}" alt="Gambar berita" class="h-24 w-full rounded object-cover" loading="lazy" decoding="async">
                        <span class="mt-2 inline-flex items-center gap-2 text-xs text-gray-700">
                            <input type="checkbox" name="remove_image_ids[]" value="{{ $image->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            Hapus
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $news->is_published ?? false)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span class="text-sm text-gray-700">Publish ke website publik</span>
    </label>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.news.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>


