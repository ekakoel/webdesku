@php
    use Illuminate\Support\Str;

    $routeName = (string) optional(request()->route())->getName();

    if (!Str::startsWith($routeName, 'admin.')) {
        return;
    }

    $routeCore = Str::after($routeName, 'admin.');
    $segments = explode('.', $routeCore);
    $resourceKey = $segments[0] ?? 'dashboard';
    $action = $segments[1] ?? 'index';
    $extra = $segments[2] ?? null;

    $resourceLabels = [
        'dashboard' => 'Dashboard Admin',
        'news' => 'Berita',
        'agendas' => 'Agenda',
        'announcements' => 'Pengumuman',
        'services' => 'Layanan Desa',
        'service-requests' => 'Pengajuan Layanan',
        'galleries' => 'Galeri',
        'village-assets' => 'Aset Desa',
        'village-populations' => 'Penduduk',
        'village-population-stats' => 'Statistik Penduduk',
        'village-transparency-items' => 'Transparansi Desa',
        'village-apbdes-items' => 'APBDes',
        'village-infographic-items' => 'Infografis Lainnya',
        'profile-pages' => 'Halaman Profil Desa',
        'sliders' => 'Slider Beranda',
        'head-messages' => 'Sambutan Kepala Desa',
        'officials' => 'Aparatur Desa',
        'village-settings' => 'Pengaturan Desa',
        'village-map' => 'Map Desa',
    ];

    $groupLabels = [
        'news' => 'Konten Publik',
        'agendas' => 'Konten Publik',
        'announcements' => 'Konten Publik',
        'galleries' => 'Konten Publik',
        'sliders' => 'Konten Publik',
        'services' => 'Layanan Desa',
        'service-requests' => 'Layanan Desa',
        'village-assets' => 'Infografis & Transparansi',
        'village-populations' => 'Infografis & Transparansi',
        'village-population-stats' => 'Infografis & Transparansi',
        'village-transparency-items' => 'Infografis & Transparansi',
        'village-apbdes-items' => 'Infografis & Transparansi',
        'village-infographic-items' => 'Infografis & Transparansi',
        'village-settings' => 'Profil & Organisasi Desa',
        'head-messages' => 'Profil & Organisasi Desa',
        'officials' => 'Profil & Organisasi Desa',
        'profile-pages' => 'Profil & Organisasi Desa',
        'village-map' => 'Profil & Organisasi Desa',
    ];

    $resourceRoutes = [
        'dashboard' => 'admin.dashboard',
        'news' => 'admin.news.index',
        'agendas' => 'admin.agendas.index',
        'announcements' => 'admin.announcements.index',
        'services' => 'admin.services.index',
        'service-requests' => 'admin.service-requests.index',
        'galleries' => 'admin.galleries.index',
        'village-assets' => 'admin.village-assets.index',
        'village-populations' => 'admin.village-populations.index',
        'village-population-stats' => 'admin.village-population-stats.index',
        'village-transparency-items' => 'admin.village-transparency-items.index',
        'village-apbdes-items' => 'admin.village-apbdes-items.index',
        'village-infographic-items' => 'admin.village-infographic-items.index',
        'profile-pages' => 'admin.profile-pages.index',
        'sliders' => 'admin.sliders.index',
        'head-messages' => 'admin.head-messages.index',
        'officials' => 'admin.officials.index',
        'village-settings' => 'admin.village-settings.edit',
        'village-map' => 'admin.village-map.edit',
    ];

    $actionLabels = [
        'index' => 'Daftar',
        'create' => 'Tambah',
        'edit' => 'Ubah',
        'show' => 'Detail',
        'export' => 'Export',
        'import-big' => 'Import BIG',
        'resolve-map-link' => 'Resolve Link Map',
    ];

    $resourceLabel = $resourceLabels[$resourceKey] ?? Str::headline(str_replace('-', ' ', $resourceKey));
    $groupLabel = $groupLabels[$resourceKey] ?? null;
    $resourceRoute = $resourceRoutes[$resourceKey] ?? null;
    $resourceUrl = $resourceRoute && Route::has($resourceRoute) ? route($resourceRoute) : null;

    $currentLabel = null;
    if ($resourceKey === 'dashboard') {
        $currentLabel = null;
    } elseif ($action !== 'index') {
        $actionLabel = $actionLabels[$action] ?? Str::headline(str_replace('-', ' ', $action));
        if ($action === 'export' && $extra) {
            $currentLabel = $actionLabel.' '.Str::upper($extra);
        } else {
            $currentLabel = $actionLabel;
        }
    }

    $isResourceCurrent = $resourceKey !== 'dashboard' && $currentLabel === null;
@endphp

<nav class="admin-breadcrumb" aria-label="Breadcrumb">
    <ol class="admin-breadcrumb__list">
        <li>
            <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
        </li>

        @if ($groupLabel && $resourceKey !== 'dashboard')
            <li class="admin-breadcrumb__sep" aria-hidden="true">/</li>
            <li class="admin-breadcrumb__group">{{ $groupLabel }}</li>
        @endif

        @if ($resourceKey !== 'dashboard')
            <li class="admin-breadcrumb__sep" aria-hidden="true">/</li>
            <li @class(['admin-breadcrumb__current' => $isResourceCurrent])>
                @if ($isResourceCurrent || !$resourceUrl)
                    <span>{{ $resourceLabel }}</span>
                @else
                    <a href="{{ $resourceUrl }}">{{ $resourceLabel }}</a>
                @endif
            </li>
        @endif

        @if ($currentLabel)
            <li class="admin-breadcrumb__sep" aria-hidden="true">/</li>
            <li class="admin-breadcrumb__current">
                <span>{{ $currentLabel }}</span>
            </li>
        @endif
    </ol>
</nav>
