<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?: 'en') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HeavenlyMatch')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            prefix: 'tw-',
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    colors: {
                        hm: {
                            50: '#fff1f6', 100: '#ffe1ed', 200: '#ffc0d7', 300: '#ff92bb', 400: '#fb5e98',
                            500: '#e21d63', 600: '#c91455', 700: '#a20f45', 800: '#86113f', 900: '#500824',
                            green: '#08745c', greenDark: '#045441'
                        }
                    },
                    boxShadow: {
                        soft: '0 18px 48px rgba(16,24,40,.13)',
                        card: '0 8px 24px rgba(16,24,40,.07)',
                        glow: '0 18px 45px rgba(226,29,99,.18)'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/hm-app-ui.css') }}">
    @stack('styles')
</head>
<body class="hm-app-body tw-min-h-screen">
    <div class="hm-desktop-only">
        @include('components.user-nav')
    </div>

    @yield('mobile_header')

    <div class="hm-mobile-safe-bottom tw-min-h-screen">
        <div class="tw-mx-auto tw-w-full tw-max-w-[1240px] md:tw-grid md:tw-grid-cols-[250px_minmax(0,1fr)] md:tw-gap-5 md:tw-px-5 md:tw-py-5 lg:tw-grid-cols-[280px_minmax(0,1fr)]">
            <aside class="hm-desktop-only">
                @include('components.user-sidebar')
            </aside>

            <main class="tw-min-w-0">
                @if(session('success'))
                    <div class="tw-m-4 tw-rounded-2xl tw-border tw-border-emerald-200 tw-bg-emerald-50 tw-p-3 tw-text-sm tw-font-bold tw-text-emerald-800 md:tw-mb-5 md:tw-mt-0">
                        <i class="bi bi-check2-circle"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('warning'))
                    <div class="tw-m-4 tw-rounded-2xl tw-border tw-border-amber-200 tw-bg-amber-50 tw-p-3 tw-text-sm tw-font-bold tw-text-amber-800 md:tw-mb-5 md:tw-mt-0">
                        <i class="bi bi-exclamation-triangle"></i> {{ session('warning') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="tw-m-4 tw-rounded-2xl tw-border tw-border-rose-200 tw-bg-rose-50 tw-p-3 tw-text-sm tw-font-bold tw-text-rose-800 md:tw-mb-5 md:tw-mt-0">
                        <i class="bi bi-x-circle"></i> {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <nav class="hm-bottom-nav hm-mobile-only tw-fixed tw-bottom-0 tw-left-0 tw-right-0 tw-z-50 tw-border-t tw-border-slate-200 tw-bg-white">
        <div class="tw-grid tw-grid-cols-5 tw-text-[11px] tw-font-semibold tw-text-slate-500">
            <a class="tw-flex tw-flex-col tw-items-center tw-gap-1 tw-py-2 tw-no-underline {{ request()->routeIs('myhome') ? 'tw-text-hm-green' : 'tw-text-slate-500' }}" href="{{ route('myhome') }}"><i class="bi bi-house-fill tw-text-[25px]"></i>Home</a>
            <a class="tw-flex tw-flex-col tw-items-center tw-gap-1 tw-py-2 tw-no-underline {{ request()->routeIs('search') || request()->routeIs('matches') ? 'tw-text-hm-green' : 'tw-text-slate-500' }}" href="{{ route('search') }}"><i class="bi bi-search tw-text-[25px]"></i>Search</a>
            <a class="tw-flex tw-flex-col tw-items-center tw-gap-1 tw-py-2 tw-no-underline {{ request()->routeIs('inbox') || request()->routeIs('sent') ? 'tw-text-hm-green' : 'tw-text-slate-500' }}" href="{{ route('inbox') }}"><i class="bi bi-envelope-fill tw-text-[25px]"></i>Mailbox</a>
            <a class="tw-flex tw-flex-col tw-items-center tw-gap-1 tw-py-2 tw-no-underline {{ request()->routeIs('upgrade') ? 'tw-text-hm-green' : 'tw-text-slate-500' }}" href="{{ route('upgrade') }}"><i class="bi bi-gem tw-text-[25px]"></i>Upgrade</a>
            <a class="tw-flex tw-flex-col tw-items-center tw-gap-1 tw-py-2 tw-no-underline {{ request()->routeIs('profiledetail') ? 'tw-text-hm-green' : 'tw-text-slate-500' }}" href="{{ route('profiledetail') }}"><i class="bi bi-person-fill tw-text-[25px]"></i>Profile</a>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('click', function (event) {
            const trigger = event.target.closest('[data-hm-toggle]');
            if (!trigger) return;
            const target = document.querySelector(trigger.dataset.hmToggle);
            if (target) target.classList.toggle('tw-hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>
