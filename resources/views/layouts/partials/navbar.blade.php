@php
    $navVillage = app()->bound('currentVillage') ? app('currentVillage') : null;
    $navLogo = $navVillage?->logo_url ?? asset('icons/icon_desa.png');
@endphp

<nav class="topbar">
    <div class="topbar__inner">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand__logo"><img src="{{ $navLogo }}" alt="logo desa"></span>
            <span>
                <strong>{{ $navVillage?->name ?? 'Desa Dangin Puri' }}</strong>
                <small>Portal Informasi Desa</small>
            </span>
        </a>
        <button type="button" class="topbar__toggle" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="topbar-nav">
            <span aria-hidden="true">&#9776;</span>
        </button>
        <div id="topbar-nav" class="topbar__nav">
            <ul class="menu">
                <li><a href="{{ route('home') }}">Beranda</a></li>
                <li class="menu__item menu__item--dropdown">
                    <a href="{{ route('profil.gambaran') }}" class="profile-dropdown-trigger">Profil Desa</a>
                    <div class="menu__dropdown">
                        <a href="{{ route('profil.gambaran') }}">Gambaran Umum Desa</a>
                        <a href="{{ route('profil.sejarah') }}">Sejarah Desa</a>
                        <a href="{{ route('profil.visimisi') }}">Visi dan Misi</a>
                        <a href="{{ route('profil.organisasi') }}">Susunan Organisasi</a>
                    </div>
                </li>
                <li><a href="{{ route('berita') }}">Berita</a></li>
                <li><a href="{{ route('agenda') }}">Agenda</a></li>
                <li><a href="{{ route('infografis') }}">Infografis</a></li>
                <li><a href="{{ route('services') }}">Layanan</a></li>
                {{-- <li><a href="{{ route('services.status') }}">Cek Status</a></li> --}}
                <li><a href="{{ route('transparansi') }}">Transparansi</a></li>
                {{-- <li><a href="{{ route('galeri') }}">Galeri</a></li> --}}
                {{-- <li><a href="{{ route('pengumuman') }}">Pengumuman</a></li> --}}
                {{-- <li><a href="{{ route('kontak') }}">Kontak</a></li> --}}
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

        const dropdownItem = nav.querySelector('.menu__item--dropdown');
        if (!dropdownItem) return;

        const trigger = dropdownItem.querySelector('.profile-dropdown-trigger');
        const menu = dropdownItem.querySelector('.menu__dropdown');
        if (!trigger || !menu) return;

        trigger.addEventListener('click', function (event) {
            if (window.matchMedia('(max-width: 1024px)').matches) {
                event.preventDefault();
            }
            dropdownItem.classList.toggle('is-open');
        });

        document.addEventListener('click', function (event) {
            if (!nav.contains(event.target)) {
                navPanel.classList.remove('is-open');
                nav.classList.remove('is-mobile-open');
                navToggle.setAttribute('aria-expanded', 'false');
            }
            if (!dropdownItem.contains(event.target)) {
                dropdownItem.classList.remove('is-open');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                navPanel.classList.remove('is-open');
                nav.classList.remove('is-mobile-open');
                navToggle.setAttribute('aria-expanded', 'false');
                dropdownItem.classList.remove('is-open');
            }
        });
    })();
</script>
