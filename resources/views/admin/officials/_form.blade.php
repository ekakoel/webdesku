@csrf

<div class="space-y-5">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Aparatur</label>
            <input id="name" name="name" type="text" value="{{ old('name', $official->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="position" class="block text-sm font-medium text-gray-700">Jabatan</label>
            <input id="position" name="position" type="text" value="{{ old('position', $official->position ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="unit" class="block text-sm font-medium text-gray-700">Unit / Dusun (opsional)</label>
            <input id="unit" name="unit" type="text" value="{{ old('unit', $official->unit ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700">Urutan Tampil</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $official->sort_order ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <div>
        <label for="bio" class="block text-sm font-medium text-gray-700">Deskripsi Singkat (opsional)</label>
        <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('bio', $official->bio ?? '') }}</textarea>
    </div>

    <div>
        <label for="photo" class="block text-sm font-medium text-gray-700">Foto Aparatur (opsional)</label>
        <input id="photo" name="photo" type="file" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @if (!empty($official?->photo_path))
            <p class="mt-1 text-xs text-gray-500">Foto saat ini: {{ $official->photo_path }}</p>
        @endif
        @error('photo')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="is_highlighted" value="0">
            <input type="checkbox" name="is_highlighted" value="1" @checked(old('is_highlighted', $official->is_highlighted ?? false)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-gray-700">Tandai sebagai aparatur utama</span>
        </label>
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $official->is_published ?? true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-gray-700">Tampilkan di Beranda</span>
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.officials.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>



