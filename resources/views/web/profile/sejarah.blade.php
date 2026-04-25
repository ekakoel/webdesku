@extends('web.web')

@section('content')
@php
    $sejarah = $sejarahData ?? [];
    $pageMeta = $sejarah['_page'] ?? [];
@endphp

<section class="section-wrap">
    <div class="container-grid">
        <article class="section-card greeting-card">
            <h1 style="margin:0; font-size: clamp(1.35rem, 2.5vw, 1.9rem);">{{ $pageMeta['title'] ?? 'Sejarah Desa' }}</h1>
            @if (!empty($pageMeta['subtitle']))
                <p style="margin-top:.45rem; color:#64748b;">{{ $pageMeta['subtitle'] }}</p>
            @endif
            <p style="margin-top:.7rem;">{{ $village->history ?? 'Data sejarah desa belum tersedia.' }}</p>
            {{-- @if (!empty($sejarah['sumber']))
                <p style="margin-top:.5rem; color:#64748b; font-size:.86rem;">
                    Sumber referensi: <a href="{{ $sejarah['sumber'] }}" target="_blank" rel="noopener" class="text-link">{{ $sejarah['sumber'] }}</a>
                </p>
            @endif --}}
        </article>
    </div>
</section>

@if (!empty($sejarah))
<section class="section-wrap">
    <div class="container-grid split">
        <article class="section-card budget-card">
            <h2>Pemekaran Wilayah Tahun 1982</h2>
            <ol style="margin:.75rem 0 0 1.1rem; color:#475569; display:grid; gap:.35rem;">
                @foreach (($sejarah['pemekaran'] ?? []) as $row)
                    <li>{{ $row }}</li>
                @endforeach
            </ol>
        </article>
        <article class="section-card budget-card">
            <h2>Cakupan Wilayah Awal</h2>
            <p style="margin-top:.75rem; color:#475569;">{{ $sejarah['cakupan_awal'] ?? '-' }}</p>
            <h3 style="margin-top:1rem;">Batas Wilayah</h3>
            <div class="potensi-list">
                <div><h3>Utara</h3><p>{{ $sejarah['batas']['utara'] ?? '-' }}</p></div>
                <div><h3>Timur</h3><p>{{ $sejarah['batas']['timur'] ?? '-' }}</p></div>
                <div><h3>Selatan</h3><p>{{ $sejarah['batas']['selatan'] ?? '-' }}</p></div>
                <div><h3>Barat</h3><p>{{ $sejarah['batas']['barat'] ?? '-' }}</p></div>
            </div>
        </article>
    </div>
</section>

<section class="section-wrap">
    <div class="container-grid split">
        <article class="section-card budget-card">
            <h2>Periode Kepemimpinan Perbekel</h2>
            <div class="potensi-list" style="margin-top:.75rem;">
                @foreach (($sejarah['perbekel'] ?? []) as $row)
                    <div>
                        <h3>{{ $row['nama'] }}</h3>
                        <p>{{ $row['periode'] }}</p>
                    </div>
                @endforeach
            </div>
        </article>
        <article class="section-card budget-card">
            <h2>Banjar Dinas Awal</h2>
            <ol style="margin:.75rem 0 0 1.1rem; color:#475569; display:grid; gap:.35rem;">
                @foreach (($sejarah['banjar_dinas'] ?? []) as $row)
                    <li>{{ $row }}</li>
                @endforeach
            </ol>
        </article>
    </div>
</section>
@endif

{{-- <section class="section-wrap section-wrap--last">
    <div class="container-grid">
        <article class="section-card greeting-card">
            <h2>Ringkasan Sejarah pada Database Desa</h2>
            <p>{{ $village?->history ?? '-' }}</p>
        </article>
    </div>
</section> --}}
@endsection
