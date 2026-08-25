<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @php
            $layoutVillage = \App\Support\VillageIdentity::village();
            $layoutIcon = $layoutVillage?->logo_url ?? asset('icons/icon_desa.png');
            $layoutTitle = \App\Support\VillageIdentity::title($title ?? \App\Support\VillageIdentity::defaultPageTitle(), $layoutVillage);
        @endphp
        <link rel="icon" type="image/png" href="{{ $layoutIcon }}">

        <title>{{ $layoutTitle }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased {{ request()->routeIs('admin.*') ? 'admin-area' : '' }}">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                        @if (request()->routeIs('admin.*'))
                            @include('layouts.partials.admin-breadcrumbs')
                        @endif
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>


