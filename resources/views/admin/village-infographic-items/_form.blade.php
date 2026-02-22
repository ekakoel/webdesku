@csrf
<div class="space-y-5">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
            <input id="title" name="title" type="text" value="{{ old('title', $item->title ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="value" class="block text-sm font-medium text-gray-700">Nilai</label>
            <input id="value" name="value" type="text" value="{{ old('value', $item->value ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
            <input id="unit" name="unit" type="text" value="{{ old('unit', $item->unit ?? '') }}" placeholder="contoh: unit, %, orang, km" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="icon" class="block text-sm font-medium text-gray-700">Icon (teks singkat)</label>
            <input id="icon" name="icon" type="text" value="{{ old('icon', $item->icon ?? '') }}" placeholder="contoh: UMKM, BUMDES" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="color" class="block text-sm font-medium text-gray-700">Warna Badge</label>
            <input id="color" name="color" type="text" value="{{ old('color', $item->color ?? '#64748b') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700">Urutan Tampil</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $item->sort_order ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $item->description ?? '') }}</textarea>
    </div>
    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published ?? true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span class="text-sm text-gray-700">Tampilkan di tab Lainnya</span>
    </label>
    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">{{ $submitLabel }}</button>
        <a href="{{ route('admin.village-infographic-items.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>

