@php($hmSiteName = \App\Models\SystemSetting::get('general.site_name', 'HeavenlyMatch'))
@php($hmLogo = \App\Models\SystemSetting::get('media.logo'))
<header class="tw-sticky tw-top-0 tw-z-50 tw-border-b tw-border-slate-200/80 tw-bg-white/90 tw-backdrop-blur-xl">
    <div class="tw-mx-auto tw-flex tw-h-16 tw-max-w-7xl tw-items-center tw-justify-between tw-px-4 sm:tw-px-6 lg:tw-px-8">
        <a class="tw-flex tw-items-center tw-gap-3 tw-text-slate-950 tw-no-underline" href="{{ route('welcome') }}" aria-label="{{ $hmSiteName }} home">
            @if($hmLogo)
                <img src="{{ asset($hmLogo) }}" alt="{{ $hmSiteName }}" class="tw-h-11 tw-w-11 tw-rounded-2xl tw-object-contain tw-shadow-shad">
            @else
                <span class="tw-grid tw-h-11 tw-w-11 tw-place-items-center tw-rounded-2xl tw-bg-gradient-to-br tw-from-hm-700 tw-to-hm-500 tw-text-white tw-shadow-glow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35 10.55 20.03C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35Z"/></svg>
                </span>
            @endif
            <span class="tw-text-xl tw-font-black tw-tracking-tight">{{ $hmSiteName }}</span>
        </a>

        <nav class="tw-hidden tw-items-center tw-gap-1 md:tw-flex" aria-label="Main navigation">
            <a class="tw-rounded-xl tw-px-3 tw-py-2 tw-text-sm tw-font-bold tw-no-underline tw-transition {{ request()->routeIs('welcome') ? 'tw-bg-hm-50 tw-text-hm-700' : 'tw-text-slate-600 hover:tw-bg-slate-100 hover:tw-text-slate-950' }}" href="{{ route('welcome') }}">Home</a>
            <a class="tw-rounded-xl tw-px-3 tw-py-2 tw-text-sm tw-font-bold tw-no-underline tw-transition {{ request()->routeIs('about') ? 'tw-bg-hm-50 tw-text-hm-700' : 'tw-text-slate-600 hover:tw-bg-slate-100 hover:tw-text-slate-950' }}" href="{{ route('about') }}">About</a>
            <a class="tw-rounded-xl tw-px-3 tw-py-2 tw-text-sm tw-font-bold tw-no-underline tw-transition {{ request()->routeIs('faq') ? 'tw-bg-hm-50 tw-text-hm-700' : 'tw-text-slate-600 hover:tw-bg-slate-100 hover:tw-text-slate-950' }}" href="{{ route('faq') }}">FAQ</a>
            <a class="tw-rounded-xl tw-px-3 tw-py-2 tw-text-sm tw-font-bold tw-no-underline tw-transition {{ request()->routeIs('guide') ? 'tw-bg-hm-50 tw-text-hm-700' : 'tw-text-slate-600 hover:tw-bg-slate-100 hover:tw-text-slate-950' }}" href="{{ route('guide') }}">Guide</a>
            <a class="tw-rounded-xl tw-px-3 tw-py-2 tw-text-sm tw-font-bold tw-no-underline tw-transition {{ request()->routeIs('contact') ? 'tw-bg-hm-50 tw-text-hm-700' : 'tw-text-slate-600 hover:tw-bg-slate-100 hover:tw-text-slate-950' }}" href="{{ route('contact') }}">Contact</a>
        </nav>

        <div class="tw-hidden tw-items-center tw-gap-2 md:tw-flex">
            @guest
                <a class="tw-rounded-xl tw-px-4 tw-py-2 tw-text-sm tw-font-black tw-text-hm-700 tw-no-underline hover:tw-bg-hm-50" href="{{ route('login') }}">Login</a>
                <a class="tw-rounded-xl tw-bg-hm-600 tw-px-4 tw-py-2 tw-text-sm tw-font-black tw-text-white tw-no-underline tw-shadow-glow hover:tw-bg-hm-700" href="{{ route('register.show') }}">Register</a>
            @else
                <details class="tw-relative">
                    <summary class="tw-flex tw-cursor-pointer tw-list-none tw-items-center tw-gap-2 tw-rounded-xl tw-border tw-border-slate-200 tw-bg-white tw-px-3 tw-py-2 tw-text-sm tw-font-bold tw-text-slate-700 tw-shadow-shad">
                        {{ Auth::user()->name }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-4 tw-w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </summary>
                    <div class="tw-absolute tw-right-0 tw-mt-2 tw-w-48 tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-p-2 tw-shadow-soft">
                        <a class="tw-block tw-rounded-xl tw-px-3 tw-py-2 tw-text-sm tw-font-semibold tw-text-slate-700 tw-no-underline hover:tw-bg-slate-100" href="{{ route('myhome') }}">Dashboard</a>
                        <a class="tw-block tw-rounded-xl tw-px-3 tw-py-2 tw-text-sm tw-font-semibold tw-text-slate-700 tw-no-underline hover:tw-bg-slate-100" href="{{ route('biodata.create') }}">Edit Biodata</a>
                        <form action="{{ route('logout') }}" method="POST" class="tw-mt-1 tw-border-t tw-border-slate-100 tw-pt-1">@csrf<button type="submit" class="tw-w-full tw-rounded-xl tw-border-0 tw-bg-white tw-px-3 tw-py-2 tw-text-left tw-text-sm tw-font-semibold tw-text-rose-600 hover:tw-bg-rose-50">Logout</button></form>
                    </div>
                </details>
            @endguest
        </div>

        <button type="button" class="tw-inline-flex tw-h-10 tw-w-10 tw-items-center tw-justify-center tw-rounded-xl tw-border tw-border-slate-200 tw-bg-white tw-text-slate-700 tw-shadow-shad md:tw-hidden" data-hm-mobile-toggle aria-label="Open menu">
            <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    <div data-hm-mobile-menu class="tw-hidden tw-border-t tw-border-slate-200 tw-bg-white md:tw-hidden">
        <div class="tw-mx-auto tw-max-w-7xl tw-space-y-1 tw-px-4 tw-py-4 sm:tw-px-6">
            <a class="tw-block tw-rounded-xl tw-px-3 tw-py-2 tw-text-sm tw-font-bold tw-text-slate-700 tw-no-underline hover:tw-bg-slate-100" href="{{ route('welcome') }}">Home</a>
            <a class="tw-block tw-rounded-xl tw-px-3 tw-py-2 tw-text-sm tw-font-bold tw-text-slate-700 tw-no-underline hover:tw-bg-slate-100" href="{{ route('about') }}">About</a>
            <a class="tw-block tw-rounded-xl tw-px-3 tw-py-2 tw-text-sm tw-font-bold tw-text-slate-700 tw-no-underline hover:tw-bg-slate-100" href="{{ route('faq') }}">FAQ</a>
            <a class="tw-block tw-rounded-xl tw-px-3 tw-py-2 tw-text-sm tw-font-bold tw-text-slate-700 tw-no-underline hover:tw-bg-slate-100" href="{{ route('guide') }}">Guide</a>
            <a class="tw-block tw-rounded-xl tw-px-3 tw-py-2 tw-text-sm tw-font-bold tw-text-slate-700 tw-no-underline hover:tw-bg-slate-100" href="{{ route('contact') }}">Contact</a>
            <div class="tw-pt-2">
                @guest
                    <a class="tw-mr-2 tw-inline-flex tw-rounded-xl tw-px-4 tw-py-2 tw-text-sm tw-font-black tw-text-hm-700 tw-no-underline hover:tw-bg-hm-50" href="{{ route('login') }}">Login</a>
                    <a class="tw-inline-flex tw-rounded-xl tw-bg-hm-600 tw-px-4 tw-py-2 tw-text-sm tw-font-black tw-text-white tw-no-underline" href="{{ route('register.show') }}">Register</a>
                @else
                    <a class="tw-block tw-rounded-xl tw-px-3 tw-py-2 tw-text-sm tw-font-bold tw-text-slate-700 tw-no-underline hover:tw-bg-slate-100" href="{{ route('myhome') }}">Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="tw-w-full tw-rounded-xl tw-border-0 tw-bg-white tw-px-3 tw-py-2 tw-text-left tw-text-sm tw-font-bold tw-text-rose-600 hover:tw-bg-rose-50">Logout</button></form>
                @endguest
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('[data-hm-mobile-toggle]');
    const menu = document.querySelector('[data-hm-mobile-menu]');
    if (!toggle || !menu) return;
    toggle.addEventListener('click', function () {
        menu.classList.toggle('tw-hidden');
    });
});
</script>
@endpush
