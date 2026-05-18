@extends('layouts.app')

@section('title', 'FAQ')

@section('content')
@php($hmFaqIntro = \App\Models\SystemSetting::get('pages.faq_intro'))
<section class="tw-relative tw-overflow-hidden tw-bg-[radial-gradient(circle_at_top_left,#ffe8f5,transparent_34%),linear-gradient(135deg,#ffffff_0%,#fff7fc_48%,#f8fafc_100%)] tw-px-4 tw-py-16 sm:tw-py-20">
    <div class="tw-pointer-events-none tw-absolute -tw-left-24 tw-top-8 tw-h-72 tw-w-72 tw-rounded-full tw-bg-hm-100 tw-blur-3xl"></div>
    <div class="tw-pointer-events-none tw-absolute -tw-right-20 tw-bottom-0 tw-h-80 tw-w-80 tw-rounded-full tw-bg-hm-200/60 tw-blur-3xl"></div>

    <div class="tw-relative tw-mx-auto tw-max-w-5xl">
        <div class="tw-mx-auto tw-mb-10 tw-max-w-3xl tw-text-center">
            <span class="tw-inline-flex tw-items-center tw-rounded-full tw-border tw-border-hm-200 tw-bg-white tw-px-4 tw-py-2 tw-text-xs tw-font-black tw-uppercase tw-tracking-widest tw-text-hm-700 tw-shadow-shad">Help Center</span>
            <h1 class="tw-mt-4 tw-text-4xl tw-font-black tw-tracking-tight tw-text-slate-950 md:tw-text-5xl">Frequently Asked Questions</h1>
            <p class="tw-mx-auto tw-mt-4 tw-max-w-2xl tw-text-base tw-leading-7 tw-text-slate-600">
                {{ $hmFaqIntro ?: 'Get quick answers about HeavenlyMatch, biodata, privacy, approval, and communication.' }}
            </p>
        </div>

        <div class="tw-grid tw-gap-4">
            @foreach(($faqs ?? []) as $index => $faq)
                <details class="tw-group tw-rounded-3xl tw-border tw-border-slate-200 tw-bg-white/95 tw-p-1 tw-shadow-shad tw-transition hover:tw-shadow-soft">
                    <summary class="tw-flex tw-cursor-pointer tw-list-none tw-items-center tw-gap-4 tw-rounded-[1.35rem] tw-p-4 tw-text-left tw-text-base tw-font-black tw-text-slate-950 md:tw-p-5">
                        <span class="tw-grid tw-h-11 tw-w-11 tw-shrink-0 tw-place-items-center tw-rounded-2xl tw-bg-hm-50 tw-text-sm tw-font-black tw-text-hm-700">{{ $index + 1 }}</span>
                        <span class="tw-flex-1">{{ $faq['q'] }}</span>
                        <span class="tw-grid tw-h-9 tw-w-9 tw-shrink-0 tw-place-items-center tw-rounded-full tw-bg-slate-100 tw-text-slate-600 tw-transition group-open:tw-rotate-45 group-open:tw-bg-hm-600 group-open:tw-text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-5 tw-w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                        </span>
                    </summary>
                    <div class="tw-border-t tw-border-slate-100 tw-px-5 tw-pb-5 tw-pt-4 tw-text-sm tw-leading-7 tw-text-slate-600 md:tw-pl-[5.75rem]">
                        {{ $faq['a'] }}
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endsection
