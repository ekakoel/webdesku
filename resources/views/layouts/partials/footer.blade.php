@php
    $footVillage = \App\Support\VillageIdentity::village();
    $footGovernmentName = \App\Support\VillageIdentity::governmentName($footVillage);
@endphp

<footer class="site-footer">
    <div class="site-footer__inner">
        <div>
            <h3>{{ \App\Support\VillageIdentity::name($footVillage) }}</h3>
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
        <small>&copy; {{ date('Y') }} {{ $footGovernmentName }}. Semua hak dilindungi.</small>
    </div>
</footer>


