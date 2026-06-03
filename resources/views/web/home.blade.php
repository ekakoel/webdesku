@extends('web.web')

@section('content')
<section class="home-slider" data-slider-root>
    <div class="home-slider__track">
        @if ($sliders->isNotEmpty())
            @foreach ($sliders as $index => $slide)
                <article class="home-slide {{ $index === 0 ? 'is-active' : '' }}" data-slide-item>
                    <div class="home-slide__bg" style="background-image: linear-gradient(120deg, rgba(6, 23, 49, 0.66), rgba(10, 46, 91, 0.45)), url('{{ \Illuminate\Support\Facades\Storage::url($slide->image_path) }}');"></div>
                    <div class="home-slide__content">
                        <span class="home-slide__badge">Website Resmi Desa</span>
                        <h1>{{ $slide->title ?: ($village?->name ?? 'Website Desa') }}</h1>
                        <p>{{ $slide->caption ?: ($village?->description ?? 'Portal digital desa untuk layanan publik dan keterbukaan informasi.') }}</p>
                        <div class="hero__actions">
                            @if ($slide->cta_url && $slide->cta_text)
                                <a href="{{ $slide->cta_url }}" class="btn btn--light">{{ $slide->cta_text }}</a>
                            @endif
                            @if ($moduleStates['services'] ?? false)
                                <a href="{{ route('services') }}" class="btn btn--ghost">Jelajahi Layanan</a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        @else
            <article class="home-slide is-active" data-slide-item>
                <div class="home-slide__bg home-slide__bg--fallback"></div>
                <div class="home-slide__content">
                    <span class="home-slide__badge">Website Resmi Desa</span>
                    <h1>{{ $village?->name ?? 'Website Desa' }}</h1>
                    <p>{{ $village?->description ?? 'Selamat datang di portal digital desa untuk layanan publik, berita, dan transparansi informasi.' }}</p>
                    <div class="hero__actions">
                        @if ($moduleStates['services'] ?? false)
                            <a href="{{ route('services') }}" class="btn btn--light">Jelajahi Layanan</a>
                        @endif
                        @if ($moduleStates['news'] ?? false)
                            <a href="{{ route('berita') }}" class="btn btn--ghost">Lihat Berita</a>
                        @endif
                    </div>
                </div>
            </article>
        @endif
    </div>

    <div class="home-slider__dots" data-slider-dots>
        @for ($i = 0; $i < max($sliders->count(), 1); $i++)
            <button type="button" class="home-slider__dot {{ $i === 0 ? 'is-active' : '' }}" data-slider-dot aria-label="Slide {{ $i + 1 }}"></button>
        @endfor
    </div>
</section>

@if ($moduleStates['profile'] ?? false)
    <div class="head-message-modal" data-head-message-modal hidden aria-hidden="true">
        <div class="head-message-modal__backdrop" data-head-message-close></div>
        <div class="head-message-modal__dialog section-card" role="dialog" aria-modal="true" aria-labelledby="headMessageModalTitle">
            <button type="button" class="head-message-modal__close" data-head-message-close aria-label="Tutup modal sambutan">&times;</button>
            <article class="head-message-card head-message-card--modal">
                <div class="head-message-card__content">
                    <span class="head-message-card__badge">Sambutan Kepala Desa</span>
                    <h2 id="headMessageModalTitle">{{ $headMessage?->position ?? 'Kepala Desa' }}</h2>
                    <p>{{ $headMessage?->message ?? ($village?->head_greeting ?? 'Kami berkomitmen menghadirkan pelayanan publik yang cepat, transparan, dan mudah diakses warga melalui portal digital desa.') }}</p>
                    <div class="head-message-card__person">
                        <strong>{{ $headMessage?->name ?? ($village?->head_name ?? 'Kepala Desa') }}</strong>
                        @if ($headMessage?->signature)
                            <small>{{ $headMessage->signature }}</small>
                        @endif
                    </div>
                </div>
                <div class="head-message-card__photo">
                    @if ($headMessage?->photo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($headMessage->photo_path) }}" alt="{{ $headMessage?->name ?? 'Kepala Desa' }}">
                    @else
                        <div class="head-message-card__photo-fallback">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($headMessage?->name ?? ($village?->head_name ?? 'KD'), 0, 2)) }}
                        </div>
                    @endif
                </div>
            </article>
        </div>
    </div>
@endif
@if ($moduleStates['announcements'] ?? false)
    <section class="section-wrap">
        <div class="container-grid">
            <div class="section-head">
                <h2>Pengumuman Terbaru</h2>
                <a href="{{ route('pengumuman') }}">Lihat Semua</a>
            </div>
            <div class="announcement-grid">
                @forelse ($announcements as $item)
                    <article class="section-card announcement-card">
                        <span class="announcement-card__type" style="background: {{ $item->typeColor() }}">
                            <i class="{{ $item->typeIcon() }}" aria-hidden="true"></i> {{ $item->typeLabel() }}
                        </span>
                        <h3>{{ $item->title }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->content), 135) }}</p>
                        <small>{{ $item->published_at?->translatedFormat('d M Y') ?? $item->created_at?->translatedFormat('d M Y') }}</small>
                    </article>
                @empty
                    <article class="section-card announcement-card">
                        <h3>Belum ada pengumuman</h3>
                        <p>Pengumuman dari admin desa akan tampil otomatis di sini.</p>
                    </article>
                @endforelse
            </div>
        </div>
    </section>
@endif

@if ($moduleStates['news'] ?? false)
    <section class="section-wrap">
        <div class="container-grid">
            <div class="section-head">
                <h2>Berita Terbaru</h2>
                <a href="{{ route('berita') }}">Arsip Berita</a>
            </div>
            <div class="news-modern-grid">
            @forelse ($news as $item)
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
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->content), 155) }}</p>
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
            @empty
                <article class="section-card news-modern-card news-modern-card--empty">
                    <span>Belum ada publikasi</span>
                    <h3>Berita desa akan ditampilkan di sini</h3>
                    <p>Tambahkan berita dari panel admin untuk mengisi bagian ini secara otomatis.</p>
                </article>
            @endforelse
            </div>
        </div>
    </section>
@endif

@if (($instagramPosts ?? collect())->isNotEmpty())
    <section class="section-wrap">
        <div class="container-grid">
            <div class="section-head">
                <h2>Instagram Desa</h2>
                @if (!empty($village?->instagram_username))
                    <a href="https://instagram.com/{{ ltrim($village->instagram_username, '@') }}" target="_blank" rel="noopener noreferrer">
                        @{{ ltrim($village->instagram_username, '@') }}
                    </a>
                @endif
            </div>
            <div class="gallery-grid">
                @foreach ($instagramPosts as $post)
                    @php
                        $preview = $post->thumbnail_url ?: $post->media_url;
                    @endphp
                    <a href="{{ $post->permalink ?: '#' }}" class="section-card gallery-item" target="_blank" rel="noopener noreferrer" @if($preview) style="background-image: linear-gradient(165deg, rgba(9, 49, 104, 0.24), rgba(11, 77, 163, 0.36)), url('{{ $preview }}'); background-size: cover; background-position: center;" @endif>
                        <span>{{ \Illuminate\Support\Str::limit($post->caption ?: 'Lihat postingan', 60) }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

@if ($moduleStates['agendas'] ?? false)
    <section class="section-wrap section-wrap--last">
        <div class="container-grid">
            <div class="section-head">
                <h2>Agenda Desa</h2>
                <a href="{{ route('agenda') }}">Lihat Agenda</a>
            </div>
            <div class="agenda-list agenda-list--grid">
            @forelse ($agendas as $agenda)
                @php
                    $now = now();
                    $agendaStatus = 'Akan Datang';
                    if ($agenda->start_at && $agenda->start_at <= $now && (!$agenda->end_at || $agenda->end_at >= $now)) {
                        $agendaStatus = 'Berlangsung';
                    } elseif (($agenda->end_at && $agenda->end_at < $now) || ($agenda->start_at && !$agenda->end_at && $agenda->start_at < $now)) {
                        $agendaStatus = 'Selesai';
                    }
                @endphp
                <article class="section-card agenda-card">
                    @if ($agenda->poster_url)
                        <div class="agenda-card__poster">
                            <img src="{{ $agenda->poster_url }}" alt="{{ $agenda->title }}" loading="lazy" decoding="async">
                        </div>
                    @endif
                    <div class="agenda-card__header">
                        <span class="agenda-card__badge">{{ $agendaStatus }}</span>
                        <h2><a href="{{ route('agenda.show', $agenda) }}">{{ $agenda->title }}</a></h2>
                    </div>
                    <div class="agenda-card__meta">
                        <span>
                            {{ $agenda->start_at?->translatedFormat('d M Y H:i') ?? '-' }}
                            @if ($agenda->end_at)
                                - {{ $agenda->end_at->translatedFormat('d M Y H:i') }}
                            @endif
                        </span>
                        <span>{{ $agenda->location ?: 'Lokasi akan diinformasikan' }}</span>
                    </div>
                </article>
            @empty
                <article class="section-card agenda-card">
                    <span>Belum ada agenda</span>
                    <h2>Agenda desa akan ditampilkan di sini</h2>
                    <div class="agenda-card__meta">
                        <span>Tambahkan agenda melalui backend untuk menampilkan jadwal kegiatan desa.</span>
                    </div>
                </article>
            @endforelse
            </div>
        </div>
    </section>
@endif

@if ($moduleStates['galleries'] ?? false)
    <section class="section-wrap section-wrap--last">
        <div class="container-grid">
            <div class="section-head">
                <h2>Galeri Desa</h2>
                <a href="{{ route('galeri') }}">Lihat Galeri</a>
            </div>
            <div class="gallery-grid">
            @forelse ($galleries as $gallery)
                <div class="section-card gallery-item" @if($gallery->image_url) style="background-image: linear-gradient(165deg, rgba(9, 49, 104, 0.28), rgba(11, 77, 163, 0.42)), url('{{ $gallery->image_url }}'); background-size: cover; background-position: center;" @endif>
                    <span>{{ $gallery->title }}</span>
                </div>
            @empty
                <div class="section-card gallery-item"><span>Belum ada galeri</span></div>
            @endforelse
            </div>
        </div>
    </section>
@endif

<script>
    (function () {
        const root = document.querySelector('[data-slider-root]');
        if (!root) return;

        const slides = Array.from(root.querySelectorAll('[data-slide-item]'));
        const dots = Array.from(root.querySelectorAll('[data-slider-dot]'));
        if (slides.length <= 1) return;

        let activeIndex = 0;
        let timer;

        const render = (index) => {
            activeIndex = (index + slides.length) % slides.length;
            slides.forEach((slide, i) => slide.classList.toggle('is-active', i === activeIndex));
            dots.forEach((dot, i) => dot.classList.toggle('is-active', i === activeIndex));
        };

        const start = () => {
            clearInterval(timer);
            timer = setInterval(() => render(activeIndex + 1), 6000);
        };

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                render(index);
                start();
            });
        });

        render(0);
        start();
    })();
</script>
<script>
    (function () {
        const modal = document.querySelector('[data-head-message-modal]');
        if (!modal) return;

        const storageKey = 'headMessageModalClosedInTab';
        const alreadyClosed = window.sessionStorage.getItem(storageKey) === '1';
        if (alreadyClosed) return;

        const closeButtons = Array.from(modal.querySelectorAll('[data-head-message-close]'));

        const openModal = () => {
            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('is-modal-open');
        };

        const closeModal = () => {
            modal.hidden = true;
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('is-modal-open');
            window.sessionStorage.setItem(storageKey, '1');
        };

        closeButtons.forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.hidden) {
                closeModal();
            }
        });

        openModal();
    })();
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const canvas = document.getElementById('genderPopulationChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const chartData = @json($populationChart);
        const male = Number(chartData.male || 0);
        const female = Number(chartData.female || 0);

        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [male, female],
                    backgroundColor: ['#1b63bf', '#60a5fa'],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 14,
                            color: '#2f415a',
                            font: { size: 12, weight: '600' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const value = Number(ctx.parsed || 0);
                                return `${ctx.label}: ${new Intl.NumberFormat('id-ID').format(value)} jiwa`;
                            }
                        }
                    }
                },
                cutout: '62%',
            }
        });
    })();
</script>
@endsection
