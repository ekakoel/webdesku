@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid" style="max-width: 900px;">
            <a href="{{ route('berita') }}" class="text-link">Kembali ke Arsip Berita</a>
        <article class="section-card" style="margin-top: .85rem; overflow: hidden;">
            @if ($news->thumbnail_url)
                <img src="{{ $news->thumbnail_url }}" alt="{{ $news->title }}" style="width: 100%; max-height: 420px; object-fit: cover;" loading="lazy" decoding="async">
            @endif
            <div style="padding: 1.2rem;">
                <h1 style="margin: 0; font-size: clamp(1.3rem, 2.8vw, 2rem);">{{ $news->title }}</h1>
                <p style="margin: .45rem 0 0; color: #4b5563; font-size: .92rem;">
                    Diposting oleh {{ $news->author?->name ?? 'Admin Desa' }} |
                    {{ $news->published_at?->translatedFormat('d F Y, H:i') ?? $news->created_at?->translatedFormat('d F Y, H:i') }} |
                    {{ number_format((int) ($news->view_count ?? 0), 0, ',', '.') }} kali dilihat
                </p>
                <div style="margin-top: 1rem; color: #1f2937; line-height: 1.8;">
                    {!! nl2br(e($news->content)) !!}
                </div>

                @if ($news->images->isNotEmpty())
                    <div style="margin-top: 1.5rem;">
                        <h2 style="margin: 0 0 .7rem; font-size: 1.2rem; color: #0b1728;">Galeri Berita</h2>
                        <div style="display: grid; gap: .75rem; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
                            @foreach ($news->images as $image)
                                <a href="{{ $image->image_url }}" target="_blank" rel="noopener" style="display: block;">
                                    <img src="{{ $image->image_url }}" alt="{{ $image->caption ?: $news->title }}" style="width: 100%; object-fit: cover; border-radius: 12px;" loading="lazy" decoding="async">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </article>

        @if ($relatedNews->isNotEmpty())
            <div class="section-head" style="margin-top: 1.1rem;">
                <h2>Baca Juga</h2>
            </div>
            <div class="news-modern-grid">
                @foreach ($relatedNews as $item)
                    <article class="section-card news-modern-card">
                        <a href="{{ route('berita.show', $item->slug) }}" class="news-modern-card__image-link" aria-label="{{ $item->title }}">
                            @if ($item->thumbnail_url)
                                <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" class="news-modern-card__image" loading="lazy" decoding="async">
                            @else
                                <div class="news-modern-card__image news-modern-card__image--fallback"></div>
                            @endif
                        </a>
                        <div class="news-modern-card__body">
                            <h3><a href="{{ route('berita.show', $item->slug) }}">{{ $item->title }}</a></h3>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->content), 110) }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection



