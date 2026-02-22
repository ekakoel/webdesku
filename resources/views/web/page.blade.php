@extends('web.web')

@section('content')
<section style="padding: 2rem 1rem;">
    <h1 style="font-size: 1.75rem; font-weight: 700;">{{ $title }}</h1>
    <p style="margin-top: .75rem; color: #4b5563;">{{ $description }}</p>
</section>

@if (isset($items))
    <section style="padding: 0 1rem 2rem;">
        @if (method_exists($items, 'isEmpty') && $items->isEmpty())
            <p style="color: #6b7280;">Belum ada data untuk halaman ini.</p>
        @else
            <div style="display: grid; gap: 1rem;">
                @foreach ($items as $item)
                    <article style="border: 1px solid #dbeafe; border-radius: .7rem; padding: 1rem; background: #fff;">
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
    </section>
@endif
@endsection


