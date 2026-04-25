@php
    $footVillage = app()->bound('currentVillage') ? app('currentVillage') : null;
@endphp

<footer class="site-footer">
    <div class="site-footer__inner">
        <div>
            <h3>{{ $footVillage?->name ?? 'Desa Dangin Puri' }}</h3>
            <p>{{ $footVillage?->description ?? 'Portal resmi informasi, layanan publik, dan transparansi desa.' }}</p>
        </div>
        <div>
            <h4>Kontak Desa</h4>
            <p>Email: desadanginpuri@example.id</p>
            <p>Telp: (0361) 123456</p>
        </div>
        <div>
            <h4>Jam Pelayanan</h4>
            <p>Senin - Jumat: 08:00 - 15:00 WITA</p>
            <p>Layanan Online: 24 Jam</p>
        </div>
    </div>
    <div class="site-footer__bottom">
        <small>&copy; {{ date('Y') }} {{ $footVillage?->name ?? config('app.name', 'Webdesku') }}. Semua hak dilindungi.</small>
    </div>
</footer>


