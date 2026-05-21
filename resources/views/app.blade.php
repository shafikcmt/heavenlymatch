<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    {{-- Inertia page title / description are set per-page via usePage() --}}
    <title inertia>{{ config('app.name', 'HeavenlyMatch') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Favicon --}}
    <link rel="icon" href="/favicon.ico" sizes="any" />
    <link rel="icon" href="/images/icon.svg" type="image/svg+xml" />
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png" />

    {{-- Scripts & Styles (Vite) --}}
    @routes
    @viteReactRefresh
    @vite(['resources/js/app.tsx'])
    @inertiaHead
</head>
<body class="h-full font-sans antialiased bg-slate-50 text-slate-900">
    @inertia
</body>
</html>
