@csrf

<div class="space-y-5">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Kepala Desa</label>
            <input id="name" name="name" type="text" value="{{ old('name', $headMessage->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="position" class="block text-sm font-medium text-gray-700">Jabatan</label>
            <input id="position" name="position" type="text" value="{{ old('position', $headMessage->position ?? 'Kepala Desa') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <div>
        <label for="message" class="block text-sm font-medium text-gray-700">Isi Sambutan</label>
        <textarea id="message" name="message" rows="6" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('message', $headMessage->message ?? '') }}</textarea>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="signature" class="block text-sm font-medium text-gray-700">Teks Penutup / Signature (opsional)</label>
            <input id="signature" name="signature" type="text" value="{{ old('signature', $headMessage->signature ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label for="photo" class="block text-sm font-medium text-gray-700">Foto Kepala Desa (opsional)</label>
            <input id="photo" name="photo" type="file" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @if (!empty($headMessage?->photo_path))
                <p class="mt-1 text-xs text-gray-500">Foto saat ini: {{ $headMessage->photo_path }}</p>
            @endif
            @error('photo')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $headMessage->is_published ?? true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-gray-700">Tampilkan di Beranda</span>
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.head-messages.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>



