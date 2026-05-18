@extends('layouts.app')

@section('title', 'Create Account | HeavenlyMatch')

@section('content')
@php
    $profileForOptions = [
        'self' => ['label' => 'Myself', 'sub' => 'আমি নিজে', 'icon' => '🙂'],
        'son' => ['label' => 'Son', 'sub' => 'ছেলের জন্য', 'icon' => '👦'],
        'daughter' => ['label' => 'Daughter', 'sub' => 'মেয়ের জন্য', 'icon' => '👧'],
        'brother' => ['label' => 'Brother', 'sub' => 'ভাইয়ের জন্য', 'icon' => '🤝'],
        'sister' => ['label' => 'Sister', 'sub' => 'বোনের জন্য', 'icon' => '🌸'],
        'relative' => ['label' => 'Relative', 'sub' => 'আত্মীয়ের জন্য', 'icon' => '🏠'],
        'friend' => ['label' => 'Friend', 'sub' => 'বন্ধুর জন্য', 'icon' => '✨'],
    ];

    $languageOptions = [
        'bn' => ['label' => 'বাংলা', 'sub' => 'Bangla'],
        'en' => ['label' => 'English', 'sub' => 'English'],
    ];

    $countryCodes = [
        '+880' => 'Bangladesh +880',
        '+91' => 'India +91',
        '+92' => 'Pakistan +92',
        '+1' => 'USA / Canada +1',
        '+44' => 'United Kingdom +44',
        '+971' => 'UAE +971',
        '+966' => 'Saudi Arabia +966',
        '+60' => 'Malaysia +60',
        '+65' => 'Singapore +65',
    ];

    $religionOptions = $religionOptions ?? ['Islam', 'Christian', 'Buddhist', 'Hindu'];
    $bloodGroupOptions = $bloodGroupOptions ?? ['A+', 'A-'];
    $maritalStatusOptions = $maritalStatusOptions ?? ['Single', 'Married', 'Divorced', 'Widow'];

    $oldProfileFor = old('profile_for', 'self');
    $oldLanguage = old('preferred_language', 'bn');
    $oldCountryCode = old('country_code', '+880');
    $oldReligion = old('religion', '');
    $oldBloodGroup = old('blood_group', '');
    $oldMaritalStatus = old('marital_status', '');

    $initialStep = 1;
    if ($errors->hasAny(['religion', 'blood_group', 'marital_status'])) {
        $initialStep = 1;
    } elseif ($errors->hasAny(['email'])) {
        $initialStep = 2;
    } elseif ($errors->hasAny(['country_code', 'mobile_number'])) {
        $initialStep = 3;
    } elseif ($errors->hasAny(['password', 'password_confirmation', 'terms'])) {
        $initialStep = 4;
    }
@endphp

<section class="tw-min-h-screen tw-bg-[radial-gradient(circle_at_top_left,#fff1f8,transparent_35%),linear-gradient(135deg,#ffffff_0%,#fff7fc_45%,#f7f4ff_100%)] tw-px-4 tw-py-8 sm:tw-py-12">
    <div class="tw-mx-auto tw-grid tw-w-full tw-max-w-6xl tw-gap-6 lg:tw-grid-cols-[0.78fr_1fr]">
        <aside class="tw-hidden tw-overflow-hidden tw-rounded-[2rem] tw-bg-gradient-to-br tw-from-[#5b1769] tw-via-[#8a1b80] tw-to-[#de2d83] tw-p-8 tw-text-white tw-shadow-soft lg:tw-block">
            <div class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-full tw-bg-white/15 tw-px-4 tw-py-2 tw-text-sm tw-font-bold">
                <span class="tw-h-2 tw-w-2 tw-rounded-full tw-bg-emerald-300"></span>
                HeavenlyMatch Matrimony
            </div>

            <h1 class="tw-mt-10 tw-text-5xl tw-font-black tw-leading-tight">Simple, safe account creation.</h1>
            <p class="tw-mt-5 tw-max-w-md tw-text-base tw-leading-7 tw-text-white/80">
                ইউজার যেন মোবাইল অ্যাপের মতো সহজ ধাপে রেজিস্ট্রেশন করতে পারে — clean fields, clear privacy, quick verification.
            </p>

            <div class="tw-mt-10 tw-space-y-4">
                <div class="tw-rounded-3xl tw-bg-white/12 tw-p-5 tw-backdrop-blur">
                    <div class="tw-flex tw-gap-4">
                        <span class="tw-grid tw-h-12 tw-w-12 tw-shrink-0 tw-place-items-center tw-rounded-2xl tw-bg-white/15">🔒</span>
                        <div>
                            <h3 class="tw-mb-1 tw-font-black">Privacy-first</h3>
                            <p class="tw-mb-0 tw-text-sm tw-leading-6 tw-text-white/75">Contact data stays private until the right stage of the matrimony workflow.</p>
                        </div>
                    </div>
                </div>
                <div class="tw-rounded-3xl tw-bg-white/12 tw-p-5 tw-backdrop-blur">
                    <div class="tw-flex tw-gap-4">
                        <span class="tw-grid tw-h-12 tw-w-12 tw-shrink-0 tw-place-items-center tw-rounded-2xl tw-bg-white/15">📱</span>
                        <div>
                            <h3 class="tw-mb-1 tw-font-black">App-ready flow</h3>
                            <p class="tw-mb-0 tw-text-sm tw-leading-6 tw-text-white/75">Same step layout can be reused later in Flutter, React Native, or API-driven apps.</p>
                        </div>
                    </div>
                </div>
                <div class="tw-rounded-3xl tw-bg-white/12 tw-p-5 tw-backdrop-blur">
                    <div class="tw-flex tw-gap-4">
                        <span class="tw-grid tw-h-12 tw-w-12 tw-shrink-0 tw-place-items-center tw-rounded-2xl tw-bg-white/15">✅</span>
                        <div>
                            <h3 class="tw-mb-1 tw-font-black">Biodata ready</h3>
                            <p class="tw-mb-0 tw-text-sm tw-leading-6 tw-text-white/75">After email verification, users continue full 10-step biodata from dashboard.</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <div class="tw-rounded-[2rem] tw-border tw-border-white/80 tw-bg-white/95 tw-p-5 tw-shadow-soft sm:tw-p-7 lg:tw-p-9">
            <div class="tw-mb-7 tw-flex tw-items-start tw-justify-between tw-gap-4">
                <div>
                    <div class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-full tw-bg-hm-50 tw-px-3 tw-py-1 tw-text-xs tw-font-black tw-text-hm-700 lg:tw-hidden">
                        <span>💍</span> HeavenlyMatch
                    </div>
                    <h2 class="tw-mt-3 tw-text-3xl tw-font-black tw-tracking-tight tw-text-slate-950 sm:tw-text-4xl">Create your account</h2>
                    <p class="tw-mt-2 tw-mb-0 tw-text-sm tw-text-slate-500">Step <span id="stepNumber" class="tw-font-black tw-text-hm-700">1</span> of 4 · <span id="stepLabel">Profile</span></p>
                </div>
                <a href="{{ route('login') }}" class="tw-rounded-full tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-py-2 tw-text-sm tw-font-black tw-text-slate-700 tw-no-underline hover:tw-border-hm-200 hover:tw-bg-hm-50 hover:tw-text-hm-700">Login</a>
            </div>

            <div class="tw-mb-8">
                <div class="tw-h-2 tw-overflow-hidden tw-rounded-full tw-bg-slate-100">
                    <div id="progressBar" class="tw-h-full tw-w-1/4 tw-rounded-full tw-bg-gradient-to-r tw-from-[#6b1b76] tw-to-[#e12f83] tw-transition-all tw-duration-300"></div>
                </div>
                <div class="tw-mt-4 tw-grid tw-grid-cols-4 tw-gap-2">
                    @foreach([1 => 'Profile', 2 => 'Email', 3 => 'Phone', 4 => 'Secure'] as $step => $label)
                        <button type="button" class="step-tab tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-p-3 tw-text-center tw-text-xs tw-font-black tw-text-slate-500 tw-transition" data-step-tab="{{ $step }}">
                            <span class="tw-mx-auto tw-mb-1 tw-grid tw-h-7 tw-w-7 tw-place-items-center tw-rounded-full tw-bg-slate-100">{{ $step }}</span>
                            <span class="tw-hidden sm:tw-inline">{{ $label }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            @if($errors->any())
                <div class="tw-mb-6 tw-rounded-3xl tw-border tw-border-rose-200 tw-bg-rose-50 tw-p-4 tw-text-rose-800">
                    <div class="tw-flex tw-gap-3">
                        <span class="tw-grid tw-h-9 tw-w-9 tw-shrink-0 tw-place-items-center tw-rounded-full tw-bg-rose-100 tw-font-black">!</span>
                        <div>
                            <h3 class="tw-mb-1 tw-text-sm tw-font-black">Please fix these fields</h3>
                            <ul class="tw-mb-0 tw-space-y-1 tw-pl-5 tw-text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form id="registrationForm" method="POST" action="{{ route('register.store') }}" class="tw-space-y-7" novalidate>
                @csrf

                <section class="reg-step tw-space-y-6" data-step="1" data-title="Profile">
                    <div>
                        <label class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-900">Profile for <span class="tw-text-hm-500">*</span></label>
                        <input type="hidden" name="profile_for" id="profile_for" value="{{ $oldProfileFor }}" required>
                        <div class="hm-dropdown tw-relative" data-target="profile_for">
                            <button type="button" class="hm-dropdown-btn tw-flex tw-w-full tw-items-center tw-justify-between tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-py-3 tw-text-left tw-shadow-sm tw-transition hover:tw-border-hm-200 focus:tw-border-hm-500 focus:tw-outline-none focus:tw-ring-4 focus:tw-ring-hm-100">
                                <span class="hm-dropdown-text tw-text-slate-400">Select profile creator</span>
                                <i class="bi bi-chevron-down tw-text-slate-400"></i>
                            </button>
                            <div class="hm-dropdown-menu tw-absolute tw-left-0 tw-right-0 tw-top-[calc(100%+8px)] tw-z-30 tw-hidden tw-max-h-72 tw-overflow-y-auto tw-rounded-3xl tw-border tw-border-slate-100 tw-bg-white tw-p-2 tw-shadow-soft">
                                @foreach($profileForOptions as $value => $item)
                                    <button type="button" class="hm-option tw-flex tw-w-full tw-items-center tw-gap-3 tw-rounded-2xl tw-px-3 tw-py-3 tw-text-left tw-transition hover:tw-bg-hm-50" data-value="{{ $value }}" data-label="{{ $item['label'] }} - {{ $item['sub'] }}">
                                        <span class="tw-grid tw-h-10 tw-w-10 tw-place-items-center tw-rounded-2xl tw-bg-hm-50">{{ $item['icon'] }}</span>
                                        <span>
                                            <span class="tw-block tw-text-sm tw-font-black tw-text-slate-950">{{ $item['label'] }}</span>
                                            <span class="tw-block tw-text-xs tw-text-slate-500">{{ $item['sub'] }}</span>
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @error('profile_for') <p class="tw-mt-2 tw-mb-0 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="tw-grid tw-gap-5 md:tw-grid-cols-2">
                        <div>
                            <label for="name" class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-900">Full name <span class="tw-text-hm-500">*</span></label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" class="tw-w-full tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-py-3 tw-outline-none tw-transition placeholder:tw-text-slate-400 focus:tw-border-hm-500 focus:tw-ring-4 focus:tw-ring-hm-100" placeholder="Enter full name" required maxlength="255" autocomplete="name">
                            @error('name') <p class="tw-mt-2 tw-mb-0 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-900">Preferred language <span class="tw-text-hm-500">*</span></label>
                            <input type="hidden" name="preferred_language" id="preferred_language" value="{{ $oldLanguage }}" required>
                            <div class="hm-dropdown tw-relative" data-target="preferred_language">
                                <button type="button" class="hm-dropdown-btn tw-flex tw-w-full tw-items-center tw-justify-between tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-py-3 tw-text-left tw-shadow-sm tw-transition hover:tw-border-hm-200 focus:tw-border-hm-500 focus:tw-outline-none focus:tw-ring-4 focus:tw-ring-hm-100">
                                    <span class="hm-dropdown-text tw-text-slate-400">Select language</span>
                                    <i class="bi bi-chevron-down tw-text-slate-400"></i>
                                </button>
                                <div class="hm-dropdown-menu tw-absolute tw-left-0 tw-right-0 tw-top-[calc(100%+8px)] tw-z-30 tw-hidden tw-rounded-3xl tw-border tw-border-slate-100 tw-bg-white tw-p-2 tw-shadow-soft">
                                    @foreach($languageOptions as $value => $item)
                                        <button type="button" class="hm-option tw-flex tw-w-full tw-items-center tw-justify-between tw-rounded-2xl tw-px-3 tw-py-3 tw-text-left tw-transition hover:tw-bg-hm-50" data-value="{{ $value }}" data-label="{{ $item['label'] }}">
                                            <span>
                                                <span class="tw-block tw-text-sm tw-font-black tw-text-slate-950">{{ $item['label'] }}</span>
                                                <span class="tw-block tw-text-xs tw-text-slate-500">{{ $item['sub'] }}</span>
                                            </span>
                                            <i class="bi bi-check2 tw-hidden tw-text-hm-600"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @error('preferred_language') <p class="tw-mt-2 tw-mb-0 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="tw-mb-3 tw-block tw-text-sm tw-font-black tw-text-slate-900">Gender / Biodata type <span class="tw-text-hm-500">*</span></label>
                        <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2">
                            <label class="tw-cursor-pointer">
                                <input type="radio" name="gender" value="male" class="tw-peer tw-sr-only" {{ old('gender') === 'male' ? 'checked' : '' }} required>
                                <span class="tw-flex tw-items-center tw-gap-4 tw-rounded-3xl tw-border tw-border-slate-200 tw-bg-white tw-p-4 tw-transition peer-checked:tw-border-hm-500 peer-checked:tw-bg-hm-50 peer-checked:tw-shadow-glow">
                                    <span class="tw-grid tw-h-12 tw-w-12 tw-place-items-center tw-rounded-2xl tw-bg-blue-50">👨</span>
                                    <span><span class="tw-block tw-font-black tw-text-slate-950">Male</span><span class="tw-text-sm tw-text-slate-500">পাত্রের বায়োডাটা</span></span>
                                </span>
                            </label>
                            <label class="tw-cursor-pointer">
                                <input type="radio" name="gender" value="female" class="tw-peer tw-sr-only" {{ old('gender') === 'female' ? 'checked' : '' }} required>
                                <span class="tw-flex tw-items-center tw-gap-4 tw-rounded-3xl tw-border tw-border-slate-200 tw-bg-white tw-p-4 tw-transition peer-checked:tw-border-hm-500 peer-checked:tw-bg-hm-50 peer-checked:tw-shadow-glow">
                                    <span class="tw-grid tw-h-12 tw-w-12 tw-place-items-center tw-rounded-2xl tw-bg-pink-50">👩</span>
                                    <span><span class="tw-block tw-font-black tw-text-slate-950">Female</span><span class="tw-text-sm tw-text-slate-500">পাত্রীর বায়োডাটা</span></span>
                                </span>
                            </label>
                        </div>
                        @error('gender') <p class="tw-mt-2 tw-mb-0 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="tw-rounded-3xl tw-bg-slate-50 tw-p-5">
                        <h3 class="tw-mb-1 tw-font-black tw-text-slate-950">Basic profile attributes</h3>
                        <p class="tw-mb-0 tw-text-sm tw-leading-6 tw-text-slate-600">These dropdowns come from Admin → User Attributes and help prefill biodata later.</p>
                    </div>

                    <div class="tw-grid tw-gap-5 md:tw-grid-cols-3">
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-900">Religion</label>
                            <input type="hidden" name="religion" id="religion" value="{{ $oldReligion }}">
                            <div class="hm-dropdown tw-relative" data-target="religion">
                                <button type="button" class="hm-dropdown-btn tw-flex tw-w-full tw-items-center tw-justify-between tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-py-3 tw-text-left tw-shadow-sm tw-transition hover:tw-border-hm-200 focus:tw-border-hm-500 focus:tw-outline-none focus:tw-ring-4 focus:tw-ring-hm-100">
                                    <span class="hm-dropdown-text tw-text-slate-400">Select religion</span>
                                    <i class="bi bi-chevron-down tw-text-slate-400"></i>
                                </button>
                                <div class="hm-dropdown-menu tw-absolute tw-left-0 tw-right-0 tw-top-[calc(100%+8px)] tw-z-30 tw-hidden tw-max-h-72 tw-overflow-y-auto tw-rounded-3xl tw-border tw-border-slate-100 tw-bg-white tw-p-2 tw-shadow-soft">
                                    @foreach($religionOptions as $option)
                                        <button type="button" class="hm-option tw-flex tw-w-full tw-items-center tw-justify-between tw-rounded-2xl tw-px-3 tw-py-3 tw-text-left tw-transition hover:tw-bg-hm-50" data-value="{{ $option }}" data-label="{{ $option }}">
                                            <span class="tw-text-sm tw-font-black tw-text-slate-950">{{ $option }}</span>
                                            <i class="bi bi-check2 tw-hidden tw-text-hm-600"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @error('religion') <p class="tw-mt-2 tw-mb-0 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-900">Blood Group</label>
                            <input type="hidden" name="blood_group" id="blood_group" value="{{ $oldBloodGroup }}">
                            <div class="hm-dropdown tw-relative" data-target="blood_group">
                                <button type="button" class="hm-dropdown-btn tw-flex tw-w-full tw-items-center tw-justify-between tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-py-3 tw-text-left tw-shadow-sm tw-transition hover:tw-border-hm-200 focus:tw-border-hm-500 focus:tw-outline-none focus:tw-ring-4 focus:tw-ring-hm-100">
                                    <span class="hm-dropdown-text tw-text-slate-400">Select blood group</span>
                                    <i class="bi bi-chevron-down tw-text-slate-400"></i>
                                </button>
                                <div class="hm-dropdown-menu tw-absolute tw-left-0 tw-right-0 tw-top-[calc(100%+8px)] tw-z-30 tw-hidden tw-max-h-72 tw-overflow-y-auto tw-rounded-3xl tw-border tw-border-slate-100 tw-bg-white tw-p-2 tw-shadow-soft">
                                    @foreach($bloodGroupOptions as $option)
                                        <button type="button" class="hm-option tw-flex tw-w-full tw-items-center tw-justify-between tw-rounded-2xl tw-px-3 tw-py-3 tw-text-left tw-transition hover:tw-bg-hm-50" data-value="{{ $option }}" data-label="{{ $option }}">
                                            <span class="tw-text-sm tw-font-black tw-text-slate-950">{{ $option }}</span>
                                            <i class="bi bi-check2 tw-hidden tw-text-hm-600"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @error('blood_group') <p class="tw-mt-2 tw-mb-0 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-900">Marital Status</label>
                            <input type="hidden" name="marital_status" id="marital_status" value="{{ $oldMaritalStatus }}">
                            <div class="hm-dropdown tw-relative" data-target="marital_status">
                                <button type="button" class="hm-dropdown-btn tw-flex tw-w-full tw-items-center tw-justify-between tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-py-3 tw-text-left tw-shadow-sm tw-transition hover:tw-border-hm-200 focus:tw-border-hm-500 focus:tw-outline-none focus:tw-ring-4 focus:tw-ring-hm-100">
                                    <span class="hm-dropdown-text tw-text-slate-400">Select marital status</span>
                                    <i class="bi bi-chevron-down tw-text-slate-400"></i>
                                </button>
                                <div class="hm-dropdown-menu tw-absolute tw-left-0 tw-right-0 tw-top-[calc(100%+8px)] tw-z-30 tw-hidden tw-max-h-72 tw-overflow-y-auto tw-rounded-3xl tw-border tw-border-slate-100 tw-bg-white tw-p-2 tw-shadow-soft">
                                    @foreach($maritalStatusOptions as $option)
                                        <button type="button" class="hm-option tw-flex tw-w-full tw-items-center tw-justify-between tw-rounded-2xl tw-px-3 tw-py-3 tw-text-left tw-transition hover:tw-bg-hm-50" data-value="{{ $option }}" data-label="{{ $option }}">
                                            <span class="tw-text-sm tw-font-black tw-text-slate-950">{{ $option }}</span>
                                            <i class="bi bi-check2 tw-hidden tw-text-hm-600"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @error('marital_status') <p class="tw-mt-2 tw-mb-0 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section class="reg-step tw-hidden tw-space-y-6" data-step="2" data-title="Email">
                    <div class="tw-rounded-3xl tw-bg-hm-50 tw-p-5">
                        <h3 class="tw-mb-1 tw-font-black tw-text-slate-950">Verify with email</h3>
                        <p class="tw-mb-0 tw-text-sm tw-leading-6 tw-text-slate-600">We will send a 6-digit code and one-click verification link to your email.</p>
                    </div>
                    <div>
                        <label for="email" class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-900">Email address <span class="tw-text-hm-500">*</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="tw-w-full tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-py-3 tw-outline-none tw-transition placeholder:tw-text-slate-400 focus:tw-border-hm-500 focus:tw-ring-4 focus:tw-ring-hm-100" placeholder="name@example.com" required autocomplete="email">
                        @error('email') <p class="tw-mt-2 tw-mb-0 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </section>

                <section class="reg-step tw-hidden tw-space-y-6" data-step="3" data-title="Phone">
                    <div class="tw-rounded-3xl tw-bg-slate-50 tw-p-5">
                        <h3 class="tw-mb-1 tw-font-black tw-text-slate-950">Phone number</h3>
                        <p class="tw-mb-0 tw-text-sm tw-leading-6 tw-text-slate-600">Use only digits in the mobile number. Do not write country code twice.</p>
                    </div>
                    <div class="tw-grid tw-gap-5 md:tw-grid-cols-[0.75fr_1.25fr]">
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-900">Country code <span class="tw-text-hm-500">*</span></label>
                            <input type="hidden" name="country_code" id="country_code" value="{{ $oldCountryCode }}" required>
                            <div class="hm-dropdown tw-relative" data-target="country_code">
                                <button type="button" class="hm-dropdown-btn tw-flex tw-w-full tw-items-center tw-justify-between tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-py-3 tw-text-left tw-shadow-sm tw-transition hover:tw-border-hm-200 focus:tw-border-hm-500 focus:tw-outline-none focus:tw-ring-4 focus:tw-ring-hm-100">
                                    <span class="hm-dropdown-text tw-text-slate-400">Select country</span>
                                    <i class="bi bi-chevron-down tw-text-slate-400"></i>
                                </button>
                                <div class="hm-dropdown-menu tw-absolute tw-left-0 tw-right-0 tw-top-[calc(100%+8px)] tw-z-30 tw-hidden tw-max-h-72 tw-overflow-y-auto tw-rounded-3xl tw-border tw-border-slate-100 tw-bg-white tw-p-2 tw-shadow-soft">
                                    @foreach($countryCodes as $code => $label)
                                        <button type="button" class="hm-option tw-flex tw-w-full tw-items-center tw-justify-between tw-rounded-2xl tw-px-3 tw-py-3 tw-text-left tw-transition hover:tw-bg-hm-50" data-value="{{ $code }}" data-label="{{ $label }}">
                                            <span class="tw-text-sm tw-font-black tw-text-slate-950">{{ $label }}</span>
                                            <i class="bi bi-check2 tw-hidden tw-text-hm-600"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @error('country_code') <p class="tw-mt-2 tw-mb-0 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="mobile_number" class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-900">Mobile number <span class="tw-text-hm-500">*</span></label>
                            <input id="mobile_number" type="tel" name="mobile_number" value="{{ old('mobile_number') }}" class="tw-w-full tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-py-3 tw-outline-none tw-transition placeholder:tw-text-slate-400 focus:tw-border-hm-500 focus:tw-ring-4 focus:tw-ring-hm-100" placeholder="1712345678" required pattern="[0-9]{8,15}" inputmode="numeric" autocomplete="tel-national">
                            @error('mobile_number') <p class="tw-mt-2 tw-mb-0 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section class="reg-step tw-hidden tw-space-y-6" data-step="4" data-title="Secure">
                    <div class="tw-grid tw-gap-5 md:tw-grid-cols-2">
                        <div>
                            <label for="password" class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-900">Password <span class="tw-text-hm-500">*</span></label>
                            <div class="tw-relative">
                                <input id="password" type="password" name="password" class="tw-w-full tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-py-3 tw-pr-12 tw-outline-none tw-transition placeholder:tw-text-slate-400 focus:tw-border-hm-500 focus:tw-ring-4 focus:tw-ring-hm-100" placeholder="Minimum 8 characters" required minlength="8" maxlength="64" autocomplete="new-password">
                                <button type="button" class="toggle-password tw-absolute tw-inset-y-0 tw-right-3 tw-my-auto tw-text-slate-400 hover:tw-text-hm-600" data-target="password"><i class="bi bi-eye"></i></button>
                            </div>
                            @error('password') <p class="tw-mt-2 tw-mb-0 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="tw-mb-2 tw-block tw-text-sm tw-font-black tw-text-slate-900">Confirm password <span class="tw-text-hm-500">*</span></label>
                            <div class="tw-relative">
                                <input id="password_confirmation" type="password" name="password_confirmation" class="tw-w-full tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-4 tw-py-3 tw-pr-12 tw-outline-none tw-transition placeholder:tw-text-slate-400 focus:tw-border-hm-500 focus:tw-ring-4 focus:tw-ring-hm-100" placeholder="Repeat password" required minlength="8" maxlength="64" autocomplete="new-password">
                                <button type="button" class="toggle-password tw-absolute tw-inset-y-0 tw-right-3 tw-my-auto tw-text-slate-400 hover:tw-text-hm-600" data-target="password_confirmation"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                    </div>

                    <label class="tw-flex tw-cursor-pointer tw-items-start tw-gap-3 tw-rounded-3xl tw-border tw-border-slate-200 tw-bg-slate-50 tw-p-4">
                        <input type="checkbox" name="terms" value="1" class="tw-mt-1 tw-h-5 tw-w-5 tw-rounded tw-border-slate-300 tw-text-hm-600 focus:tw-ring-hm-500" {{ old('terms') ? 'checked' : '' }} required>
                        <span class="tw-text-sm tw-leading-6 tw-text-slate-700">
                            I accept the privacy and matrimony-use agreement.
                            <button type="button" id="openTermsModal" class="tw-font-black tw-text-hm-700 tw-underline">Read terms</button>
                        </span>
                    </label>
                    @error('terms') <p class="tw-mt-2 tw-mb-0 tw-text-sm tw-font-semibold tw-text-rose-600">{{ $message }}</p> @enderror
                </section>

                <div class="tw-flex tw-items-center tw-justify-between tw-gap-3 tw-border-t tw-border-slate-100 tw-pt-5">
                    <button type="button" id="prevBtn" class="tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-6 tw-py-3 tw-text-sm tw-font-black tw-text-slate-600 tw-transition hover:tw-bg-slate-50">← Back</button>
                    <button type="button" id="nextBtn" class="tw-rounded-2xl tw-bg-gradient-to-r tw-from-[#7a1b7e] tw-to-[#e12f83] tw-px-8 tw-py-3 tw-text-sm tw-font-black tw-text-white tw-shadow-glow tw-transition hover:tw-scale-[1.01]">Next →</button>
                    <button type="submit" id="submitBtn" class="tw-hidden tw-rounded-2xl tw-bg-gradient-to-r tw-from-[#7a1b7e] tw-to-[#e12f83] tw-px-8 tw-py-3 tw-text-sm tw-font-black tw-text-white tw-shadow-glow tw-transition hover:tw-scale-[1.01]">Create account</button>
                </div>
            </form>
        </div>
    </div>
</section>

<div id="termsModal" class="tw-fixed tw-inset-0 tw-z-[9999] tw-hidden tw-items-center tw-justify-center tw-bg-slate-950/50 tw-p-4">
    <div class="tw-w-full tw-max-w-lg tw-rounded-[2rem] tw-bg-white tw-p-6 tw-shadow-soft">
        <div class="tw-flex tw-items-start tw-justify-between tw-gap-4">
            <div>
                <h3 class="tw-mb-1 tw-text-2xl tw-font-black tw-text-slate-950">Privacy & matrimony-use agreement</h3>
                <p class="tw-mb-0 tw-text-sm tw-text-slate-500">Please read before creating an account.</p>
            </div>
            <button type="button" class="close-modal tw-grid tw-h-10 tw-w-10 tw-place-items-center tw-rounded-full tw-bg-slate-100 tw-text-slate-500 hover:tw-bg-slate-200">×</button>
        </div>
        <div class="tw-mt-5 tw-space-y-3 tw-text-sm tw-leading-6 tw-text-slate-700">
            <p class="tw-mb-0">Your account information must be truthful and respectful. Fake, duplicate, or misleading profiles may be rejected.</p>
            <p class="tw-mb-0">Contact details are used only for verification and matrimony workflow. Do not share other people's private information without permission.</p>
            <p class="tw-mb-0">You agree to use HeavenlyMatch for lawful, respectful marriage-related communication only.</p>
        </div>
        <div class="tw-mt-6 tw-flex tw-justify-end tw-gap-3">
            <button type="button" class="close-modal tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-5 tw-py-3 tw-text-sm tw-font-black tw-text-slate-600">Close</button>
            <button type="button" id="acceptTermsBtn" class="tw-rounded-2xl tw-bg-hm-600 tw-px-5 tw-py-3 tw-text-sm tw-font-black tw-text-white">Accept terms</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registrationForm');
    const steps = Array.from(document.querySelectorAll('.reg-step'));
    const tabs = Array.from(document.querySelectorAll('[data-step-tab]'));
    const progressBar = document.getElementById('progressBar');
    const stepNumber = document.getElementById('stepNumber');
    const stepLabel = document.getElementById('stepLabel');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    let currentStep = Number(@json($initialStep));

    function showStep(step) {
        currentStep = Math.max(1, Math.min(4, step));
        steps.forEach(section => section.classList.toggle('tw-hidden', Number(section.dataset.step) !== currentStep));
        tabs.forEach(tab => {
            const isActive = Number(tab.dataset.stepTab) === currentStep;
            tab.classList.toggle('tw-border-hm-500', isActive);
            tab.classList.toggle('tw-bg-hm-50', isActive);
            tab.classList.toggle('tw-text-hm-700', isActive);
            const bubble = tab.querySelector('span');
            if (bubble) {
                bubble.classList.toggle('tw-bg-hm-600', isActive);
                bubble.classList.toggle('tw-text-white', isActive);
            }
        });
        progressBar.style.width = (currentStep * 25) + '%';
        stepNumber.textContent = currentStep;
        const active = steps.find(section => Number(section.dataset.step) === currentStep);
        stepLabel.textContent = active ? active.dataset.title : 'Profile';
        prevBtn.disabled = currentStep === 1;
        prevBtn.classList.toggle('tw-opacity-50', currentStep === 1);
        nextBtn.classList.toggle('tw-hidden', currentStep === 4);
        submitBtn.classList.toggle('tw-hidden', currentStep !== 4);
    }

    function validateCurrentStep() {
        const active = steps.find(section => Number(section.dataset.step) === currentStep);
        const fields = Array.from(active.querySelectorAll('input, select, textarea')).filter(el => !el.disabled);
        for (const field of fields) {
            if (field.type === 'hidden' && field.required && !field.value) {
                const dropdown = active.querySelector(`[data-target="${field.id}"] .hm-dropdown-btn`);
                if (dropdown) {
                    dropdown.classList.add('tw-border-rose-400', 'tw-ring-4', 'tw-ring-rose-100');
                    dropdown.focus();
                    setTimeout(() => dropdown.classList.remove('tw-border-rose-400', 'tw-ring-4', 'tw-ring-rose-100'), 1800);
                }
                return false;
            }
            if (!field.checkValidity()) {
                field.reportValidity();
                return false;
            }
        }
        return true;
    }

    nextBtn.addEventListener('click', () => {
        if (validateCurrentStep()) showStep(currentStep + 1);
    });

    prevBtn.addEventListener('click', () => showStep(currentStep - 1));

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = Number(tab.dataset.stepTab);
            if (target < currentStep || validateCurrentStep()) showStep(target);
        });
    });

    document.querySelectorAll('.hm-dropdown').forEach(dropdown => {
        const targetId = dropdown.dataset.target;
        const input = document.getElementById(targetId);
        const btn = dropdown.querySelector('.hm-dropdown-btn');
        const text = dropdown.querySelector('.hm-dropdown-text');
        const menu = dropdown.querySelector('.hm-dropdown-menu');
        const options = dropdown.querySelectorAll('.hm-option');

        function setSelected(value, label) {
            input.value = value;
            text.textContent = label;
            text.classList.remove('tw-text-slate-400');
            text.classList.add('tw-text-slate-900', 'tw-font-bold');
            options.forEach(option => {
                const selected = option.dataset.value === value;
                option.classList.toggle('tw-bg-hm-50', selected);
                const icon = option.querySelector('.bi-check2');
                if (icon) icon.classList.toggle('tw-hidden', !selected);
            });
        }

        btn.addEventListener('click', () => {
            document.querySelectorAll('.hm-dropdown-menu').forEach(other => {
                if (other !== menu) other.classList.add('tw-hidden');
            });
            menu.classList.toggle('tw-hidden');
        });

        options.forEach(option => {
            option.addEventListener('click', () => {
                setSelected(option.dataset.value, option.dataset.label);
                menu.classList.add('tw-hidden');
            });
            if (input.value && option.dataset.value === input.value) {
                setSelected(option.dataset.value, option.dataset.label);
            }
        });
    });

    document.addEventListener('click', event => {
        if (!event.target.closest('.hm-dropdown')) {
            document.querySelectorAll('.hm-dropdown-menu').forEach(menu => menu.classList.add('tw-hidden'));
        }
    });

    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.target);
            const icon = button.querySelector('i');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

    const modal = document.getElementById('termsModal');
    const openTerms = document.getElementById('openTermsModal');
    const acceptTerms = document.getElementById('acceptTermsBtn');
    const termsInput = form.querySelector('input[name="terms"]');
    openTerms.addEventListener('click', () => {
        modal.classList.remove('tw-hidden');
        modal.classList.add('tw-flex');
    });
    document.querySelectorAll('.close-modal').forEach(button => {
        button.addEventListener('click', () => {
            modal.classList.add('tw-hidden');
            modal.classList.remove('tw-flex');
        });
    });
    acceptTerms.addEventListener('click', () => {
        termsInput.checked = true;
        modal.classList.add('tw-hidden');
        modal.classList.remove('tw-flex');
    });

    showStep(currentStep);
});
</script>
@endpush
