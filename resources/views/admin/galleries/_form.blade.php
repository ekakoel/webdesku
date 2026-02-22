@csrf

<div class="space-y-5">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Judul Galeri</label>
        <input id="title" name="title" type="text" value="{{ old('title', $gallery->title ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div>
        <label for="caption" class="block text-sm font-medium text-gray-700">Caption</label>
        <textarea id="caption" name="caption" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('caption', $gallery->caption ?? '') }}</textarea>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="image" class="block text-sm font-medium text-gray-700">Upload Gambar</label>
            <input id="image" name="image" type="file" accept="image/jpeg,image/jpg,image/png,image/webp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" @required(empty($gallery))>
            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, WEBP. Maksimal 4MB. File otomatis dikompres ke WEBP dan dibuatkan thumbnail.</p>

            @if (!empty($gallery?->image_url))
                <div class="mt-3">
                    <img src="{{ $gallery->thumbnail_url ?? $gallery->image_url }}" alt="{{ $gallery->title }}" class="h-28 w-44 rounded-md object-cover border border-gray-200">
                    <label class="mt-2 inline-flex items-center gap-2">
                        <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm text-gray-700">Hapus gambar saat ini</span>
                    </label>
                </div>
            @endif
        </div>
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
            <input id="category" name="category" type="text" value="{{ old('category', $gallery->category ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $gallery->is_published ?? false)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span class="text-sm text-gray-700">Tampilkan di website publik</span>
    </label>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.galleries.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>


