@extends('web.web')

@section('content')
@php
    $vm = $visiMisiData ?? [];
    $pageMeta = $vm['_page'] ?? [];
@endphp

<section class="section-wrap">
    <div class="container-grid">
        <article class="section-card budget-card">
            <h1 style="margin:0; font-size: clamp(1.35rem, 2.5vw, 1.9rem);">{{ $pageMeta['title'] ?? 'Visi dan Misi Desa' }}</h1>
            @if (!empty($pageMeta['subtitle']))
                <p style="margin-top:.45rem; color:#64748b;">{{ $pageMeta['subtitle'] }}</p>
            @endif
            {{-- @if (!empty($vm['sumber']))
                <p style="margin-top:.45rem; color:#64748b; font-size:.86rem;">
                    Sumber referensi: <a href="{{ $vm['sumber'] }}" target="_blank" rel="noopener" class="text-link">{{ $vm['sumber'] }}</a>
                </p>
            @endif --}}
        </article>
    </div>
</section>

<section class="section-wrap">
    <div class="container-grid">
        <article class="section-card greeting-card">
            <h2>Visi Desa</h2>
            <p style="margin-top:.7rem; font-weight:600; color:#0f2f57;">
                {{ $vm['visi'] ?? ($village?->vision ?? 'Visi desa belum tersedia.') }}
            </p>
        </article>
    </div>
</section>

<section class="section-wrap section-wrap--last">
    <div class="container-grid">
        <article class="section-card budget-card">
            <h2>Misi Desa</h2>
            @php
                $misi = $vm['misi_pokok'] ?? $missions;
            @endphp
            @if (count($misi) > 0)
                <ol style="margin:.8rem 0 0 1rem; color:#475569; display:grid; gap:.5rem;">
                    @foreach ($misi as $row)
                        <li>{{ $row }}</li>
                    @endforeach
                </ol>
            @else
                <p style="margin-top:.8rem; color:#475569;">Misi desa belum tersedia.</p>
            @endif
        </article>
    </div>
</section>
@endsection
