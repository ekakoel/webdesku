@csrf
<div class="space-y-5">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="fiscal_year" class="block text-sm font-medium text-gray-700">Tahun</label>
            <input id="fiscal_year" name="fiscal_year" type="number" min="1990" max="2100" value="{{ old('fiscal_year', $item->fiscal_year ?? now()->year) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700">Urutan Tampil</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $item->sort_order ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>
    <div class="grid gap-4 md:grid-cols-3">
        <div class="md:col-span-2">
            <label for="label" class="block text-sm font-medium text-gray-700">Label Penggunaan Lahan</label>
            <input id="label" name="label" type="text" maxlength="120" value="{{ old('label', $item->label ?? '') }}" required placeholder="Contoh: Permukiman" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="area_value" class="block text-sm font-medium text-gray-700">Luas</label>
            <input id="area_value" name="area_value" type="number" step="0.01" min="0" value="{{ old('area_value', $item->area_value ?? 0) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>
    <div>
        <label for="unit" class="block text-sm font-medium text-gray-700">Satuan</label>
        <input id="unit" name="unit" type="text" maxlength="20" value="{{ old('unit', $item->unit ?? 'Ha') }}" placeholder="Contoh: Ha" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published ?? true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span class="text-sm text-gray-700">Tampilkan di Gambaran Umum Desa</span>
    </label>
    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">{{ $submitLabel }}</button>
        <a href="{{ route('admin.village-land-use-areas.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>
