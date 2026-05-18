<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?: 'en') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php($hmSiteName = \App\Models\SystemSetting::get('general.site_name', 'HeavenlyMatch'))
    @php($hmSeoTitle = \App\Models\SystemSetting::get('seo.meta_title', $hmSiteName))
    @php($hmSeoDescription = \App\Models\SystemSetting::get('seo.meta_description', 'Find trusted matrimonial matches with HeavenlyMatch.'))
    @php($hmSeoKeywords = \App\Models\SystemSetting::get('seo.meta_keywords'))
    @php($hmCanonical = \App\Models\SystemSetting::get('seo.canonical_url', url()->current()))
    @php($hmFavicon = \App\Models\SystemSetting::get('media.favicon'))
    @php($hmOgImage = \App\Models\SystemSetting::get('seo.og_image'))
    <link rel="canonical" href="{{ $hmCanonical }}" />
    @php($hmPageTitle = trim($__env->yieldContent('title')))
    <title>{{ $hmPageTitle ? $hmPageTitle . ' | ' . $hmSiteName : $hmSeoTitle }}</title>
    <meta name="description" content="{{ $hmSeoDescription }}">
    @if($hmSeoKeywords)<meta name="keywords" content="{{ $hmSeoKeywords }}">@endif
    @if($hmOgImage)<meta property="og:image" content="{{ asset($hmOgImage) }}">@endif
    @if($hmFavicon)<link rel="icon" href="{{ asset($hmFavicon) }}">@endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            prefix: 'tw-',
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'ui-sans-serif', 'system-ui']
                    },
                    colors: {
                        hm: {
                            50: '#fff5fb',
                            100: '#ffe8f5',
                            200: '#ffcce8',
                            300: '#f79ed0',
                            500: '#e12f83',
                            600: '#c31872',
                            700: '#8b146f',
                            900: '#341044'
                        },
                        border: '#e5e7eb',
                        input: '#e5e7eb',
                        ring: '#c31872',
                        background: '#ffffff',
                        foreground: '#0f172a',
                        muted: '#f8fafc',
                        'muted-foreground': '#64748b',
                        card: '#ffffff',
                        'card-foreground': '#0f172a'
                    },
                    borderRadius: {
                        xl: '0.875rem',
                        '2xl': '1rem',
                        '3xl': '1.5rem'
                    },
                    boxShadow: {
                        shad: '0 1px 2px rgba(15, 23, 42, 0.06), 0 1px 3px rgba(15, 23, 42, 0.10)',
                        soft: '0 24px 80px rgba(52, 16, 68, 0.14)',
                        glow: '0 18px 45px rgba(195, 24, 114, 0.24)'
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
    @php($hmCustomCss = \App\Models\SystemSetting::get('custom.css'))
    @if($hmCustomCss)<style>{!! $hmCustomCss !!}</style>@endif
</head>
<body class="tw-bg-slate-50 tw-font-sans tw-text-slate-900">

    @include('components.navbar')

    <main class="tw-min-h-screen">
        @if(session('success'))
            <div id="successAlert" class="tw-fixed tw-right-4 tw-top-4 tw-z-[9999] tw-flex tw-max-w-sm tw-items-start tw-gap-3 tw-rounded-2xl tw-border tw-border-emerald-200 tw-bg-white tw-p-4 tw-shadow-soft tw-opacity-0 -tw-translate-y-3 tw-transition-all tw-duration-300">
                <span class="tw-grid tw-h-10 tw-w-10 tw-shrink-0 tw-place-items-center tw-rounded-full tw-bg-emerald-50 tw-text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                </span>
                <div class="tw-text-sm tw-font-semibold tw-leading-6 tw-text-emerald-800">{{ session('success') }}</div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const alert = document.getElementById('successAlert');
                    if (!alert) return;
                    alert.classList.remove('tw-opacity-0', '-tw-translate-y-3');
                    alert.classList.add('tw-opacity-100', 'tw-translate-y-0');
                    setTimeout(function () {
                        alert.classList.add('tw-opacity-0', '-tw-translate-y-3');
                    }, 4000);
                });
            </script>
        @endif

        @yield('content')
    </main>

    @include('components.footer')

    @php($hmGdprEnabled = \App\Models\SystemSetting::bool('gdpr.enabled'))
    @if($hmGdprEnabled)
        <div id="hmCookieNotice" class="tw-fixed tw-bottom-4 tw-left-4 tw-right-4 tw-z-[9999] tw-hidden tw-rounded-3xl tw-bg-slate-950 tw-p-4 tw-text-white tw-shadow-2xl md:tw-left-auto md:tw-w-[430px]">
            <div class="tw-text-sm tw-font-semibold tw-leading-6">{{ \App\Models\SystemSetting::get('gdpr.message', 'We use cookies to improve your experience.') }}</div>
            <div class="tw-mt-3 tw-flex tw-items-center tw-justify-between tw-gap-3">
                @php($hmCookieUrl = \App\Models\SystemSetting::get('gdpr.policy_url'))
                @if($hmCookieUrl)<a class="tw-text-xs tw-font-bold tw-text-white/75 tw-no-underline hover:tw-text-white" href="{{ $hmCookieUrl }}">Learn more</a>@else<span></span>@endif
                <button type="button" onclick="localStorage.setItem('hm_cookie_ok','1');document.getElementById('hmCookieNotice').remove();" class="tw-rounded-2xl tw-bg-hm-600 tw-px-4 tw-py-2 tw-text-sm tw-font-black tw-text-white tw-border-0">{{ \App\Models\SystemSetting::get('gdpr.button_text', 'Accept') }}</button>
            </div>
        </div>
        <script>document.addEventListener('DOMContentLoaded',function(){if(!localStorage.getItem('hm_cookie_ok')){document.getElementById('hmCookieNotice')?.classList.remove('tw-hidden');}});</script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
