@csrf
<div class="space-y-5">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="fiscal_year" class="block text-sm font-medium text-gray-700">Tahun Anggaran</label>
            <input id="fiscal_year" name="fiscal_year" type="number" min="1990" max="2100" value="{{ old('fiscal_year', $item->fiscal_year ?? now()->year) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700">Jenis</label>
            <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach ($types as $key => $label)
                    <option value="{{ $key }}" @selected(old('type', $item->type ?? 'pendapatan') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
            <input id="category" name="category" type="text" value="{{ old('category', $item->category ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700">Nilai (Rp)</label>
            <input id="amount" name="amount" type="number" min="0" value="{{ old('amount', $item->amount ?? 0) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>
    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $item->notes ?? '') }}</textarea>
    </div>
    <div>
        <label for="sort_order" class="block text-sm font-medium text-gray-700">Urutan Tampil</label>
        <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $item->sort_order ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published ?? true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span class="text-sm text-gray-700">Tampilkan di tab APBDes</span>
    </label>
    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">{{ $submitLabel }}</button>
        <a href="{{ route('admin.village-apbdes-items.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>

