@extends('web.web')

@section('content')
<section class="section-wrap">
    <div class="container-grid">
        <article class="page-hero section-card">
            <div>
                <small>Layanan Desa</small>
                <h1>Layanan Desa</h1>
                <p>Ajukan layanan administrasi desa secara online. Pilih layanan sesuai kebutuhan Anda.</p>
            </div>
            <div class="page-hero__actions">
                <a href="{{ route('services.status') }}" class="text-link">Cek Status Pengajuan</a>
            </div>
        </article>
    </div>
</section>

<section class="section-wrap section-wrap--last">
    <div class="container-grid">
        @if ($services->isEmpty())
            <p style="color: #6b7280;">Belum ada layanan yang dipublikasikan.</p>
        @else
            <div class="service-catalog-grid">
                @foreach ($services as $service)
                    <article class="section-card service-catalog-card">
                        <div class="service-catalog-card__head">
                            <span>{{ $service->icon ?: 'SV' }}</span>
                            <h3>{{ $service->name }}</h3>
                        </div>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($service->description), 135) }}</p>
                        <div class="service-catalog-card__meta">
                            <small>{{ count($service->requirementsList()) }} persyaratan</small>
                            <small>{{ count($service->processList()) }} langkah</small>
                            <small>SLA {{ (int) ($service->sla_target_hours ?? 72) }} jam</small>
                        </div>
                        <a href="{{ route('services.show', $service->slug) }}" class="service-catalog-card__cta">Ajukan Layanan</a>
                    </article>
                @endforeach
            </div>

            <div style="margin-top: 1rem;">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
