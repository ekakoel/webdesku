<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteVillage = app()->bound('currentVillage') ? app('currentVillage') : null;
        $siteIcon = $siteVillage?->logo_url ?? asset('icons/icon_desa.png');
    @endphp
    <link rel="icon" type="image/png" href="{{ $siteIcon }}">
    <title>{{ $title ?? 'Webdesku' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Merriweather:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/web.css', 'resources/js/app.js'])

</head>
<body class="site-body">

@include('layouts.partials.navbar')

<main class="site-main">
    @yield('content')
</main>

@include('layouts.partials.footer')

</body>
</html>


