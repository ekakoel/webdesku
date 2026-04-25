@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid">
        <h1 style="font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 800; margin: 0;">Berita Desa</h1>
        @if ($village)
            <p style="margin-top: .45rem; color: #4b5563;">Publikasi terbaru dari {{ $village->name }}.</p>
        @endif
    </div>
</section>

<section class="section-wrap section-wrap--last">
    <div class="container-grid">
        @if ($news->isEmpty())
            <p style="color: #6b7280;">Belum ada berita.</p>
        @else
            <div class="news-modern-grid">
                @foreach ($news as $item)
                    <article class="section-card news-modern-card">
                        <a href="{{ route('berita.show', $item->slug) }}" class="news-modern-card__image-link" aria-label="{{ $item->title }}">
                            @if ($item->thumbnail_url)
                                <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" class="news-modern-card__image" loading="lazy" decoding="async">
                            @else
                                <div class="news-modern-card__image news-modern-card__image--fallback"></div>
                            @endif
                        </a>
                        <div class="news-modern-card__body">
                            <h3>
                                <a href="{{ route('berita.show', $item->slug) }}">{{ $item->title }}</a>
                            </h3>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->content), 145) }}</p>
                        </div>
                        <div class="news-modern-card__meta">
                            <div class="news-modern-card__meta-line">
                                <span>Diposting: {{ $item->author?->name ?? 'Admin Desa' }}</span>
                                <span>{{ number_format((int) ($item->view_count ?? 0), 0, ',', '.') }} kali dilihat</span>
                            </div>
                            <span class="news-modern-card__date">
                                {{ $item->published_at?->translatedFormat('d M Y') ?? $item->created_at?->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    </article>
                @endforeach
            </div>

            <div style="margin-top: 1rem;">
                {{ $news->links() }}
            </div>
        @endif
    </div>
</section>
@endsection



