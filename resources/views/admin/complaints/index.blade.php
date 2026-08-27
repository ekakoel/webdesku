<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengaduan Masyarakat</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
                $statusTone = [
                    'baru' => ['bar' => 'bg-amber-500', 'badge' => 'bg-amber-100 text-amber-800'],
                    'diproses' => ['bar' => 'bg-blue-600', 'badge' => 'bg-blue-100 text-blue-800'],
                    'selesai' => ['bar' => 'bg-emerald-600', 'badge' => 'bg-emerald-100 text-emerald-800'],
                    'ditolak' => ['bar' => 'bg-rose-600', 'badge' => 'bg-rose-100 text-rose-800'],
                ];
                $queryString = http_build_query(request()->query());
            @endphp

            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
                <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3"><p class="text-xs text-blue-700">Total Aduan</p><p class="mt-1 text-xl font-bold text-blue-900">{{ number_format((int) ($stats['total'] ?? 0), 0, ',', '.') }}</p></div>
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3"><p class="text-xs text-amber-700">Baru</p><p class="mt-1 text-xl font-bold text-amber-900">{{ number_format((int) ($stats['baru'] ?? 0), 0, ',', '.') }}</p></div>
                <div class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3"><p class="text-xs text-indigo-700">Diproses</p><p class="mt-1 text-xl font-bold text-indigo-900">{{ number_format((int) ($stats['diproses'] ?? 0), 0, ',', '.') }}</p></div>
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3"><p class="text-xs text-emerald-700">Selesai</p><p class="mt-1 text-xl font-bold text-emerald-900">{{ number_format((int) ($stats['selesai'] ?? 0), 0, ',', '.') }}</p></div>
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3"><p class="text-xs text-rose-700">Ditolak</p><p class="mt-1 text-xl font-bold text-rose-900">{{ number_format((int) ($stats['ditolak'] ?? 0), 0, ',', '.') }}</p></div>
                <div class="rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3"><p class="text-xs text-cyan-700">Ada Kontak</p><p class="mt-1 text-xl font-bold text-cyan-900">{{ number_format((int) ($stats['reachable'] ?? 0), 0, ',', '.') }}</p></div>
                <div class="rounded-lg border border-violet-200 bg-violet-50 px-4 py-3"><p class="text-xs text-violet-700">Response Rate</p><p class="mt-1 text-xl font-bold text-violet-900">{{ number_format((float) ($stats['response_rate'] ?? 0), 1, ',', '.') }}%</p></div>
                <div class="rounded-lg border border-teal-200 bg-teal-50 px-4 py-3"><p class="text-xs text-teal-700">Completion Rate</p><p class="mt-1 text-xl font-bold text-teal-900">{{ number_format((float) ($stats['completion_rate'] ?? 0), 1, ',', '.') }}%</p></div>
            </div>

            <div class="mb-4">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <select name="year" class="rounded-md border-gray-300 text-sm">@foreach ($yearOptions as $yearOption)<option value="{{ $yearOption }}" @selected($year === (int) $yearOption)>{{ $yearOption }}</option>@endforeach</select>
                    <select name="month" class="rounded-md border-gray-300 text-sm"><option value="0" @selected($month === 0)>Semua Bulan</option>@for ($monthNumber = 1; $monthNumber <= 12; $monthNumber++)<option value="{{ $monthNumber }}" @selected($month === $monthNumber)>{{ \Carbon\Carbon::create()->month($monthNumber)->translatedFormat('F') }}</option>@endfor</select>
                    <select name="status" class="rounded-md border-gray-300 text-sm"><option value="">Semua Status</option>@foreach (['baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $key => $label)<option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>@endforeach</select>
                    <button type="submit" class="rounded-md bg-blue-700 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-800">Filter</button>
                    <a href="{{ route('admin.complaints.index') }}" class="rounded-md bg-gray-100 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-200">Reset</a>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead><tr class="border-b"><th class="px-3 py-2 text-left font-semibold">Tiket</th><th class="px-3 py-2 text-left font-semibold">Pelapor</th><th class="px-3 py-2 text-left font-semibold">Judul Aduan</th><th class="px-3 py-2 text-left font-semibold">Kontak</th><th class="px-3 py-2 text-left font-semibold">Status</th><th class="px-3 py-2 text-left font-semibold">Tanggal</th><th class="px-3 py-2 text-right font-semibold">Aksi</th></tr></thead>
                        <tbody>
                            @forelse ($complaints as $item)
                                <tr class="border-b">
                                    <td class="px-3 py-3 font-semibold">{{ $item->ticket_code }}</td>
                                    <td class="px-3 py-3">{{ $item->name }}</td>
                                    <td class="px-3 py-3">{{ \Illuminate\Support\Str::limit($item->title, 70) }}</td>
                                    <td class="px-3 py-3 text-xs text-gray-700">{{ $item->whatsapp ?: '-' }}<br>{{ $item->email ?: '-' }}</td>
                                    <td class="px-3 py-3">@php $badgeTone = $statusTone[$item->status]['badge'] ?? 'bg-slate-100 text-slate-800'; @endphp<span class="rounded-full px-2 py-1 text-xs font-semibold {{ $badgeTone }}">{{ $item->statusLabel() }}</span></td>
                                    <td class="px-3 py-3 text-gray-600">{{ $item->submitted_at?->format('d M Y H:i') }}</td>
                                    <td class="px-3 py-3 text-right">
                                        <button type="button" data-complaint-open="complaint-modal-{{ $item->id }}" class="text-blue-700 hover:underline">Detail</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-3 py-6 text-center text-gray-500">Belum ada pengaduan masuk.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $complaints->links() }}</div>
                </div>
            </div>

            @foreach ($complaints as $item)
                <div id="complaint-modal-{{ $item->id }}" data-complaint-modal class="fixed inset-0 z-50 hidden items-center justify-center px-3 py-5">
                    <div class="absolute inset-0 bg-slate-900/55" data-complaint-close></div>
                    <div class="relative z-10 w-full max-w-4xl max-h-[92vh] overflow-auto rounded-xl bg-white shadow-2xl">
                        <div class="flex items-center justify-between border-b px-5 py-4">
                            <h3 class="text-base font-semibold text-gray-800">Detail Pengaduan - {{ $item->ticket_code }}</h3>
                            <button type="button" data-complaint-close class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">Tutup</button>
                        </div>
                        <div class="p-5 grid gap-5 md:grid-cols-2">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800">Data Pelapor</h4>
                                <dl class="mt-2 space-y-1 text-sm text-gray-700">
                                    <div><dt class="inline text-gray-500">Nama:</dt> <dd class="inline">{{ $item->name }}</dd></div>
                                    <div><dt class="inline text-gray-500">WhatsApp:</dt> <dd class="inline">{{ $item->whatsapp ?: '-' }}</dd></div>
                                    <div><dt class="inline text-gray-500">Email:</dt> <dd class="inline">{{ $item->email ?: '-' }}</dd></div>
                                </dl>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800">Data Pengaduan</h4>
                                <dl class="mt-2 space-y-1 text-sm text-gray-700">
                                    <div><dt class="inline text-gray-500">Kategori:</dt> <dd class="inline">{{ \Illuminate\Support\Str::headline($item->category) }}</dd></div>
                                    <div><dt class="inline text-gray-500">Status:</dt> <dd class="inline">{{ $item->statusLabel() }}</dd></div>
                                    <div><dt class="inline text-gray-500">Tanggal:</dt> <dd class="inline">{{ $item->submitted_at?->format('d M Y H:i') }}</dd></div>
                                    <div><dt class="inline text-gray-500">Lokasi:</dt> <dd class="inline">{{ $item->location ?: '-' }}</dd></div>
                                </dl>
                            </div>
                            <div class="md:col-span-2">
                                <h4 class="text-sm font-semibold text-gray-800">Isi Pengaduan</h4>
                                <p class="mt-2 whitespace-pre-line rounded-lg border bg-slate-50 px-3 py-2 text-sm text-gray-700">{{ $item->description }}</p>
                                @if ($item->attachment_url)
                                    <a href="{{ $item->attachment_url }}" target="_blank" class="mt-2 inline-block text-sm text-blue-700 hover:underline">Lihat Lampiran</a>
                                @endif
                            </div>
                        </div>

                        <div class="border-t px-5 py-5">
                            <div class="mt-4">
                                <h5 class="text-sm font-semibold text-gray-800">Riwayat Respon</h5>
                                <div class="mt-2 space-y-2 max-h-56 overflow-auto pr-1">
                                    @forelse ($item->responses as $response)
                                        @php
                                            $fromTone = $statusTone[$response->from_status ?? 'baru']['badge'] ?? 'bg-slate-100 text-slate-800';
                                            $toTone = $statusTone[$response->to_status]['badge'] ?? 'bg-slate-100 text-slate-800';
                                        @endphp
                                        <article class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                                            <p class="flex items-center gap-2 text-xs text-gray-500">
                                                <span>{{ $response->created_at?->format('d M Y H:i') }} · {{ $response->user?->name ?? 'Sistem' }}</span>
                                                @if ($loop->first)
                                                    <span class="rounded-full bg-violet-100 px-2 py-0.5 font-semibold text-violet-800">Terbaru</span>
                                                @endif
                                            </p>
                                            <p class="mt-1 flex items-center gap-2 text-sm font-semibold text-gray-800">
                                                <span class="rounded-full px-2 py-0.5 text-xs {{ $fromTone }}">{{ ucfirst((string) ($response->from_status ?? 'baru')) }}</span>
                                                <span class="text-gray-500">-></span>
                                                <span class="rounded-full px-2 py-0.5 text-xs {{ $toTone }}">{{ ucfirst((string) $response->to_status) }}</span>
                                            </p>
                                            @if ($response->note)<p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $response->note }}</p>@endif
                                        </article>
                                    @empty
                                        <p class="text-sm text-gray-500">Belum ada riwayat respon.</p>
                                    @endforelse
                                </div>
                            </div>

                            <h4 class="mt-5 text-sm font-semibold text-gray-800">Tindak Lanjut / Respon</h4>
                            <form method="POST" action="{{ route('admin.complaints.update', $item) }}{{ $queryString ? ('?'.$queryString) : '' }}" class="mt-3 space-y-3" data-response-form>
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="diproses" data-status-input>
                                <div data-note-wrap>
                                    <label class="block text-sm font-medium text-gray-700">Keterangan / Alasan</label>
                                    <textarea name="status_note" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm" data-note-input placeholder="Contoh: Aduan diterima, diverifikasi, dan dijadwalkan tindak lanjut lapangan pada [tanggal/jam]." required></textarea>
                                    <p class="mt-1 text-xs text-gray-500" data-note-hint>Keterangan wajib diisi untuk setiap respon.</p>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <button
                                            type="button"
                                            class="rounded-md border border-emerald-300 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-800 hover:bg-emerald-100"
                                            data-quick-status="selesai"
                                            data-submit-status="selesai">
                                            Selesai
                                        </button>
                                        {{-- <button type="button" class="rounded-md border border-emerald-300 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-800 hover:bg-emerald-100" data-quick-status="selesai">Selesai</button> --}}
                                        <button
                                            type="button"
                                            class="rounded-md border border-rose-300 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-800 hover:bg-rose-100"
                                            data-quick-status="ditolak">
                                            Ditolak
                                        </button>
                                        {{-- <button type="button" class="rounded-md border border-rose-300 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-800 hover:bg-rose-100" data-quick-status="ditolak">Ditolak</button> --}}
                                        <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Simpan Tindak Lanjut</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
    (function () {
        const modals = Array.from(
            document.querySelectorAll('[data-complaint-modal]')
        );

        const openButtons = Array.from(
            document.querySelectorAll('[data-complaint-open]')
        );

        const closeModal = (modal) => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('is-modal-open');
        };

        const openModal = (modal) => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('is-modal-open');
        };

        openButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const modalId = button.getAttribute('data-complaint-open');
                const modal = document.getElementById(modalId);

                if (modal) {
                    openModal(modal);
                }
            });
        });

        modals.forEach((modal) => {
            modal.querySelectorAll('[data-complaint-close]').forEach((button) => {
                button.addEventListener('click', () => {
                    closeModal(modal);
                });
            });

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal(modal);
                }
            });

            const form = modal.querySelector('[data-response-form]');

            if (!form) {
                return;
            }

            const statusInput = form.querySelector('[data-status-input]');
            const noteInput = form.querySelector('[data-note-input]');
            const noteHint = form.querySelector('[data-note-hint]');
            const quickButtons = Array.from(
                form.querySelectorAll('[data-quick-status]')
            );

            const syncNoteState = () => {
                const currentStatus = statusInput
                    ? statusInput.value
                    : 'diproses';

                if (!noteInput || !statusInput) {
                    return;
                }

                noteInput.required = true;

                const placeholders = {
                    diproses:
                        'Contoh: Aduan diterima, diverifikasi, dan dijadwalkan tindak lanjut lapangan pada [tanggal/jam].',

                    selesai:
                        'Contoh: Tindak lanjut selesai pada [tanggal]. Hasil penanganan: [ringkasan tindakan/solusi].',

                    ditolak:
                        'Contoh: Aduan belum dapat diproses karena [alasan jelas], disarankan [langkah/perbaikan dokumen].',
                };

                noteInput.placeholder =
                    placeholders[currentStatus] ||
                    placeholders.diproses;

                if (noteHint) {
                    noteHint.textContent =
                        currentStatus === 'ditolak'
                            ? 'Jelaskan alasan penolakan dengan jelas agar warga paham langkah selanjutnya.'
                            : currentStatus === 'selesai'
                                ? 'Jelaskan hasil akhir tindak lanjut secara ringkas dan konkret.'
                                : 'Jelaskan progres dan rencana tindak lanjut berikutnya.';
                }
            };

            quickButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const targetStatus = button.getAttribute('data-quick-status');

                    if (!statusInput) {
                        return;
                    }

                    statusInput.value = targetStatus;
                    syncNoteState();

                    // Jika tombol memiliki data-submit-status,
                    // langsung submit form.
                    if (button.hasAttribute('data-submit-status')) {
                        if (noteInput && !noteInput.value.trim()) {
                            noteInput.focus();
                            return;
                        }

                        button.disabled = true;
                        button.textContent = 'Memproses...';

                        form.submit();
                        return;
                    }

                    if (noteInput) {
                        noteInput.focus();
                    }
                });
            });

            /*
             * PENTING:
             * Jangan mengubah status ketika tombol submit diklik.
             *
             * Status sudah ditentukan oleh tombol:
             * - Selesai  -> selesai
             * - Ditolak  -> ditolak
             *
             * Jika tidak memilih quick action, default tetap:
             * - diproses
             */

            syncNoteState();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') {
                return;
            }

            const activeModal = modals.find(
                (modal) => !modal.classList.contains('hidden')
            );

            if (activeModal) {
                closeModal(activeModal);
            }
        });

        const openTicket = @json($activeModalTicket ?? '');

        if (openTicket) {
            const row = Array.from(
                document.querySelectorAll('tr')
            ).find((tr) =>
                tr.textContent.includes(openTicket)
            );

            if (row) {
                const btn = row.querySelector(
                    '[data-complaint-open]'
                );

                if (btn) {
                    btn.click();
                }
            }
        }
    })();
</script>
</x-app-layout>

