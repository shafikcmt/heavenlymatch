@extends('layouts.app')

@section('title', 'Register | HeavenlyMatch')

@section('content')
<section class="tw-relative tw-isolate tw-overflow-hidden tw-bg-gradient-to-br tw-from-white tw-via-hm-50 tw-to-purple-50 tw-px-4 tw-py-10 sm:tw-py-16">
    <div class="tw-pointer-events-none tw-absolute tw-left-[-140px] tw-top-[-140px] tw-h-80 tw-w-80 tw-rounded-full tw-bg-hm-200 tw-opacity-50 tw-blur-3xl"></div>
    <div class="tw-pointer-events-none tw-absolute tw-bottom-[-160px] tw-right-[-160px] tw-h-96 tw-w-96 tw-rounded-full tw-bg-purple-200 tw-opacity-60 tw-blur-3xl"></div>

    <div class="tw-relative tw-mx-auto tw-max-w-5xl">
        <div class="tw-mx-auto tw-max-w-2xl tw-text-center">
            <span class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-full tw-bg-white tw-px-4 tw-py-2 tw-text-sm tw-font-black tw-text-hm-700 tw-shadow-sm">
                <span>💍</span> HeavenlyMatch
            </span>
            <h1 class="tw-mt-5 tw-text-4xl tw-font-black tw-tracking-tight tw-text-slate-950 sm:tw-text-5xl">Create your matrimony account</h1>
            <p class="tw-mt-4 tw-text-base tw-leading-7 tw-text-slate-600">Choose how you want to start. Email registration includes app-ready fields for profile owner, language, verification and mobile OTP.</p>
        </div>

        <div class="tw-mt-10 tw-grid tw-gap-4 md:tw-grid-cols-3">
            <a href="#" class="tw-group tw-rounded-[1.75rem] tw-border tw-border-slate-200 tw-bg-white tw-p-6 tw-text-slate-900 tw-no-underline tw-shadow-soft tw-transition hover:-tw-translate-y-1 hover:tw-border-red-200 hover:tw-bg-red-50">
                <span class="tw-grid tw-h-14 tw-w-14 tw-place-items-center tw-rounded-2xl tw-bg-red-50 tw-text-2xl tw-transition group-hover:tw-bg-white">G</span>
                <h3 class="tw-mt-5 tw-text-xl tw-font-black">Continue with Google</h3>
                <p class="tw-mb-0 tw-mt-2 tw-text-sm tw-leading-6 tw-text-slate-600">Quick account creation when social login is enabled.</p>
            </a>

            <a href="{{ route('register.show') }}" class="tw-group tw-rounded-[1.75rem] tw-border tw-border-hm-200 tw-bg-gradient-to-br tw-from-hm-700 tw-to-hm-500 tw-p-6 tw-text-white tw-no-underline tw-shadow-glow tw-transition hover:-tw-translate-y-1">
                <span class="tw-grid tw-h-14 tw-w-14 tw-place-items-center tw-rounded-2xl tw-bg-white/15 tw-text-2xl">✉️</span>
                <h3 class="tw-mt-5 tw-text-xl tw-font-black">Create with Email</h3>
                <p class="tw-mb-0 tw-mt-2 tw-text-sm tw-leading-6 tw-text-white/80">Recommended. Complete profile, language, email and phone step-by-step.</p>
            </a>

            <a href="https://www.youtube.com/watch?v=your-video-id" target="_blank" class="tw-group tw-rounded-[1.75rem] tw-border tw-border-slate-200 tw-bg-white tw-p-6 tw-text-slate-900 tw-no-underline tw-shadow-soft tw-transition hover:-tw-translate-y-1 hover:tw-border-slate-300 hover:tw-bg-slate-50">
                <span class="tw-grid tw-h-14 tw-w-14 tw-place-items-center tw-rounded-2xl tw-bg-slate-100 tw-text-2xl">▶️</span>
                <h3 class="tw-mt-5 tw-text-xl tw-font-black">Watch guide</h3>
                <p class="tw-mb-0 tw-mt-2 tw-text-sm tw-leading-6 tw-text-slate-600">Show users how to register and complete biodata correctly.</p>
            </a>
        </div>

        <div class="tw-mt-8 tw-text-center tw-text-sm tw-text-slate-600">
            Already registered? <a href="{{ route('login') }}" class="tw-font-black tw-text-hm-700 tw-no-underline hover:tw-text-hm-500">Login here</a>
        </div>
    </div>
</section>
@endsection
