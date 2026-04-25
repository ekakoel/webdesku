@csrf
<div class="space-y-5">
    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label for="fiscal_year" class="block text-sm font-medium text-gray-700">Tahun Anggaran</label>
            <input id="fiscal_year" name="fiscal_year" type="number" min="1990" max="2100" value="{{ old('fiscal_year', $item->fiscal_year ?? now()->year) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="md:col-span-2">
            <label for="category" class="block text-sm font-medium text-gray-700">Kategori Transparansi</label>
            <select id="category" name="category" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach ($categories as $key => $label)
                    <option value="{{ $key }}" @selected(old('category', $item->category ?? 'laporan') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Judul Informasi</label>
        <input id="title" name="title" type="text" value="{{ old('title', $item->title ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700">Nominal (Opsional)</label>
            <input id="amount" name="amount" type="number" min="0" value="{{ old('amount', $item->amount ?? '') }}" placeholder="Contoh: 1250000000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700">Urutan Tampil</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $item->sort_order ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <div>
        <label for="document_url" class="block text-sm font-medium text-gray-700">Link Dokumen (Opsional)</label>
        <input id="document_url" name="document_url" type="url" value="{{ old('document_url', $item->document_url ?? '') }}" placeholder="https://..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $item->description ?? '') }}</textarea>
    </div>

    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published ?? true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span class="text-sm text-gray-700">Tampilkan di halaman Transparansi publik</span>
    </label>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">{{ $submitLabel }}</button>
        <a href="{{ route('admin.village-transparency-items.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>

