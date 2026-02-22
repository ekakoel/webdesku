@csrf

<div class="space-y-5">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Judul Slider</label>
            <input id="title" name="title" type="text" value="{{ old('title', $slider->title ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="image_alt" class="block text-sm font-medium text-gray-700">Alt Text Gambar</label>
            <input id="image_alt" name="image_alt" type="text" value="{{ old('image_alt', $slider->image_alt ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <div>
        <label for="caption" class="block text-sm font-medium text-gray-700">Caption</label>
        <textarea id="caption" name="caption" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('caption', $slider->caption ?? '') }}</textarea>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label for="cta_text" class="block text-sm font-medium text-gray-700">Teks Tombol (opsional)</label>
            <input id="cta_text" name="cta_text" type="text" value="{{ old('cta_text', $slider->cta_text ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="md:col-span-2">
            <label for="cta_url" class="block text-sm font-medium text-gray-700">Link Tombol (opsional)</label>
            <input id="cta_url" name="cta_url" type="url" value="{{ old('cta_url', $slider->cta_url ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="image" class="block text-sm font-medium text-gray-700">Upload Gambar</label>
            <input id="image" name="image" type="file" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @if (!empty($slider?->image_path))
                <p class="mt-1 text-xs text-gray-500">Gambar saat ini: {{ $slider->image_path }}</p>
            @endif
            @error('image')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700">Urutan Tampil</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $slider->sort_order ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <div class="space-y-2">
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $slider->is_active ?? true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-gray-700">Aktifkan slider</span>
        </label>
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $slider->is_published ?? true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-gray-700">Tampilkan di Beranda</span>
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.sliders.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>


