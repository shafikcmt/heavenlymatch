@php($hmSiteName = \App\Models\SystemSetting::get('general.site_name', 'HeavenlyMatch'))
@php($hmFooterAbout = \App\Models\SystemSetting::get('frontend.footer_about', 'A privacy-first matrimony platform for serious family-led marriage search.'))
<footer class="tw-border-t tw-border-slate-200 tw-bg-slate-950 tw-px-4 tw-py-12 tw-text-white">
    <div class="tw-mx-auto tw-grid tw-max-w-7xl tw-gap-8 md:tw-grid-cols-4">
        <div class="md:tw-col-span-2">
            <div class="tw-flex tw-items-center tw-gap-3">
                <span class="tw-grid tw-h-11 tw-w-11 tw-place-items-center tw-rounded-2xl tw-bg-gradient-to-br tw-from-hm-700 tw-to-hm-500 tw-text-white">❤</span>
                <h3 class="tw-m-0 tw-text-xl tw-font-black">{{ $hmSiteName }}</h3>
            </div>
            <p class="tw-mt-4 tw-max-w-xl tw-text-sm tw-leading-6 tw-text-slate-300">{{ $hmFooterAbout }}</p>
            <p class="tw-mt-3 tw-text-xs tw-font-semibold tw-uppercase tw-tracking-widest tw-text-slate-500">Strictly for matrimony purpose only, not a dating website.</p>
        </div>
        <div>
            <h4 class="tw-text-sm tw-font-black tw-uppercase tw-tracking-wide tw-text-slate-400">Quick links</h4>
            <div class="tw-mt-4 tw-space-y-2 tw-text-sm">
                <a class="tw-block tw-text-slate-300 tw-no-underline hover:tw-text-white" href="{{ route('about') }}">About</a>
                <a class="tw-block tw-text-slate-300 tw-no-underline hover:tw-text-white" href="{{ route('guide') }}">Guide</a>
                <a class="tw-block tw-text-slate-300 tw-no-underline hover:tw-text-white" href="{{ route('faq') }}">FAQ</a>
                <a class="tw-block tw-text-slate-300 tw-no-underline hover:tw-text-white" href="{{ route('contact') }}">Contact</a>
            </div>
        </div>
        <div>
            <h4 class="tw-text-sm tw-font-black tw-uppercase tw-tracking-wide tw-text-slate-400">Support</h4>
            <div class="tw-mt-4 tw-space-y-2 tw-text-sm tw-text-slate-300">
                <p class="tw-m-0">support@HeavenlyMatch.com</p>
                <p class="tw-m-0">+880 9613-820303</p>
                <p class="tw-m-0">Mymensingh, Bangladesh</p>
            </div>
        </div>
    </div>
    <div class="tw-mx-auto tw-mt-8 tw-max-w-7xl tw-border-t tw-border-white/10 tw-pt-5 tw-text-center tw-text-xs tw-text-slate-400">© {{ date('Y') }} {{ $hmSiteName }}. All rights reserved.</div>
</footer>
