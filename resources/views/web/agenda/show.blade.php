@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid" style="max-width: 900px;">
        <a href="{{ route('agenda') }}" class="text-link">Kembali ke Agenda</a>

        @php
            $now = now();
            $agendaStatus = 'Akan Datang';
            if ($agenda->start_at && $agenda->start_at <= $now && (!$agenda->end_at || $agenda->end_at >= $now)) {
                $agendaStatus = 'Berlangsung';
            } elseif (($agenda->end_at && $agenda->end_at < $now) || ($agenda->start_at && !$agenda->end_at && $agenda->start_at < $now)) {
                $agendaStatus = 'Selesai';
            }
        @endphp

        <article class="section-card agenda-detail-card">
            <div class="agenda-detail-card__top">
                <span class="agenda-card__badge">{{ $agendaStatus }}</span>
                <h1>{{ $agenda->title }}</h1>
            </div>
            @if ($agenda->poster_url)
                <div class="agenda-detail-card__poster">
                    <img src="{{ $agenda->poster_url }}" alt="{{ $agenda->title }}" loading="lazy" decoding="async">
                </div>
            @endif
            <div class="agenda-detail-card__meta">
                <div>
                    <small>Mulai</small>
                    <p>{{ $agenda->start_at?->translatedFormat('d F Y H:i') ?? '-' }}</p>
                </div>
                <div>
                    <small>Selesai</small>
                    <p>{{ $agenda->end_at?->translatedFormat('d F Y H:i') ?? '-' }}</p>
                </div>
                <div>
                    <small>Lokasi</small>
                    <p>{{ $agenda->location ?: 'Lokasi akan diinformasikan' }}</p>
                </div>
            </div>
            <div class="agenda-detail-card__content">
                {!! nl2br(e($agenda->description ?: 'Deskripsi agenda belum tersedia.')) !!}
            </div>
        </article>

        @if ($relatedAgendas->isNotEmpty())
            <div class="section-head" style="margin-top: 1rem;">
                <h2>Agenda Lainnya</h2>
            </div>
            <div class="agenda-list">
                @foreach ($relatedAgendas as $item)
                    <article class="section-card agenda-card">
                        <div class="agenda-card__header">
                            <h2><a href="{{ route('agenda.show', $item) }}">{{ $item->title }}</a></h2>
                        </div>
                        <div class="agenda-card__meta">
                            <span>{{ $item->start_at?->translatedFormat('d M Y H:i') ?? '-' }}</span>
                            <span>{{ $item->location ?: 'Lokasi akan diinformasikan' }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
