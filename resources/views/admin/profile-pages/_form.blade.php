@csrf
<input type="hidden" name="slug" value="{{ old('slug', $slug) }}">

<div class="space-y-5">
    <div>
        <label class="block text-sm font-medium text-gray-700">Halaman</label>
        <div class="mt-1 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-700">
            {{ $label }} (slug: {{ $slug }})
        </div>
    </div>
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
        <input id="title" name="title" type="text" value="{{ old('title', $profilePage->title ?? $label) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label for="subtitle" class="block text-sm font-medium text-gray-700">Subjudul</label>
        <textarea id="subtitle" name="subtitle" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('subtitle', $profilePage->subtitle ?? '') }}</textarea>
    </div>
    <div>
        <label for="content" class="block text-sm font-medium text-gray-700">Konten Utama</label>
        <textarea id="content" name="content" rows="8" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('content', $profilePage->content ?? '') }}</textarea>
    </div>
    <div>
        <label for="source_url" class="block text-sm font-medium text-gray-700">URL Referensi</label>
        <input id="source_url" name="source_url" type="url" value="{{ old('source_url', $profilePage->source_url ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label for="payload_json" class="block text-sm font-medium text-gray-700">Payload JSON (opsional, untuk data terstruktur/chart)</label>
        <textarea id="payload_json" name="payload_json" rows="10" class="mt-1 block w-full rounded-md border-gray-300 font-mono text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('payload_json', isset($profilePage) && $profilePage->payload ? json_encode($profilePage->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '') }}</textarea>
    </div>
    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $profilePage->is_published ?? true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <span class="text-sm text-gray-700">Publikasikan di website</span>
    </label>
    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">{{ $submitLabel }}</button>
        <a href="{{ route('admin.profile-pages.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>

