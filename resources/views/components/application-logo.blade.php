@php
    $logoVillage = app()->bound('currentVillage')
        ? app('currentVillage')
        : (\Illuminate\Support\Facades\Schema::hasTable('villages') ? \App\Models\Village::query()->first() : null);
    $logoUrl = $logoVillage?->logo_url ?? asset('icons/icon_desa.png');
@endphp
<img src="{{ $logoUrl }}" alt="Logo Desa" {{ $attributes }}>


