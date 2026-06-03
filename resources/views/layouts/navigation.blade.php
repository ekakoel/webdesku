@php
    $moduleManager = \App\Support\ModuleManager::class;
    $servicesModuleEnabled = $moduleManager::isEnabled('services');
    $newsModuleEnabled = $moduleManager::isEnabled('news');
    $agendaModuleEnabled = $moduleManager::isEnabled('agendas');
    $announcementModuleEnabled = $moduleManager::isEnabled('announcements');
    $regulationsModuleEnabled = $moduleManager::isEnabled('regulations');
    $galleryModuleEnabled = $moduleManager::isEnabled('galleries');
    $infographicsModuleEnabled = $moduleManager::isEnabled('infographics');
    $transparencyModuleEnabled = $moduleManager::isEnabled('transparency');
    $profileModuleEnabled = $moduleManager::isEnabled('profile');
    $complaintsModuleEnabled = $moduleManager::isEnabled('complaints');
    $adminMenuGroups = [
        'Konten Publik' => [
            ...($newsModuleEnabled ? [['route' => 'admin.news.index', 'label' => 'Kelola Berita']] : []),
            ...($agendaModuleEnabled ? [['route' => 'admin.agendas.index', 'label' => 'Kelola Agenda']] : []),
            ...($announcementModuleEnabled ? [['route' => 'admin.announcements.index', 'label' => 'Kelola Pengumuman']] : []),
            ...($regulationsModuleEnabled ? [['route' => 'admin.regulations.index', 'label' => 'Kelola Peraturan Desa']] : []),
            ...($galleryModuleEnabled ? [['route' => 'admin.galleries.index', 'label' => 'Kelola Galeri']] : []),
            ['route' => 'admin.sliders.index', 'label' => 'Kelola Slider Beranda'],
        ],
        'Layanan Desa' => [
            ...($servicesModuleEnabled ? [
                ['route' => 'admin.services.index', 'label' => 'Kelola Layanan'],
                ['route' => 'admin.service-requests.index', 'label' => 'Pengajuan Layanan'],
            ] : []),
            ...($complaintsModuleEnabled ? [['route' => 'admin.complaints.index', 'label' => 'Pengaduan Masyarakat']] : []),
        ],
        'Infografis & Transparansi' => [
            ...($infographicsModuleEnabled ? [
                ['route' => 'admin.village-assets.index', 'label' => 'Kelola Aset Desa (Map)'],
                ['route' => 'admin.village-populations.index', 'label' => 'Kelola Penduduk (Infografis)'],
                ['route' => 'admin.village-population-stats.index', 'label' => 'Kelola Statistik Penduduk'],
                ['route' => 'admin.village-infographic-items.index', 'label' => 'Kelola Infografis Lainnya'],
            ] : []),
            ...($transparencyModuleEnabled ? [
                ['route' => 'admin.village-apbdes-items.index', 'label' => 'Kelola APBDes (Infografis)'],
                ['route' => 'admin.village-apbdes-documents.index', 'label' => 'Dokumen/Laporan APBDes'],
                ['route' => 'admin.village-transparency-items.index', 'label' => 'Kelola Transparansi Desa'],
                ['route' => 'admin.village-transparency-documents.index', 'label' => 'Kelola Dokumen Transparansi'],
            ] : []),
        ],
        'Profil & Organisasi Desa' => [
            ['route' => 'admin.village-settings.edit', 'label' => 'Pengaturan Desa'],
            ['route' => 'admin.data-lineage.index', 'label' => 'Data Lineage & Governance'],
            ...($profileModuleEnabled ? [
                ['route' => 'admin.head-messages.index', 'label' => 'Kelola Sambutan Kades'],
                ['route' => 'admin.officials.index', 'label' => 'Kelola Aparatur Desa'],
                ['route' => 'admin.profile-pages.index', 'label' => 'Kelola Halaman Profil Desa'],
                ['route' => 'admin.village-land-use-areas.index', 'label' => 'Luas Wilayah Menurut Penggunaan'],
            ] : []),
            ['route' => 'admin.village-map.edit', 'label' => 'Kelola Map Desa'],
        ],
    ];
    $adminMenuGroups = collect($adminMenuGroups)
        ->filter(fn (array $groupItems) => count($groupItems) > 0)
        ->toArray();
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex flex-1 items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:-my-px sm:ms-10 sm:flex sm:flex-1 sm:items-center sm:justify-center">
                    <div class="flex items-center space-x-6">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        @if (auth()->user()?->isSuperAdmin())
                            <x-nav-link :href="route('super-admin.modules.index')" :active="request()->routeIs('super-admin.modules.*')">
                                {{ __('Super Admin') }}
                            </x-nav-link>
                        @endif
                        @if (auth()->user()?->isAparat() || auth()->user()?->isSuperAdmin())
                            @foreach ($adminMenuGroups as $groupLabel => $groupItems)
                                <x-dropdown align="left" width="64">
                                    <x-slot name="trigger">
                                        <button type="button" class="inline-flex items-center rounded-md border border-transparent px-2 py-2 text-sm font-medium leading-4 text-gray-600 transition hover:text-gray-800 focus:outline-none">
                                            <span>{{ $groupLabel }}</span>
                                            <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        @foreach ($groupItems as $item)
                                            <x-dropdown-link :href="route($item['route'])" :active="request()->routeIs($item['route'])">
                                                {{ __($item['label']) }}
                                            </x-dropdown-link>
                                        @endforeach
                                    </x-slot>
                                </x-dropdown>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if (auth()->user()?->isAparat() || auth()->user()?->isSuperAdmin())
                            <x-dropdown-link :href="route('admin.dashboard')">
                                {{ __('Admin Dashboard') }}
                            </x-dropdown-link>
                        @endif

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @if (auth()->user()?->isAparat() || auth()->user()?->isSuperAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                    {{ __('Admin Desa') }}
                </x-responsive-nav-link>
                @foreach ($adminMenuGroups as $groupLabel => $groupItems)
                    <div class="px-4 pt-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        {{ $groupLabel }}
                    </div>
                    @foreach ($groupItems as $item)
                        <x-responsive-nav-link :href="route($item['route'])" :active="request()->routeIs($item['route'])">
                            {{ __($item['label']) }}
                        </x-responsive-nav-link>
                    @endforeach
                @endforeach
            @endif
            @if (auth()->user()?->isSuperAdmin())
                <x-responsive-nav-link :href="route('super-admin.modules.index')" :active="request()->routeIs('super-admin.modules.*')">
                    {{ __('Super Admin') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
