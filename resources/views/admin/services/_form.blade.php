@csrf

<div class="space-y-5">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nama Layanan</label>
        <input id="name" name="name" type="text" value="{{ old('name', $service->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $service->description ?? '') }}</textarea>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="requirements" class="block text-sm font-medium text-gray-700">Persyaratan</label>
            <textarea id="requirements" name="requirements" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('requirements', $service->requirements ?? '') }}</textarea>
        </div>
        <div>
            <label for="process" class="block text-sm font-medium text-gray-700">Prosedur</label>
            <textarea id="process" name="process" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('process', $service->process ?? '') }}</textarea>
        </div>
    </div>

    <div>
        <label for="icon" class="block text-sm font-medium text-gray-700">Icon Singkat (contoh: SP)</label>
        <input id="icon" name="icon" type="text" value="{{ old('icon', $service->icon ?? '') }}" class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div>
        <label for="sla_target_hours" class="block text-sm font-medium text-gray-700">Target SLA (jam)</label>
        <input id="sla_target_hours" name="sla_target_hours" type="number" min="1" max="720" value="{{ old('sla_target_hours', $service->sla_target_hours ?? 72) }}" required class="mt-1 block w-40 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <p class="mt-1 text-xs text-gray-500">Contoh: 24, 48, 72 jam sesuai jenis layanan.</p>
    </div>

    <div class="space-y-2">
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $service->is_featured ?? false)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-gray-700">Tandai sebagai layanan unggulan</span>
        </label>

        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $service->is_published ?? false)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-gray-700">Tampilkan di website publik</span>
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.services.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
    </div>
</div>


