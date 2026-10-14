<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Portal KLA Konawe') }}</title>

        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

        <!-- Meta Description -->
        <meta name="description" content="Portal resmi Kabupaten Layak Anak (KLA) Kabupaten Konawe — melindungi hak anak dan mewujudkan lingkungan ramah anak.">

        <!-- Fonts & Assets -->
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.tsx'])

        <!-- Inertia Head -->
        @inertiaHead
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
