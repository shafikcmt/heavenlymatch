@extends('layouts.app')

@section('title', 'Contact')

@section('content')
@php($hmSiteName = \App\Models\SystemSetting::get('general.site_name', 'HeavenlyMatch'))
@php($hmContactIntro = \App\Models\SystemSetting::get('pages.contact_intro'))
@php($hmSupportEmail = \App\Models\SystemSetting::get('contact.email', 'support@HeavenlyMatch.com'))
@php($hmSupportPhone = \App\Models\SystemSetting::get('contact.phone', '+880 9613-820303'))
@php($hmSupportAddress = \App\Models\SystemSetting::get('contact.address', '5/25/Ga Outer Stadium, Mymensingh, Bangladesh'))
<section class="tw-relative tw-overflow-hidden tw-bg-[radial-gradient(circle_at_top_left,#ffe8f5,transparent_33%),linear-gradient(135deg,#ffffff_0%,#fff7fc_46%,#f8fafc_100%)] tw-px-4 tw-py-16 sm:tw-py-20">
    <div class="tw-pointer-events-none tw-absolute -tw-left-20 tw-top-0 tw-h-80 tw-w-80 tw-rounded-full tw-bg-hm-100 tw-blur-3xl"></div>
    <div class="tw-pointer-events-none tw-absolute -tw-right-24 tw-bottom-0 tw-h-80 tw-w-80 tw-rounded-full tw-bg-hm-200/70 tw-blur-3xl"></div>

    <div class="tw-relative tw-mx-auto tw-max-w-7xl">
        <div class="tw-mx-auto tw-mb-12 tw-max-w-3xl tw-text-center">
            <span class="tw-inline-flex tw-items-center tw-rounded-full tw-border tw-border-hm-200 tw-bg-white tw-px-4 tw-py-2 tw-text-xs tw-font-black tw-uppercase tw-tracking-widest tw-text-hm-700 tw-shadow-shad">Contact Us</span>
            <h1 class="tw-mt-4 tw-text-4xl tw-font-black tw-tracking-tight tw-text-slate-950 md:tw-text-6xl">We are here to help</h1>
            <p class="tw-mx-auto tw-mt-4 tw-max-w-2xl tw-text-base tw-leading-7 tw-text-slate-600">
                {{ $hmContactIntro ?: 'For any query you may have, please fill out the form below and send it to us. We will contact you soon InShaAllah.' }}
            </p>
        </div>

        <div class="tw-grid tw-gap-6 lg:tw-grid-cols-5">
            <div class="tw-space-y-4 lg:tw-col-span-2">
                <div class="tw-rounded-3xl tw-border tw-border-slate-200 tw-bg-white/95 tw-p-6 tw-shadow-shad tw-transition hover:-tw-translate-y-1 hover:tw-shadow-soft">
                    <span class="tw-grid tw-h-12 tw-w-12 tw-place-items-center tw-rounded-2xl tw-bg-hm-50 tw-text-hm-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-6 tw-w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </span>
                    <h3 class="tw-mt-4 tw-text-xl tw-font-black tw-text-slate-950">Email Support</h3>
                    <p class="tw-mt-2 tw-text-sm tw-leading-6 tw-text-slate-600">Send us your questions anytime. Our support team will reply as soon as possible.</p>
                    <a class="tw-mt-4 tw-inline-flex tw-font-black tw-text-hm-700 tw-no-underline" href="mailto:{{ $hmSupportEmail }}">{{ $hmSupportEmail }}</a>
                </div>

                <div class="tw-rounded-3xl tw-border tw-border-slate-200 tw-bg-white/95 tw-p-6 tw-shadow-shad tw-transition hover:-tw-translate-y-1 hover:tw-shadow-soft">
                    <span class="tw-grid tw-h-12 tw-w-12 tw-place-items-center tw-rounded-2xl tw-bg-hm-50 tw-text-hm-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-6 tw-w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.32 1.78.59 2.63a2 2 0 0 1-.45 2.11L8 9.71a16 16 0 0 0 6.29 6.29l1.25-1.25a2 2 0 0 1 2.11-.45c.85.27 1.73.47 2.63.59A2 2 0 0 1 22 16.92Z"/></svg>
                    </span>
                    <h3 class="tw-mt-4 tw-text-xl tw-font-black tw-text-slate-950">Call Us</h3>
                    <p class="tw-mt-2 tw-text-sm tw-leading-6 tw-text-slate-600">Need direct assistance? Contact our support number during office hours.</p>
                    <a class="tw-mt-4 tw-inline-flex tw-font-black tw-text-hm-700 tw-no-underline" href="tel:{{ preg_replace('/\s+/', '', $hmSupportPhone) }}">{{ $hmSupportPhone }}</a>
                </div>

                <div class="tw-rounded-3xl tw-border tw-border-slate-200 tw-bg-white/95 tw-p-6 tw-shadow-shad tw-transition hover:-tw-translate-y-1 hover:tw-shadow-soft">
                    <span class="tw-grid tw-h-12 tw-w-12 tw-place-items-center tw-rounded-2xl tw-bg-hm-50 tw-text-hm-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-6 tw-w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    </span>
                    <h3 class="tw-mt-4 tw-text-xl tw-font-black tw-text-slate-950">Office Address</h3>
                    <p class="tw-mt-2 tw-text-sm tw-leading-6 tw-text-slate-600">{{ $hmSupportAddress }}</p>
                </div>
            </div>

            <div class="lg:tw-col-span-3">
                <div class="tw-rounded-3xl tw-border tw-border-slate-200 tw-bg-white/95 tw-p-6 tw-shadow-soft sm:tw-p-8">
                    <div class="tw-flex tw-flex-col tw-gap-3 sm:tw-flex-row sm:tw-items-start sm:tw-justify-between">
                        <div>
                            <h2 class="tw-m-0 tw-text-2xl tw-font-black tw-text-slate-950 md:tw-text-3xl">Send a message</h2>
                            <p class="tw-mt-2 tw-text-sm tw-leading-6 tw-text-slate-600">Fill out the form and {{ $hmSiteName }} support will get back to you.</p>
                        </div>
                        <span class="tw-inline-flex tw-w-fit tw-rounded-full tw-bg-hm-50 tw-px-4 tw-py-2 tw-text-xs tw-font-black tw-text-hm-700">Fast Reply</span>
                    </div>

                    <form action="{{ route('contact.submit') }}" method="POST" class="tw-mt-7 tw-space-y-5">
                        @csrf
                        <div class="tw-grid tw-gap-5 md:tw-grid-cols-2">
                            <div>
                                <label for="name" class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-800">Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your name" required class="tw-flex tw-h-12 tw-w-full tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-text-sm tw-outline-none tw-transition placeholder:tw-text-slate-400 focus:tw-border-hm-600 focus:tw-ring-4 focus:tw-ring-hm-100 @error('name') tw-border-rose-400 @enderror">
                                @error('name')<p class="tw-mt-2 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="email" class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-800">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required class="tw-flex tw-h-12 tw-w-full tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-text-sm tw-outline-none tw-transition placeholder:tw-text-slate-400 focus:tw-border-hm-600 focus:tw-ring-4 focus:tw-ring-hm-100 @error('email') tw-border-rose-400 @enderror">
                                @error('email')<p class="tw-mt-2 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="subject" class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-800">Subject</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="How can we help?" required class="tw-flex tw-h-12 tw-w-full tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-text-sm tw-outline-none tw-transition placeholder:tw-text-slate-400 focus:tw-border-hm-600 focus:tw-ring-4 focus:tw-ring-hm-100 @error('subject') tw-border-rose-400 @enderror">
                            @error('subject')<p class="tw-mt-2 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="message" class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-800">Message</label>
                            <textarea id="message" name="message" rows="7" placeholder="Write your message here..." required class="tw-flex tw-w-full tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-py-3 tw-text-sm tw-outline-none tw-transition placeholder:tw-text-slate-400 focus:tw-border-hm-600 focus:tw-ring-4 focus:tw-ring-hm-100 @error('message') tw-border-rose-400 @enderror">{{ old('message') }}</textarea>
                            @error('message')<p class="tw-mt-2 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit" class="tw-inline-flex tw-w-full tw-items-center tw-justify-center tw-gap-2 tw-rounded-2xl tw-border-0 tw-bg-hm-600 tw-px-6 tw-py-3 tw-text-sm tw-font-black tw-text-white tw-shadow-glow tw-transition hover:tw-bg-hm-700 md:tw-w-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="tw-h-4 tw-w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21 23 12 2.01 3 2 10l15 2-15 2 .01 7Z"/></svg>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
