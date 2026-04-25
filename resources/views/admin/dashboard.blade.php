<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dasbor Admin Desa
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-blue-900 via-blue-800 to-blue-600 p-6 text-white shadow-sm">
                <p class="text-sm font-semibold text-blue-100">Panel Kendali Web Desa</p>
                <h3 class="mt-1 text-2xl font-bold tracking-tight">Selamat datang, {{ auth()->user()->name }}</h3>
                <p class="mt-2 max-w-3xl text-sm text-blue-100">
                    Kelola konten publik desa dari satu halaman: berita, agenda, layanan, infografis, transparansi, dan profil desa.
                </p>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Berita</p>
                    <h4 class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($stats['news'] ?? 0, 0, ',', '.') }}</h4>
                    <p class="mt-1 text-xs text-slate-500">Publik: {{ number_format($status['news_published'] ?? 0, 0, ',', '.') }} | Draf: {{ number_format($status['news_draft'] ?? 0, 0, ',', '.') }}</p>
                </article>
                <article class="rounded-xl border border-indigo-100 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Agenda</p>
                    <h4 class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($stats['agendas'] ?? 0, 0, ',', '.') }}</h4>
                    <p class="mt-1 text-xs text-slate-500">Publik: {{ number_format($status['agendas_published'] ?? 0, 0, ',', '.') }} | Draf: {{ number_format($status['agendas_draft'] ?? 0, 0, ',', '.') }}</p>
                </article>
                <article class="rounded-xl border border-cyan-100 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Pengajuan Layanan</p>
                    <h4 class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($stats['services_requests'] ?? 0, 0, ',', '.') }}</h4>
                    <p class="mt-1 text-xs text-slate-500">Selesai: {{ number_format($status['requests_done'] ?? 0, 0, ',', '.') }} | Diajukan: {{ number_format($status['requests_pending'] ?? 0, 0, ',', '.') }}</p>
                </article>
                <article class="rounded-xl border border-emerald-100 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Aset Peta</p>
                    <h4 class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($stats['assets'] ?? 0, 0, ',', '.') }}</h4>
                    <p class="mt-1 text-xs text-slate-500">Titik infografis aset desa</p>
                </article>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-slate-900">Akses Cepat Modul</h3>
                        <span class="text-xs font-semibold text-slate-500">Backend Operasional</span>
                    </div>
                    @php
                        $quickGroups = [
                            [
                                'title' => 'Konten Publik',
                                'modules' => [
                                    ['route' => 'admin.news.index', 'active' => 'admin.news.*', 'label' => 'Kelola Berita', 'icon' => 'fa-solid fa-newspaper', 'class' => 'border-blue-100 bg-blue-50 text-blue-800 hover:bg-blue-100'],
                                    ['route' => 'admin.agendas.index', 'active' => 'admin.agendas.*', 'label' => 'Kelola Agenda', 'icon' => 'fa-solid fa-calendar-days', 'class' => 'border-indigo-100 bg-indigo-50 text-indigo-800 hover:bg-indigo-100'],
                                    ['route' => 'admin.announcements.index', 'active' => 'admin.announcements.*', 'label' => 'Kelola Pengumuman', 'icon' => 'fa-solid fa-bullhorn', 'class' => 'border-cyan-100 bg-cyan-50 text-cyan-800 hover:bg-cyan-100'],
                                    ['route' => 'admin.galleries.index', 'active' => 'admin.galleries.*', 'label' => 'Kelola Galeri', 'icon' => 'fa-solid fa-images', 'class' => 'border-teal-100 bg-teal-50 text-teal-800 hover:bg-teal-100'],
                                    ['route' => 'admin.sliders.index', 'active' => 'admin.sliders.*', 'label' => 'Kelola Slider Beranda', 'icon' => 'fa-solid fa-panorama', 'class' => 'border-violet-100 bg-violet-50 text-violet-800 hover:bg-violet-100'],
                                ],
                            ],
                            [
                                'title' => 'Layanan Desa',
                                'modules' => [
                                    ['route' => 'admin.services.index', 'active' => 'admin.services.*', 'label' => 'Kelola Layanan', 'icon' => 'fa-solid fa-handshake', 'class' => 'border-sky-100 bg-sky-50 text-sky-800 hover:bg-sky-100'],
                                    ['route' => 'admin.service-requests.index', 'active' => 'admin.service-requests.*', 'label' => 'Pengajuan Layanan', 'icon' => 'fa-solid fa-file-signature', 'class' => 'border-cyan-100 bg-cyan-50 text-cyan-900 hover:bg-cyan-100'],
                                ],
                            ],
                            [
                                'title' => 'Infografis & Transparansi',
                                'modules' => [
                                    ['route' => 'admin.village-assets.index', 'active' => 'admin.village-assets.*', 'label' => 'Kelola Aset Desa (Map)', 'icon' => 'fa-solid fa-map-location-dot', 'class' => 'border-blue-100 bg-blue-50 text-blue-800 hover:bg-blue-100'],
                                    ['route' => 'admin.village-populations.index', 'active' => 'admin.village-populations.*', 'label' => 'Kelola Penduduk', 'icon' => 'fa-solid fa-people-group', 'class' => 'border-blue-100 bg-blue-50 text-blue-800 hover:bg-blue-100'],
                                    ['route' => 'admin.village-population-stats.index', 'active' => 'admin.village-population-stats.*', 'label' => 'Kelola Statistik Penduduk', 'icon' => 'fa-solid fa-chart-column', 'class' => 'border-sky-100 bg-sky-50 text-sky-800 hover:bg-sky-100'],
                                    ['route' => 'admin.village-apbdes-items.index', 'active' => 'admin.village-apbdes-items.*', 'label' => 'Kelola APBDes', 'icon' => 'fa-solid fa-wallet', 'class' => 'border-indigo-100 bg-indigo-50 text-indigo-800 hover:bg-indigo-100'],
                                    ['route' => 'admin.village-transparency-items.index', 'active' => 'admin.village-transparency-items.*', 'label' => 'Kelola Transparansi', 'icon' => 'fa-solid fa-scale-balanced', 'class' => 'border-blue-100 bg-blue-50 text-blue-900 hover:bg-blue-100'],
                                    ['route' => 'admin.village-infographic-items.index', 'active' => 'admin.village-infographic-items.*', 'label' => 'Kelola Infografis Lainnya', 'icon' => 'fa-solid fa-chart-pie', 'class' => 'border-slate-200 bg-slate-50 text-slate-800 hover:bg-slate-100'],
                                ],
                            ],
                            [
                                'title' => 'Profil & Organisasi Desa',
                                'modules' => [
                                    ['route' => 'admin.village-settings.edit', 'active' => 'admin.village-settings.*', 'label' => 'Pengaturan Desa', 'icon' => 'fa-solid fa-sliders', 'class' => 'border-sky-100 bg-sky-50 text-sky-900 hover:bg-sky-100'],
                                    ['route' => 'admin.head-messages.index', 'active' => 'admin.head-messages.*', 'label' => 'Kelola Sambutan Kades', 'icon' => 'fa-solid fa-comment-dots', 'class' => 'border-blue-100 bg-blue-50 text-blue-900 hover:bg-blue-100'],
                                    ['route' => 'admin.officials.index', 'active' => 'admin.officials.*', 'label' => 'Kelola Aparatur Desa', 'icon' => 'fa-solid fa-id-badge', 'class' => 'border-emerald-100 bg-emerald-50 text-emerald-800 hover:bg-emerald-100'],
                                    ['route' => 'admin.profile-pages.index', 'active' => 'admin.profile-pages.*', 'label' => 'Kelola Halaman Profil', 'icon' => 'fa-solid fa-file-lines', 'class' => 'border-slate-200 bg-slate-50 text-slate-800 hover:bg-slate-100'],
                                    ['route' => 'admin.village-map.edit', 'active' => 'admin.village-map.*', 'label' => 'Kelola Map Desa', 'icon' => 'fa-solid fa-map', 'class' => 'border-rose-100 bg-rose-50 text-rose-800 hover:bg-rose-100'],
                                ],
                            ],
                        ];
                    @endphp
                    <div class="mt-4 space-y-5">
                        @foreach ($quickGroups as $group)
                            <div>
                                <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $group['title'] }}</h4>
                                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                    @foreach ($group['modules'] as $module)
                                        @php
                                            $isActive = request()->routeIs($module['active'] ?? $module['route']);
                                            $tileBaseClass = 'group relative flex min-h-[168px] sm:min-h-[168px] md:min-h-[168px] flex-col items-center justify-between rounded-xl border px-3 py-4 text-center text-sm font-semibold transition duration-200 ease-out';
                                            $tileStateClass = $isActive
                                                ? 'border-blue-500 bg-blue-100 text-blue-900 ring-2 ring-blue-200 shadow-md'
                                                : ($module['class'] . ' hover:-translate-y-0.5 hover:shadow-md hover:ring-2 hover:ring-blue-200');
                                        @endphp
                                        <a href="{{ route($module['route']) }}" class="{{ $tileBaseClass }} {{ $tileStateClass }}">
                                            <span class="flex flex-1 items-center justify-center">
                                                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full border shadow-sm {{ $isActive ? 'border-blue-300 bg-white text-blue-700' : 'border-white/70 bg-white/85' }}">
                                                    <i class="{{ $module['icon'] }} text-xl {{ $isActive ? 'scale-110' : '' }}"></i>
                                                </span>
                                            </span>
                                            <span class="mt-2 line-clamp-2 text-xs leading-4 {{ $isActive ? 'text-slate-900' : '' }}">
                                                {{ $module['label'] }}
                                            </span>
                                            @if ($isActive)
                                                <span class="absolute right-2 top-2 inline-flex h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>

                <aside class="space-y-4">
                    <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-900">Ringkasan Data Desa</h3>
                        <dl class="mt-4 space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <dt class="text-slate-500">Galeri</dt>
                                <dd class="font-semibold text-slate-900">{{ number_format($stats['galleries'] ?? 0, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <dt class="text-slate-500">Tahun Penduduk</dt>
                                <dd class="font-semibold text-slate-900">{{ number_format($stats['population_years'] ?? 0, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <dt class="text-slate-500">Statistik Penduduk</dt>
                                <dd class="font-semibold text-slate-900">{{ number_format($stats['population_stats'] ?? 0, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <dt class="text-slate-500">Item Transparansi</dt>
                                <dd class="font-semibold text-slate-900">{{ number_format($stats['transparency'] ?? 0, 0, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </article>

                    <article class="rounded-xl border border-blue-100 bg-blue-50 p-5 shadow-sm">
                        <h3 class="text-sm font-semibold text-blue-900">Catatan Operasional</h3>
                        <ul class="mt-3 list-disc space-y-2 ps-5 text-xs text-blue-800">
                            <li>Periksa item berstatus draf agar informasi publik tetap mutakhir.</li>
                            <li>Gunakan urutan tampil pada setiap modul agar halaman publik konsisten.</li>
                            <li>Pastikan data APBDes, transparansi, dan layanan diperbarui berkala.</li>
                        </ul>
                    </article>
                </aside>
            </section>
        </div>
    </div>
</x-app-layout>
