@csrf

<div class="space-y-5">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Judul Peraturan</label>
        <input id="title" name="title" type="text" value="{{ old('title', $regulation->title ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div>
        <label for="content" class="block text-sm font-medium text-gray-700">Ringkasan / Keterangan</label>
        <textarea id="content" name="content" rows="7" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('content', $regulation->content ?? '') }}</textarea>
    </div>

    <div>
        <label for="attachment" class="block text-sm font-medium text-gray-700">File Peraturan</label>
        <input id="attachment" name="attachment" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" @required(empty($regulation)) class="mt-1 block w-full text-sm text-gray-700">
        <p class="mt-1 text-xs text-gray-500">Format: PDF/JPG/JPEG/PNG/WEBP, maksimal 10MB.</p>
        @if (!empty($regulation?->attachment_url))
            <div class="mt-2 rounded border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700">
                <p class="font-semibold">File saat ini: {{ $regulation->attachment_name ?: basename((string) $regulation->attachment_path) }}</p>
                <a href="{{ $regulation->attachment_url }}" target="_blank" rel="noopener" class="text-blue-700 hover:underline">Lihat file</a>
                <label class="ml-3 inline-flex items-center gap-2 text-red-700">
                    <input type="checkbox" name="remove_attachment" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    Hapus lampiran
                </label>
            </div>
        @endif
    </div>

    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $regulation->is_published ?? false)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span class="text-sm text-gray-700">Tampilkan di website publik</span>
    </label>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.regulations.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>

