@csrf

<div class="space-y-5">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Judul Pengumuman</label>
        <input id="title" name="title" type="text" value="{{ old('title', $announcement->title ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div>
        <label for="content" class="block text-sm font-medium text-gray-700">Isi Pengumuman</label>
        <textarea id="content" name="content" rows="7" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('content', $announcement->content ?? '') }}</textarea>
    </div>

    <div>
        <label for="reference_url" class="block text-sm font-medium text-gray-700">Link Referensi (opsional)</label>
        <input id="reference_url" name="reference_url" type="url" value="{{ old('reference_url', $announcement->reference_url ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $announcement->is_published ?? false)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span class="text-sm text-gray-700">Tampilkan di website publik</span>
    </label>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.announcements.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>


