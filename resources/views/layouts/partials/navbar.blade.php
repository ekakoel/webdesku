@php
    $moduleManager = \App\Support\ModuleManager::class;
    $servicesModuleEnabled = $moduleManager::isEnabled('services');
    $complaintsModuleEnabled = $moduleManager::isEnabled('complaints');
    $profileModuleEnabled = $moduleManager::isEnabled('profile');
    $newsModuleEnabled = $moduleManager::isEnabled('news');
    $agendaModuleEnabled = $moduleManager::isEnabled('agendas');
    $announcementsModuleEnabled = $moduleManager::isEnabled('announcements');
    $galleriesModuleEnabled = $moduleManager::isEnabled('galleries');
    $infographicsModuleEnabled = $moduleManager::isEnabled('infographics');
    $transparencyModuleEnabled = $moduleManager::isEnabled('transparency');
    $regulationsModuleEnabled = $moduleManager::isEnabled('regulations');
    $navVillage = \App\Support\VillageIdentity::village();
    $navLogo = $navVillage?->logo_url ?? asset('icons/icon_desa.png');
@endphp

<nav class="topbar">
    <div class="topbar__inner">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand__logo"><img src="{{ $navLogo }}" alt="logo desa"></span>
            <span>
                <strong>{{ \App\Support\VillageIdentity::name($navVillage) }}</strong>
                <small>Portal Informasi Desa</small>
            </span>
        </a>
        <button type="button" class="topbar__toggle" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="topbar-nav">
            <span aria-hidden="true">&#9776;</span>
        </button>
        <div id="topbar-nav" class="topbar__nav">
            <ul class="menu">
                <li><a href="{{ route('home') }}">Beranda</a></li>
                @if ($profileModuleEnabled)
                    <li class="menu__item menu__item--dropdown">
                        <a href="{{ route('profil.gambaran') }}" class="profile-dropdown-trigger">Profil Desa</a>
                        <div class="menu__dropdown">
                            <a href="{{ route('profil.gambaran') }}">Gambaran Umum Desa</a>
                            <a href="{{ route('profil.sejarah') }}">Sejarah Desa</a>
                            <a href="{{ route('profil.visimisi') }}">Visi dan Misi</a>
                            <a href="{{ route('profil.organisasi') }}">Susunan Organisasi</a>
                        </div>
                    </li>
                @endif
                @if ($infographicsModuleEnabled || $transparencyModuleEnabled)
                    <li class="menu__item menu__item--dropdown">
                        <a href="#" class="profile-dropdown-trigger">Data Desa</a>
                        <div class="menu__dropdown">
                            @if ($infographicsModuleEnabled)
                                <a href="{{ route('infografis') }}">Infografis</a>
                            @endif
                            @if ($transparencyModuleEnabled)
                                <a href="{{ route('transparansi') }}">Transparansi</a>
                            @endif
                        </div>
                    </li>
                @endif
                @if ($newsModuleEnabled || $agendaModuleEnabled || $announcementsModuleEnabled || $galleriesModuleEnabled)
                    <li class="menu__item menu__item--dropdown">
                        <a href="#" class="profile-dropdown-trigger">Informasi</a>
                        <div class="menu__dropdown">
                            @if ($newsModuleEnabled)
                                <a href="{{ route('berita') }}">Berita</a>
                            @endif
                            @if ($agendaModuleEnabled)
                                <a href="{{ route('agenda') }}">Agenda</a>
                            @endif
                            @if ($announcementsModuleEnabled)
                                <a href="{{ route('pengumuman') }}">Pengumuman</a>
                            @endif
                            @if ($galleriesModuleEnabled)
                                <a href="{{ route('galeri') }}">Galeri</a>
                            @endif
                        </div>
                    </li>
                @endif
                @include('admin.partials.desil_navigation')
                

                @if ($regulationsModuleEnabled)
                    <li><a href="{{ route('regulations.index') }}">Peraturan Desa</a></li>
                @endif
                @if ($complaintsModuleEnabled)
                    <li><a href="{{ route('complaints.index') }}">Pengaduan</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<script>
    (function () {
        const nav = document.querySelector('.topbar');
        if (!nav) return;
        const navPanel = nav.querySelector('.topbar__nav');
        const navToggle = nav.querySelector('.topbar__toggle');
        if (!navPanel || !navToggle) return;

        navToggle.addEventListener('click', function () {
            const isOpen = navPanel.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            nav.classList.toggle('is-mobile-open', isOpen);
        });

        const dropdownItems = Array.from(nav.querySelectorAll('.menu__item--dropdown'));
        if (dropdownItems.length === 0) return;

        dropdownItems.forEach((dropdownItem) => {
            const trigger = dropdownItem.querySelector('.profile-dropdown-trigger');
            const menu = dropdownItem.querySelector('.menu__dropdown');
            if (!trigger || !menu) return;

            trigger.addEventListener('click', function (event) {
                if (window.matchMedia('(max-width: 1024px)').matches) {
                    event.preventDefault();
                }

                const willOpen = !dropdownItem.classList.contains('is-open');
                dropdownItems.forEach((item) => item.classList.remove('is-open'));
                if (willOpen) {
                    dropdownItem.classList.add('is-open');
                }
            });
        });

        document.addEventListener('click', function (event) {
            if (!nav.contains(event.target)) {
                navPanel.classList.remove('is-open');
                nav.classList.remove('is-mobile-open');
                navToggle.setAttribute('aria-expanded', 'false');
            }
            dropdownItems.forEach((item) => {
                if (!item.contains(event.target)) {
                    item.classList.remove('is-open');
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                navPanel.classList.remove('is-open');
                nav.classList.remove('is-mobile-open');
                navToggle.setAttribute('aria-expanded', 'false');
                dropdownItems.forEach((item) => item.classList.remove('is-open'));
            }
        });
    })();
</script>
