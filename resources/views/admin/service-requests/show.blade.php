<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Pengajuan Layanan</h2>
            <a href="{{ route('admin.service-requests.index') }}" class="text-sm text-blue-700 hover:underline">Kembali</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 grid gap-6 md:grid-cols-2">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Informasi Pemohon</h3>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div><dt class="font-medium text-gray-500">Nomor Tiket</dt><dd>{{ $serviceRequest->ticket_code }}</dd></div>
                            <div><dt class="font-medium text-gray-500">Layanan</dt><dd>{{ $serviceRequest->service?->name ?? '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-500">Nama</dt><dd>{{ $serviceRequest->applicant_name }}</dd></div>
                            <div><dt class="font-medium text-gray-500">NIK</dt><dd>{{ $serviceRequest->nik }}</dd></div>
                            <div><dt class="font-medium text-gray-500">No. KK</dt><dd>{{ $serviceRequest->kk_number ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-500">Telepon</dt><dd>{{ $serviceRequest->phone }}</dd></div>
                            <div><dt class="font-medium text-gray-500">Email</dt><dd>{{ $serviceRequest->email ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-500">Alamat</dt><dd>{{ $serviceRequest->address }}</dd></div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Informasi Pengajuan</h3>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div><dt class="font-medium text-gray-500">Status Saat Ini</dt><dd>{{ ucfirst($serviceRequest->status) }}</dd></div>
                            <div><dt class="font-medium text-gray-500">Tanggal Ajukan</dt><dd>{{ $serviceRequest->submitted_at?->format('d M Y H:i') }}</dd></div>
                            <div><dt class="font-medium text-gray-500">Tanggal Proses</dt><dd>{{ $serviceRequest->processed_at?->format('d M Y H:i') ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-500">Keterangan Warga</dt><dd>{{ $serviceRequest->description ?: '-' }}</dd></div>
                            <div><dt class="font-medium text-gray-500">Lampiran</dt><dd>
                                @if ($serviceRequest->attachment_url)
                                    <a href="{{ $serviceRequest->attachment_url }}" target="_blank" class="text-blue-700 hover:underline">Lihat Lampiran</a>
                                @else
                                    -
                                @endif
                            </dd></div>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-base font-semibold text-gray-800">Proses Pengajuan</h3>
                    <form action="{{ route('admin.service-requests.update', $serviceRequest) }}" method="POST" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                                @foreach (['diajukan' => 'Diajukan', 'diverifikasi' => 'Diverifikasi', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $key => $label)
                                    <option value="{{ $key }}" @selected(old('status', $serviceRequest->status) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catatan Aparat</label>
                            <textarea name="status_note" rows="4" class="mt-1 block w-full rounded-md border-gray-300 text-sm">{{ old('status_note', $serviceRequest->status_note) }}</textarea>
                            @error('status_note') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-center justify-between">
                            <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Simpan Perubahan</button>
                            <button form="delete-request-form" type="submit" class="text-sm text-red-600 hover:underline" onclick="return confirm('Hapus pengajuan ini?')">Hapus Pengajuan</button>
                        </div>
                    </form>
                    <form id="delete-request-form" action="{{ route('admin.service-requests.destroy', $serviceRequest) }}" method="POST">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
