<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Layanan</h2>
            <a href="{{ route('admin.services.create') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                Tambah Layanan
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="px-3 py-2 text-left font-semibold">Nama</th>
                                <th class="px-3 py-2 text-left font-semibold">Target SLA</th>
                                <th class="px-3 py-2 text-left font-semibold">Unggulan</th>
                                <th class="px-3 py-2 text-left font-semibold">Pengajuan</th>
                                <th class="px-3 py-2 text-left font-semibold">Status</th>
                                <th class="px-3 py-2 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($services as $service)
                                <tr class="border-b">
                                    <td class="px-3 py-3">{{ $service->name }}</td>
                                    <td class="px-3 py-3">{{ number_format((int) ($service->sla_target_hours ?? 72), 0, ',', '.') }} jam</td>
                                    <td class="px-3 py-3">
                                        @if ($service->is_featured)
                                            <span class="rounded-full bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-800">Ya</span>
                                        @else
                                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">Tidak</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="rounded-full bg-sky-100 px-2 py-1 text-xs font-semibold text-sky-800">
                                            {{ number_format((int) ($service->requests_count ?? 0), 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">
                                        @if ($service->is_published)
                                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">Terbit</span>
                                        @else
                                            <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Draf</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <a href="{{ route('services.show', $service->slug) }}" class="text-sky-700 hover:underline" target="_blank">Lihat</a>
                                        <span class="mx-2 text-gray-300">|</span>
                                        <a href="{{ route('admin.services.edit', $service) }}" class="text-blue-700 hover:underline">Ubah</a>
                                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Hapus layanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada data layanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $services->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


