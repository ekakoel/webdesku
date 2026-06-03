@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid page-section-stack">
        <article class="page-hero section-card">
            <div>
                <small>Informasi Desa</small>
                <h1>{{ $title }}</h1>
                <p>{{ $description }}</p>
            </div>
        </article>
    </div>
</section>

@if (isset($items))
    <section class="section-wrap section-wrap--last">
        <div class="container-grid">
            @if (method_exists($items, 'isEmpty') && $items->isEmpty())
                <p style="color: #6b7280;">Belum ada data untuk halaman ini.</p>
            @else
                <div style="display: grid; gap: 1rem;">
                    @foreach ($items as $item)
                        <article class="section-card" style="padding: 1rem;">
                            <h3 style="font-size: 1.1rem; font-weight: 700;">
                                {{ $item->title ?? $item->name ?? '-' }}
                            </h3>
                            <p style="margin-top: .5rem; color: #4b5563;">
                                {{ \Illuminate\Support\Str::limit(strip_tags($item->description ?? $item->content ?? ''), 220) }}
                            </p>
                        </article>
                    @endforeach
                </div>

                @if (method_exists($items, 'links'))
                    <div style="margin-top: 1rem;">
                        {{ $items->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
@endif
@endsection


