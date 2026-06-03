@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid">
        <article class="page-hero section-card">
            <div>
                <small>Dokumen Hukum Desa</small>
                <h1>Peraturan Desa</h1>
                <p>Kumpulan peraturan resmi dari {{ $village?->name ?? 'pemerintah desa' }} dalam format PDF atau gambar.</p>
            </div>
            <div class="page-hero__actions">
                <form method="GET" action="{{ route('regulations.index') }}" class="page-hero-filter page-hero-filter--search">
                    <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari judul, isi, atau nama file peraturan...">
                    <button type="submit">Cari</button>
                    <a href="{{ route('regulations.index') }}">Reset</a>
                </form>
            </div>
        </article>
    </div>
</section>

<section class="section-wrap section-wrap--last">
    <div class="container-grid">
        @if ($regulations->isEmpty())
            <article class="section-card announcement-page-empty">
                <h3>Belum ada peraturan desa</h3>
                <p>Peraturan desa yang dipublikasikan admin akan tampil di halaman ini.</p>
            </article>
        @else
            <div class="announcement-grid announcement-grid--page">
                @foreach ($regulations as $item)
                    @php
                        $attachmentName = $item->attachment_name ?: ($item->attachment_path ? basename((string) $item->attachment_path) : null);
                        $ext = strtolower((string) pathinfo((string) $attachmentName, PATHINFO_EXTENSION));
                        $isPdf = $ext === 'pdf';
                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true);
                        $detailPayload = [
                            'id' => $item->id,
                            'title' => $item->title,
                            'content' => trim(strip_tags((string) $item->content)) !== '' ? trim(strip_tags((string) $item->content)) : 'Deskripsi belum tersedia.',
                            'publishedAt' => $item->published_at?->translatedFormat('d M Y H:i') ?? $item->created_at?->translatedFormat('d M Y H:i'),
                            'attachmentUrl' => $item->attachment_url,
                            'attachmentName' => $attachmentName,
                            'isPdf' => $isPdf,
                            'isImage' => $isImage,
                        ];
                    @endphp
                    <article
                        class="section-card announcement-card announcement-card--page announcement-card--interactive"
                        role="button"
                        tabindex="0"
                        data-regulation-detail='@json($detailPayload)'>
                        <div class="announcement-card__body">
                            <span class="announcement-card__type" style="background:#334155;">
                                <i class="fa-solid fa-scale-balanced" aria-hidden="true"></i> Peraturan Desa
                            </span>
                            <h3>{{ $item->title }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags((string) $item->content), 220) }}</p>
                            <div class="announcement-card__meta">
                                <small>
                                    Dipublikasikan:
                                    {{ $item->published_at?->translatedFormat('d M Y H:i') ?? $item->created_at?->translatedFormat('d M Y H:i') }}
                                </small>
                                @if ($attachmentName)
                                    <small>File: {{ $attachmentName }}</small>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div style="margin-top:1rem;">
                {{ $regulations->links() }}
            </div>

            <div id="regulation-detail-modal" class="announcement-modal" hidden>
                <div class="announcement-modal__backdrop" data-regulation-close></div>
                <div class="announcement-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="regulation-modal-title">
                    <button type="button" class="announcement-modal__close" data-regulation-close aria-label="Tutup detail">&times;</button>
                    <span class="announcement-modal__type" style="background:#334155;">Peraturan Desa</span>
                    <h3 id="regulation-modal-title"></h3>
                    <small id="regulation-modal-date"></small>
                    <div class="announcement-modal__content" id="regulation-modal-content"></div>
                    <div id="regulation-modal-preview-wrap" class="announcement-modal__map-wrap" hidden>
                        <h4>Preview Dokumen</h4>
                        <div id="regulation-modal-preview"></div>
                    </div>
                    <div class="announcement-modal__actions" id="regulation-modal-actions" hidden>
                        <a id="regulation-modal-download" href="#" hidden>Download</a>
                        <a id="regulation-modal-open" target="_blank" rel="noopener" hidden>Buka File di Tab Baru</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<script>
    (function () {
        const modal = document.getElementById('regulation-detail-modal');
        if (!modal) return;

        const titleEl = document.getElementById('regulation-modal-title');
        const dateEl = document.getElementById('regulation-modal-date');
        const contentEl = document.getElementById('regulation-modal-content');
        const previewWrapEl = document.getElementById('regulation-modal-preview-wrap');
        const previewEl = document.getElementById('regulation-modal-preview');
        const actionsEl = document.getElementById('regulation-modal-actions');
        const downloadEl = document.getElementById('regulation-modal-download');
        const openEl = document.getElementById('regulation-modal-open');
        const cards = document.querySelectorAll('[data-regulation-detail]');
        const baseDownloadUrl = @json(route('regulations.download', ['announcement' => '__ID__']));

        const hasText = (value) => typeof value === 'string' && value.trim() !== '';

        const closeModal = () => {
            modal.setAttribute('hidden', 'hidden');
            document.body.classList.remove('is-modal-open');
            previewEl.innerHTML = '';
        };

        const openModal = (payload) => {
            titleEl.textContent = payload.title || '-';
            dateEl.textContent = payload.publishedAt ? `Dipublikasikan: ${payload.publishedAt}` : '';
            contentEl.textContent = payload.content || '-';

            previewEl.innerHTML = '';
            previewWrapEl.setAttribute('hidden', 'hidden');
            actionsEl.setAttribute('hidden', 'hidden');
            downloadEl.setAttribute('hidden', 'hidden');
            openEl.setAttribute('hidden', 'hidden');
            downloadEl.removeAttribute('href');
            openEl.removeAttribute('href');

            if (payload.isImage && hasText(payload.attachmentUrl)) {
                const img = document.createElement('img');
                img.src = payload.attachmentUrl;
                img.alt = payload.attachmentName || payload.title || 'Lampiran peraturan';
                img.loading = 'lazy';
                img.style.width = '100%';
                img.style.borderRadius = '12px';
                previewEl.appendChild(img);
                previewWrapEl.removeAttribute('hidden');
            } else if (payload.isPdf && hasText(payload.attachmentUrl)) {
                const iframe = document.createElement('iframe');
                iframe.src = `${payload.attachmentUrl}#toolbar=1`;
                iframe.title = payload.attachmentName || 'Preview PDF Peraturan';
                iframe.style.width = '100%';
                iframe.style.height = '420px';
                iframe.style.border = '1px solid rgba(148, 163, 184, 0.4)';
                iframe.style.borderRadius = '12px';
                previewEl.appendChild(iframe);
                previewWrapEl.removeAttribute('hidden');
            }

            if (payload.id) {
                downloadEl.href = baseDownloadUrl.replace('__ID__', String(payload.id));
                downloadEl.removeAttribute('hidden');
            } else if (hasText(payload.attachmentUrl)) {
                downloadEl.href = payload.attachmentUrl;
                downloadEl.setAttribute('download', '');
                downloadEl.removeAttribute('hidden');
            }

            if (hasText(payload.attachmentUrl)) {
                openEl.href = payload.attachmentUrl;
                openEl.removeAttribute('hidden');
            }

            if (!downloadEl.hasAttribute('hidden') || !openEl.hasAttribute('hidden')) {
                actionsEl.removeAttribute('hidden');
            }

            modal.removeAttribute('hidden');
            document.body.classList.add('is-modal-open');
        };

        cards.forEach((card) => {
            const trigger = () => {
                const raw = card.getAttribute('data-regulation-detail');
                if (!raw) return;
                try {
                    openModal(JSON.parse(raw));
                } catch (_) {}
            };

            card.addEventListener('click', trigger);
            card.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    trigger();
                }
            });
        });

        modal.querySelectorAll('[data-regulation-close]').forEach((el) => {
            el.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.hasAttribute('hidden')) {
                closeModal();
            }
        });
    })();
</script>
@endsection
