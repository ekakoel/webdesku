@php
    $navVillage = app()->bound('currentVillage') ? app('currentVillage') : null;
@endphp

<nav class="topbar">
    <div class="topbar__inner">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand__logo"><img src="{{ asset('icons/icon_desa.png') }}" alt="icon desa dangin puri kauh"></span>
            <span>
                <strong>{{ $navVillage?->name ?? 'Desa Dangin Puri' }}</strong>
                <small>Portal Informasi Desa</small>
            </span>
        </a>
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
</nav>

<script>
    (function () {
        const dropdownItem = document.querySelector('.menu__item--dropdown');
        if (!dropdownItem) return;

        const trigger = dropdownItem.querySelector('.profile-dropdown-trigger');
        const menu = dropdownItem.querySelector('.menu__dropdown');
        if (!trigger || !menu) return;

        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            dropdownItem.classList.toggle('is-open');
        });

        document.addEventListener('click', function (event) {
            if (!dropdownItem.contains(event.target)) {
                dropdownItem.classList.remove('is-open');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                dropdownItem.classList.remove('is-open');
            }
        });
    })();
</script>
