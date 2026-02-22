<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengajuan Layanan Warga</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3">
                    <p class="text-xs text-blue-700">Total Pengajuan</p>
                    <p class="mt-1 text-xl font-bold text-blue-900">{{ number_format((int) ($stats['total_requests'] ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">
                    <p class="text-xs text-emerald-700">Jumlah Selesai</p>
                    <p class="mt-1 text-xl font-bold text-emerald-900">{{ number_format((int) ($stats['completed_requests'] ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3">
                    <p class="text-xs text-indigo-700">Kepatuhan SLA (target per layanan)</p>
                    <p class="mt-1 text-xl font-bold text-indigo-900">{{ number_format((float) ($stats['sla_percent'] ?? 0), 1, ',', '.') }}%</p>
                </div>
                <div class="rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3">
                    <p class="text-xs text-cyan-700">Rata-rata Waktu Proses</p>
                    <p class="mt-1 text-xl font-bold text-cyan-900">{{ number_format((float) ($stats['avg_hours_overall'] ?? 0), 2, ',', '.') }} jam</p>
                </div>
            </div>

            <div class="mb-4">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <select name="status" class="rounded-md border-gray-300 text-sm">
                        <option value="">Semua Status</option>
                        @foreach (['diajukan' => 'Diajukan', 'diverifikasi' => 'Diverifikasi', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $key => $label)
                            <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-md border-gray-300 text-sm">
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-md border-gray-300 text-sm">
                    <button type="submit" class="rounded-md bg-blue-700 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-800">Filter</button>
                    <a href="{{ route('admin.service-requests.index') }}" class="rounded-md bg-gray-100 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-200">Reset</a>
                    <a href="{{ route('admin.service-requests.export.excel', ['status' => $status, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="rounded-md bg-emerald-700 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-800">Export Excel</a>
                    <a href="{{ route('admin.service-requests.export.pdf', ['status' => $status, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="rounded-md bg-rose-700 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-800">Export PDF</a>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="px-3 py-2 text-left font-semibold">Tiket</th>
                                <th class="px-3 py-2 text-left font-semibold">Layanan</th>
                                <th class="px-3 py-2 text-left font-semibold">Pemohon</th>
                                <th class="px-3 py-2 text-left font-semibold">Status</th>
                                <th class="px-3 py-2 text-left font-semibold">Tanggal</th>
                                <th class="px-3 py-2 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requests as $item)
                                <tr class="border-b">
                                    <td class="px-3 py-3 font-semibold">{{ $item->ticket_code }}</td>
                                    <td class="px-3 py-3">{{ $item->service?->name ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ $item->applicant_name }}</td>
                                    <td class="px-3 py-3">
                                        <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">{{ ucfirst($item->status) }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-gray-600">{{ $item->submitted_at?->format('d M Y H:i') }}</td>
                                    <td class="px-3 py-3 text-right">
                                        <a href="{{ route('admin.service-requests.show', $item) }}" class="text-blue-700 hover:underline">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada pengajuan layanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>

            <div class="mt-5 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <h3 class="mb-3 text-base font-semibold text-gray-800">Rata-rata Waktu Proses per Layanan</h3>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="px-3 py-2 text-left font-semibold">Layanan</th>
                                <th class="px-3 py-2 text-left font-semibold">Pengajuan Selesai</th>
                                <th class="px-3 py-2 text-left font-semibold">Target SLA</th>
                                <th class="px-3 py-2 text-left font-semibold">Rata-rata Jam</th>
                                <th class="px-3 py-2 text-left font-semibold">Kepatuhan SLA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($serviceAverages as $row)
                                <tr class="border-b">
                                    <td class="px-3 py-3">{{ $row['service_name'] }}</td>
                                    <td class="px-3 py-3">{{ number_format((int) $row['completed_count'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-3">{{ number_format((int) $row['sla_target_hours'], 0, ',', '.') }} jam</td>
                                    <td class="px-3 py-3">{{ number_format((float) $row['avg_hours'], 2, ',', '.') }} jam</td>
                                    <td class="px-3 py-3">{{ number_format((float) $row['sla_percent'], 1, ',', '.') }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-gray-500">Belum ada data layanan berstatus selesai untuk dihitung.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
