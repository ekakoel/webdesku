@extends('web.web')

@section('content')
@php
    $org = $organisasiData ?? [];
    $pageMeta = $org['_page'] ?? [];
    $kelompok = $org['kelompok'] ?? collect();
@endphp

<section class="section-wrap">
    <div class="container-grid">
        <article class="section-card budget-card">
            <h1 style="margin:0; font-size: clamp(1.35rem, 2.5vw, 1.9rem);">{{ $pageMeta['title'] ?? ($org['judul'] ?? 'Susunan Organisasi Desa') }}</h1>
            @if (!empty($pageMeta['subtitle']))
                <p style="margin-top:.45rem; color:#64748b;">{{ $pageMeta['subtitle'] }}</p>
            @elseif (!empty($org['pengantar']))
                <p style="margin-top:.45rem; color:#64748b;">{{ $org['pengantar'] }}</p>
            @endif
            {{-- @if (!empty($org['sumber']))
                <p style="margin-top:.45rem; color:#64748b; font-size:.86rem;">
                    Sumber referensi: <a href="{{ $org['sumber'] }}" target="_blank" rel="noopener" class="text-link">{{ $org['sumber'] }}</a>
                </p>
            @endif --}}
        </article>
    </div>
</section>

<section class="section-wrap section-wrap--last">
    <div class="container-grid">
        @if ($officials->isEmpty())
            <article class="section-card greeting-card">
                <p style="margin:0; color:#475569;">Data susunan organisasi belum tersedia.</p>
            </article>
        @else
            @foreach ($kelompok as $unit => $rows)
                <article class="section-card budget-card" style="margin-bottom:.9rem;">
                    <h2>{{ $unit }}</h2>
                    <div class="official-grid" style="margin-top:.75rem;">
                        @foreach ($rows as $official)
                            <article class="section-card official-card">
                                <div class="official-card__image" @if($official->photo_path) style="background-image: url('{{ \Illuminate\Support\Facades\Storage::url($official->photo_path) }}');" @endif>
                                    @if (!$official->photo_path)
                                        <span>{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($official->name, 0, 2)) }}</span>
                                    @endif
                                </div>
                                <div class="official-card__meta">
                                    <h3>{{ $official->name }}</h3>
                                    <p>{{ $official->position }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </article>
            @endforeach
        @endif
    </div>
</section>
@endsection
