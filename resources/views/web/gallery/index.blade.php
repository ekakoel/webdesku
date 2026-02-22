@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid">
        <div class="section-head section-head--stacked">
            <h1 style="margin: 0; font-size: clamp(1.5rem, 3vw, 2rem);">Galeri Desa</h1>
            <p style="margin-top: .45rem; color: #4b5563;">
                Dokumentasi kegiatan {{ $village?->name ?? 'desa' }} dalam format visual.
            </p>
        </div>

        @if ($galleries->isEmpty())
            <article class="section-card" style="padding: 1rem;">
                <p style="margin: 0; color: #6b7280;">Belum ada dokumentasi galeri yang dipublikasikan.</p>
            </article>
        @else
            <div class="gallery-page-grid">
                @foreach ($galleries as $gallery)
                    <article class="section-card gallery-page-card">
                        <button
                            type="button"
                            class="gallery-page-card__trigger"
                            data-gallery-open
                            data-image="{{ $gallery->image_url }}"
                            data-title="{{ $gallery->title }}"
                            data-category="{{ $gallery->category ?? '-' }}"
                            data-caption="{{ trim((string) ($gallery->caption ?? '')) }}"
                            data-published="{{ $gallery->published_at?->translatedFormat('d M Y') ?? $gallery->created_at?->translatedFormat('d M Y') }}"
                        >
                            @if ($gallery->thumbnail_url)
                                <img src="{{ $gallery->thumbnail_url }}" alt="{{ $gallery->title }}" loading="lazy" decoding="async">
                            @else
                                <div class="gallery-page-card__fallback"></div>
                            @endif
                        </button>
                        <div class="gallery-page-card__body">
                            <h3>{{ $gallery->title }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags((string) $gallery->caption), 90) }}</p>
                        </div>
                    </article>
                @endforeach
            </div>

            <div style="margin-top: 1rem;">
                {{ $galleries->links() }}
            </div>
        @endif
    </div>
</section>

<div class="gallery-modal" id="gallery-modal" hidden>
    <div class="gallery-modal__backdrop" data-gallery-close></div>
    <div class="gallery-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="gallery-modal-title">
        <button type="button" class="gallery-modal__close" data-gallery-close aria-label="Tutup">×</button>
        <div class="gallery-modal__media">
            <img id="gallery-modal-image" src="" alt="" loading="lazy" decoding="async">
        </div>
        <div class="gallery-modal__content">
            <h3 id="gallery-modal-title"></h3>
            <p id="gallery-modal-caption"></p>
            <div class="gallery-modal__meta">
                <span id="gallery-modal-category"></span>
                <span id="gallery-modal-date"></span>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const modal = document.getElementById('gallery-modal');
        if (!modal) return;

        const modalImage = document.getElementById('gallery-modal-image');
        const modalTitle = document.getElementById('gallery-modal-title');
        const modalCaption = document.getElementById('gallery-modal-caption');
        const modalCategory = document.getElementById('gallery-modal-category');
        const modalDate = document.getElementById('gallery-modal-date');

        const openButtons = Array.from(document.querySelectorAll('[data-gallery-open]'));
        const closeButtons = Array.from(document.querySelectorAll('[data-gallery-close]'));

        const openModal = (button) => {
            modalImage.src = button.dataset.image || '';
            modalImage.alt = button.dataset.title || 'Galeri desa';
            modalTitle.textContent = button.dataset.title || '-';
            modalCaption.textContent = button.dataset.caption || 'Tidak ada deskripsi tambahan.';
            modalCategory.textContent = `Kategori: ${button.dataset.category || '-'}`;
            modalDate.textContent = `Dipublikasikan: ${button.dataset.published || '-'}`;

            modal.hidden = false;
            document.body.style.overflow = 'hidden';
        };

        const closeModal = () => {
            modal.hidden = true;
            document.body.style.overflow = '';
            modalImage.src = '';
        };

        openButtons.forEach((button) => {
            button.addEventListener('click', () => openModal(button));
        });

        closeButtons.forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.hidden) {
                closeModal();
            }
        });
    })();
</script>
@endsection

