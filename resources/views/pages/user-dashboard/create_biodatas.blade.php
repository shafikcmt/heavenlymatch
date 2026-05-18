@extends('layouts.user-dashboard-app')

@section('title', 'Create Biodata | HeavenlyMatch')

@section('content')
@php
    $step = max(1, min((int) ($step ?? 1), (int) ($maxStep ?? 10)));
    $maxStep = $maxStep ?? 10;
    $saved = $savedBiodata ? $savedBiodata->toArray() : [];
    $draftFlat = [];
    foreach (($draft ?? []) as $draftStep) {
        if (is_array($draftStep)) {
            $draftFlat = array_merge($draftFlat, $draftStep);
        }
    }

    $v = function (string $field, $default = '') use ($saved, $draftFlat) {
        return old($field, $draftFlat[$field] ?? $saved[$field] ?? $default);
    };

    $arr = function (string $field) use ($saved, $draftFlat) {
        $value = old($field, $draftFlat[$field] ?? $saved[$field] ?? []);
        if (is_string($value)) {
            return array_values(array_filter(array_map('trim', explode(',', $value))));
        }
        return is_array($value) ? $value : [];
    };

    $stepTitles = [
        1 => ['General Info', 'Basic identity and physical information'],
        2 => ['Address', 'Permanent, present and growing-up address'],
        3 => ['Education', 'Academic and Islamic education details'],
        4 => ['Family', 'Parents, siblings and financial background'],
        5 => ['Personal', 'Religious practice, lifestyle and personal notes'],
        6 => ['Occupation', 'Profession, income and halal status'],
        7 => ['Marriage', 'Guardian approval and marriage plan'],
        8 => ['Expected Partner', 'Preference for your future spouse'],
        9 => ['Pledge', 'Truthfulness and privacy confirmation'],
        10 => ['Contact', 'Private contact information for authority'],
    ];

    $heightOptions = ['4 ft below','4 ft','4 ft 1 in','4 ft 2 in','4 ft 3 in','4 ft 4 in','4 ft 5 in','4 ft 6 in','4 ft 7 in','4 ft 8 in','4 ft 9 in','4 ft 10 in','4 ft 11 in','5 ft','5 ft 1 in','5 ft 2 in','5 ft 3 in','5 ft 4 in','5 ft 5 in','5 ft 6 in','5 ft 7 in','5 ft 8 in','5 ft 9 in','5 ft 10 in','5 ft 11 in','6 ft','6 ft 1 in','6 ft 2 in','6 ft 3 in','6 ft 4 in','6 ft 5 in','6 ft 6 in','6 ft 7 in','7 ft+'];
    $weightOptions = array_merge(['30 kg below'], array_map(fn($n) => $n . ' kg', range(30, 120)), ['120 kg+']);
    $complexions = ['Black','Brown','Bright Brown','Fair','Very Fair'];
    $bloodGroups = ['A+','A-','B+','B-','AB+','AB-','O+','O-','Unknown'];
    $yesNo = ['Yes','No'];
    $financial = ['Upper Class','Upper Middle Class','Middle Class','Lower Middle Class','Lower Class'];
@endphp

@section('mobile_header')
    <x-app-mobile-header title="Biodata" :back="route('myhome')" />
@endsection

@push('styles')
<style>
    .hm-biodata-page { --hm-pink:#e21d63; --hm-purple:#08745c; }
    .hm-biodata-page .hm-hero { position:relative; isolation:isolate; }
    .hm-biodata-page .hm-hero::after {
        content:""; position:absolute; inset:auto 24px -34px auto; width:150px; height:150px;
        background:radial-gradient(circle, rgba(255,255,255,.35), transparent 68%); border-radius:999px; z-index:-1;
    }
    .hm-biodata-page .form-control,
    .hm-biodata-page .form-select {
        min-height:44px; border-radius:15px !important; border-color:#dde5ee !important;
        background-color:#fff !important; font-size:14px; color:#0f172a;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.75);
        transition:border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }
    .hm-biodata-page textarea.form-control { min-height:92px; padding-top:12px; }
    .hm-biodata-page .form-control:focus,
    .hm-biodata-page .form-select:focus {
        border-color:var(--hm-pink) !important;
        box-shadow:0 0 0 4px rgba(225,47,131,.10) !important;
    }
    .hm-biodata-page label { color:#101828; letter-spacing:-.01em; }
    .hm-biodata-page .hm-step-scroll::-webkit-scrollbar { height:4px; width:4px; }
    .hm-biodata-page .hm-step-scroll::-webkit-scrollbar-thumb { background:#f0a6cc; border-radius:999px; }
    .hm-biodata-page .hm-actionbar { position:sticky; bottom:0; margin-inline:-1rem; padding:12px 1rem; background:linear-gradient(180deg, rgba(255,255,255,.74), #fff 35%); backdrop-filter:blur(12px); border-radius:0 0 1.4rem 1.4rem; }
    @media (min-width:640px){ .hm-biodata-page .hm-actionbar { margin-inline:-1.25rem; padding-inline:1.25rem; } }
</style>
@endpush

<div class="hm-biodata-page tw-space-y-4 tw-px-4 tw-py-4 md:tw-p-0">
    <section class="tw-overflow-hidden hm-hero tw-rounded-[1.55rem] tw-bg-gradient-to-br tw-from-hm-green tw-via-[#0c8a6d] tw-to-hm-500 tw-p-4 tw-text-white tw-shadow-glow sm:tw-p-5">
        <div class="tw-flex tw-flex-col tw-gap-4 lg:tw-flex-row lg:tw-items-center lg:tw-justify-between">
            <div>
                <p class="tw-mb-1 tw-text-xs tw-font-bold tw-uppercase tw-tracking-wide tw-text-white/70">Biodata builder</p>
                <h1 class="tw-mb-1 tw-text-2xl tw-font-black sm:tw-text-3xl">{{ $stepTitles[$step][0] }}</h1>
                <p class="tw-mb-0 tw-max-w-2xl tw-text-sm tw-leading-6 tw-text-white/80">{{ $stepTitles[$step][1] }}. You can save a draft anytime and continue later from the dashboard.</p>
            </div>
            <div class="tw-rounded-2xl tw-bg-white/15 tw-p-3 tw-backdrop-blur">
                <div class="tw-text-sm tw-font-bold tw-text-white/80">Step {{ $step }} of {{ $maxStep }}</div>
                <div class="tw-mt-2 tw-h-2 tw-w-44 tw-overflow-hidden tw-rounded-full tw-bg-white/20">
                    <div class="tw-h-full tw-rounded-full tw-bg-white" style="width: {{ ($step / $maxStep) * 100 }}%"></div>
                </div>
            </div>
        </div>
    </section>

    <div class="tw-grid tw-gap-4 lg:tw-grid-cols-[270px_minmax(0,1fr)]">
        <aside class="tw-rounded-[1.45rem] tw-border tw-border-white/80 tw-bg-white/90 tw-p-3 tw-shadow-card tw-backdrop-blur">
            <div class="tw-hidden tw-text-[13px] tw-font-black tw-text-slate-500 lg:tw-block">Registration steps</div>
            <div class="hm-step-scroll tw-mt-3 tw-flex tw-gap-2 tw-overflow-x-auto lg:tw-block lg:tw-space-y-1.5">
                @foreach($stepTitles as $i => $meta)
                    <a href="{{ route('biodata.create', $i) }}" class="tw-flex tw-min-w-[150px] tw-items-center tw-gap-2 tw-rounded-xl tw-p-2.5 tw-no-underline lg:tw-min-w-0 {{ $i === $step ? 'tw-bg-emerald-50 tw-text-hm-green tw-ring-1 tw-ring-emerald-100' : ($i < $step ? 'tw-text-slate-700 hover:tw-bg-slate-50' : 'tw-text-slate-400 hover:tw-bg-slate-50') }}">
                        <span class="tw-grid tw-h-8 tw-w-8 tw-shrink-0 tw-place-items-center tw-rounded-full tw-text-[13px] tw-font-black {{ $i === $step ? 'tw-bg-hm-green tw-text-white' : ($i < $step ? 'tw-bg-emerald-100 tw-text-emerald-700' : 'tw-bg-slate-100 tw-text-slate-500') }}">{{ $i < $step ? '✓' : $i }}</span>
                        <span>
                            <span class="tw-block tw-text-[13px] tw-font-black">{{ $meta[0] }}</span>
                            <span class="tw-hidden tw-text-[11px] tw-leading-4 tw-text-slate-400 lg:tw-block">{{ $meta[1] }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </aside>

        <section class="tw-rounded-[1.45rem] tw-border tw-border-white/80 tw-bg-white/95 tw-p-4 tw-shadow-card tw-backdrop-blur sm:tw-p-5">
            @if ($errors->any())
                <div class="tw-mb-6 tw-rounded-2xl tw-border tw-border-rose-200 tw-bg-rose-50 tw-p-4 tw-text-sm tw-text-rose-800">
                    <div class="tw-mb-2 tw-font-black">Please fix the following:</div>
                    <ul class="tw-mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('biodata.store', $step) }}" enctype="multipart/form-data" id="biodataForm" class="hm-biodata-page tw-space-y-4">
                @csrf

                @if($step === 1)
                    <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2">
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Biodata Type *</label>
                            <select name="biodata_type" id="biodata_type" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required>
                                <option value="">Select</option>
                                <option value="groom" @selected($v('biodata_type', auth()->user()->gender === 'male' ? 'groom' : '') === 'groom')>Groom biodata</option>
                                <option value="bride" @selected($v('biodata_type', auth()->user()->gender === 'female' ? 'bride' : '') === 'bride')>Bride biodata</option>
                            </select>
                            @error('biodata_type') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Marital Status *</label>
                            <select name="marital_status" id="marital_status" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required>
                                <option value="">Select</option>
                                @foreach(['Never Married','Married','Divorced','Widow','Widower'] as $option)
                                    <option value="{{ $option }}" @selected($v('marital_status') === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('marital_status') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2 conditional-marriage">
                        <div class="sm:tw-col-span-2">
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Previous marriage details *</label>
                            <textarea name="previous_marriage_details" rows="3" class="form-control tw-rounded-2xl tw-border-slate-200" placeholder="Write divorce/widow/widower/married details clearly.">{{ $v('previous_marriage_details') }}</textarea>
                            @error('previous_marriage_details') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Children count</label>
                            <input type="number" min="0" max="20" name="children_count" value="{{ $v('children_count', 0) }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5">
                            @error('children_count') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2">
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Birth Date *</label>
                            <input type="date" name="birth_date" value="{{ $v('birth_date') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required>
                            <p class="tw-mt-2 tw-text-xs tw-text-slate-500">Use real NID/birth certificate age. Minimum age is 18.</p>
                            @error('birth_date') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Nationality *</label>
                            <input type="text" name="nationality" value="{{ $v('nationality', 'Bangladeshi') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required>
                            @error('nationality') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2">
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Height *</label>
                            <select name="height" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required>
                                <option value="">Select</option>
                                @foreach($heightOptions as $option)<option value="{{ $option }}" @selected($v('height') === $option)>{{ $option }}</option>@endforeach
                            </select>
                            @error('height') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Weight *</label>
                            <select name="weight" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required>
                                <option value="">Select</option>
                                @foreach($weightOptions as $option)<option value="{{ $option }}" @selected($v('weight') === $option)>{{ $option }}</option>@endforeach
                            </select>
                            @error('weight') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Complexion *</label>
                            <select name="complexion" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required>
                                <option value="">Select</option>
                                @foreach($complexions as $option)<option value="{{ $option }}" @selected($v('complexion') === $option)>{{ $option }}</option>@endforeach
                            </select>
                            @error('complexion') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Blood Group *</label>
                            <select name="blood_group" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required>
                                <option value="">Select</option>
                                @foreach($bloodGroups as $option)<option value="{{ $option }}" @selected($v('blood_group') === $option)>{{ $option }}</option>@endforeach
                            </select>
                            @error('blood_group') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @endif

                @if($step === 2)
                    <div class="tw-grid tw-gap-3">
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Permanent Address *</label>
                            <input type="text" name="permanent_address" id="permanent_address" value="{{ $v('permanent_address') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="District, upazila, union" required>
                            <p class="tw-mt-2 tw-text-xs tw-text-slate-500">Do not write house number. Write only village/area, district or country.</p>
                            @error('permanent_address') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Village / Area</label>
                            <input type="text" name="village_area" value="{{ $v('village_area') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="Example: Mirpur 10, Bagmara">
                            @error('village_area') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <label class="tw-flex tw-items-center tw-gap-2 tw-rounded-2xl tw-bg-slate-50 tw-p-3 tw-text-sm tw-font-bold tw-text-slate-600">
                            <input type="checkbox" id="sameAddress" class="form-check-input"> Present address is same as permanent address
                        </label>
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Present Address *</label>
                            <input type="text" name="present_address" id="present_address" value="{{ $v('present_address') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required>
                            @error('present_address') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Where did you grow up? *</label>
                            <input type="text" name="grew_up" value="{{ $v('grew_up') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="Example: Dhaka, Chattogram, Village home" required>
                            @error('grew_up') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @endif

                @if($step === 3)
                    <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2">
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Education Method *</label>
                            <select name="education_method" id="education_method" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required>
                                <option value="">Select</option>
                                @foreach(['General','Qawmi','Alia','General + Islamic','Other'] as $option)<option value="{{ $option }}" @selected($v('education_method') === $option)>{{ $option }}</option>@endforeach
                            </select>
                            @error('education_method') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Highest Qualification *</label>
                            <select name="highest_qualification" id="highest_qualification" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required>
                                <option value="">Select</option>
                                @foreach(['Below SSC','SSC','HSC','Diploma Running','Diploma','Bachelor Running','Bachelor','Postgraduate','Doctorate','Hifz','Mawlana','Mufti','Takmil','Takhassus','Other'] as $option)<option value="{{ $option }}" @selected($v('highest_qualification') === $option)>{{ $option }}</option>@endforeach
                            </select>
                            @error('highest_qualification') <p class="tw-mt-1 tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="tw-grid tw-gap-3 sm:tw-grid-cols-3 academic-fields">
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">SSC / equivalent year</label><input type="text" name="ssc_year" value="{{ $v('ssc_year') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="2016"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">SSC group</label><input type="text" name="ssc_group" value="{{ $v('ssc_group') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="Science / Business / Humanities"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Diploma medium</label><input type="text" name="diploma_medium" value="{{ $v('diploma_medium') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="HSC / Diploma"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Diploma subject</label><input type="text" name="diploma_subject" value="{{ $v('diploma_subject') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Diploma institution</label><input type="text" name="diploma_institution" value="{{ $v('diploma_institution') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Diploma year</label><input type="text" name="diploma_year" value="{{ $v('diploma_year') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Graduation subject</label><input type="text" name="graduation_subject" value="{{ $v('graduation_subject') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Graduation institution</label><input type="text" name="graduation_institution" value="{{ $v('graduation_institution') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Graduation year</label><input type="text" name="graduation_year" value="{{ $v('graduation_year') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Postgraduation subject</label><input type="text" name="postgraduation_subject" value="{{ $v('postgraduation_subject') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Postgraduation institution</label><input type="text" name="postgraduation_institution" value="{{ $v('postgraduation_institution') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Postgraduation year</label><input type="text" name="postgraduation_year" value="{{ $v('postgraduation_year') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                    </div>

                    <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2 islamic-fields">
                        <div>
                            <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Islamic titles</label>
                            <select name="islamic_titles[]" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" multiple>
                                @foreach(['Hafez','Mawlana','Mufti','Mufassir','Qari','Adib'] as $option)<option value="{{ $option }}" @selected(in_array($option, $arr('islamic_titles'), true))>{{ $option }}</option>@endforeach
                            </select>
                        </div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Islamic institution</label><input type="text" name="islamic_institution" value="{{ $v('islamic_institution') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Islamic passing year</label><input type="text" name="islamic_year" value="{{ $v('islamic_year') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                    </div>

                    <div>
                        <label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Other educational qualification</label>
                        <textarea name="other_education" rows="4" class="form-control tw-rounded-2xl tw-border-slate-200" placeholder="Institution, subject, result and passing year if applicable.">{{ $v('other_education') }}</textarea>
                    </div>
                @endif

                @if($step === 4)
                    <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2">
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Father's name *</label><input type="text" name="father_name" value="{{ $v('father_name') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required>@error('father_name')<p class="tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p>@enderror</div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Father alive? *</label><select name="father_alive" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required><option value="">Select</option>@foreach($yesNo as $option)<option value="{{ $option }}" @selected($v('father_alive') === $option)>{{ $option }}</option>@endforeach</select></div>
                        <div class="sm:tw-col-span-2"><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Father's profession details *</label><textarea name="father_profession" rows="3" class="form-control tw-rounded-2xl tw-border-slate-200" required>{{ $v('father_profession') }}</textarea><p class="tw-mt-2 tw-text-xs tw-text-slate-500">Write details, not only “businessman” or “service holder”.</p></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Mother's name *</label><input type="text" name="mother_name" value="{{ $v('mother_name') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Mother alive? *</label><select name="mother_alive" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required><option value="">Select</option>@foreach($yesNo as $option)<option value="{{ $option }}" @selected($v('mother_alive') === $option)>{{ $option }}</option>@endforeach</select></div>
                        <div class="sm:tw-col-span-2"><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Mother's profession details *</label><textarea name="mother_profession" rows="3" class="form-control tw-rounded-2xl tw-border-slate-200" required>{{ $v('mother_profession') }}</textarea></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Brothers *</label><input type="number" min="0" max="20" name="brothers" id="brothers" value="{{ $v('brothers', 0) }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Sisters *</label><input type="number" min="0" max="20" name="sisters" id="sisters" value="{{ $v('sisters', 0) }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required></div>
                        <div class="sm:tw-col-span-2 sibling-brothers"><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Brothers information</label><textarea name="brothers_info" rows="3" class="form-control tw-rounded-2xl tw-border-slate-200" placeholder="Education, marital status and profession.">{{ $v('brothers_info') }}</textarea></div>
                        <div class="sm:tw-col-span-2 sibling-sisters"><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Sisters information</label><textarea name="sisters_info" rows="3" class="form-control tw-rounded-2xl tw-border-slate-200" placeholder="Education, marital status and profession.">{{ $v('sisters_info') }}</textarea></div>
                        <div class="sm:tw-col-span-2"><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Uncles' professions</label><textarea name="uncle_profession" rows="3" class="form-control tw-rounded-2xl tw-border-slate-200">{{ $v('uncle_profession') }}</textarea></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Family financial status *</label><select name="family_financial_status" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required><option value="">Select</option>@foreach($financial as $option)<option value="{{ $option }}" @selected($v('family_financial_status') === $option)>{{ $option }}</option>@endforeach</select></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Home ownership/type *</label><input type="text" name="home_ownership" value="{{ $v('home_ownership') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="Own house/rented house details" required></div>
                        <div class="sm:tw-col-span-2"><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Family assets/business details *</label><textarea name="family_details" rows="4" class="form-control tw-rounded-2xl tw-border-slate-200" required>{{ $v('family_details') }}</textarea></div>
                        <div class="sm:tw-col-span-2"><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Family religious environment *</label><textarea name="family_religious_condition" rows="4" class="form-control tw-rounded-2xl tw-border-slate-200" required>{{ $v('family_religious_condition') }}</textarea></div>
                    </div>
                @endif

                @if($step === 5)
                    <div class="tw-grid tw-gap-3">
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Clothing outside home *</label><textarea name="clothing_style" rows="3" class="form-control tw-rounded-2xl tw-border-slate-200" required>{{ $v('clothing_style') }}</textarea></div>
                        <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2">
                            <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Niqab / veil information</label><input type="text" name="niqab_since" value="{{ $v('niqab_since') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="For bride profile"></div>
                            <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Beard information</label><input type="text" name="beard_info" value="{{ $v('beard_info') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="For groom profile"></div>
                            <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Clothes above ankles</label><input type="text" name="clothes_above_ankles" value="{{ $v('clothes_above_ankles') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="For groom profile"></div>
                            <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Fiqh *</label><select name="fiqh" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required><option value="">Select</option>@foreach(['Hanafi','Maliki','Shafi','Hanbali','Ahl-e-Hadith / Salafi'] as $option)<option value="{{ $option }}" @selected($v('fiqh') === $option)>{{ $option }}</option>@endforeach</select></div>
                        </div>
                        <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2">
                            <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Five daily prayers *</label><input type="text" name="prayers_info" value="{{ $v('prayers_info') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="Yes/No and since when" required></div>
                            <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Weekly qaza prayers *</label><input type="text" name="prayers_qaza_weekly" value="{{ $v('prayers_qaza_weekly') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required></div>
                            <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Mahram / non-mahram *</label><input type="text" name="mahram_nonmahram" value="{{ $v('mahram_nonmahram') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required></div>
                            <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Quran recitation *</label><input type="text" name="quran_recitation" value="{{ $v('quran_recitation') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required></div>
                        </div>
                        <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2">
                            <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Entertainment habit *</label><input type="text" name="watch_entertainment" value="{{ $v('watch_entertainment') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required></div>
                            <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Physical/mental disease *</label><input type="text" name="diseases" value="{{ $v('diseases') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required></div>
                            <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Religious work</label><input type="text" name="religious_work" value="{{ $v('religious_work') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="Tabligh, teaching, dawah, etc."></div>
                            <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Belief about majar *</label><input type="text" name="beliefs_on_mazar" value="{{ $v('beliefs_on_mazar') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required></div>
                        </div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">At least 3 Islamic books read *</label><input type="text" name="books_read" value="{{ $v('books_read') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">At least 3 favorite scholars *</label><input type="text" name="favorite_scholars" value="{{ $v('favorite_scholars') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required></div>
                        <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2">
                            <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Special category</label><select name="special_category[]" multiple class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5">@foreach(['Disabled','Infertile','New Muslim','Orphan','Interested in becoming second wife','Tabligh'] as $option)<option value="{{ $option }}" @selected(in_array($option, $arr('special_category'), true))>{{ $option }}</option>@endforeach</select></div>
                            <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Groom/mobile number</label><input type="text" name="groom_mobile" value="{{ $v('groom_mobile') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        </div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Hobbies / interests</label><textarea name="hobbies" rows="3" class="form-control tw-rounded-2xl tw-border-slate-200">{{ $v('hobbies') }}</textarea></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Upload photo</label><input type="file" name="groom_photo" accept="image/*" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5">@if($v('groom_photo'))<a class="tw-mt-2 tw-inline-block tw-text-sm tw-font-bold tw-text-hm-green" href="{{ asset('storage/'.$v('groom_photo')) }}" target="_blank">View uploaded photo</a>@endif</div>
                    </div>
                @endif

                @if($step === 6)
                    <div class="tw-grid tw-gap-3">
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Occupation *</label><input type="text" name="occupation" value="{{ $v('occupation') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="Software Engineer, Teacher, Business" required></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Profession details *</label><textarea name="profession_details" rows="4" class="form-control tw-rounded-2xl tw-border-slate-200" required>{{ $v('profession_details') }}</textarea></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Halal income status *</label><textarea name="profession_halal_status" rows="3" class="form-control tw-rounded-2xl tw-border-slate-200" placeholder="Explain if income source is fully halal and why." required>{{ $v('profession_halal_status') }}</textarea></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Monthly income</label><input type="number" min="0" name="monthly_income" value="{{ $v('monthly_income') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="Example: 30000"></div>
                    </div>
                @endif

                @if($step === 7)
                    <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2">
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Guardian agrees? *</label><select name="guardian_agree" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required><option value="">Select</option>@foreach(['Yes','No','Need to discuss'] as $option)<option value="{{ $option }}" @selected($v('guardian_agree') === $option)>{{ $option }}</option>@endforeach</select></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Wife in veil after marriage</label><input type="text" name="wife_in_veil" value="{{ $v('wife_in_veil') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Allow wife to study</label><input type="text" name="wife_study_allowed" value="{{ $v('wife_study_allowed') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Allow wife to work</label><input type="text" name="wife_job_allowed" value="{{ $v('wife_job_allowed') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Residence after marriage *</label><input type="text" name="residence_after_marriage" value="{{ $v('residence_after_marriage') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Expect gift from bride family? *</label><select name="expect_gift_from_bride" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required><option value="">Select</option>@foreach($yesNo as $option)<option value="{{ $option }}" @selected($v('expect_gift_from_bride') === $option)>{{ $option }}</option>@endforeach</select></div>
                        <div class="sm:tw-col-span-2"><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Marriage plan *</label><textarea name="marriage_plan" rows="4" class="form-control tw-rounded-2xl tw-border-slate-200" placeholder="Timeline, family involvement, expectations, etc." required>{{ $v('marriage_plan') }}</textarea></div>
                    </div>
                @endif

                @if($step === 8)
                    <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2">
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Expected age range *</label><input type="text" name="partner_age" value="{{ $v('partner_age') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="20-28" required></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Expected height</label><input type="text" name="partner_height" value="{{ $v('partner_height') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Complexion preference</label><select name="partner_complexion[]" multiple class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5">@foreach($complexions as $option)<option value="{{ $option }}" @selected(in_array($option, $arr('partner_complexion'), true))>{{ $option }}</option>@endforeach</select></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Marital status preference</label><select name="partner_marital_status[]" multiple class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5">@foreach(['Never Married','Married','Divorced','Widow','Widower'] as $option)<option value="{{ $option }}" @selected(in_array($option, $arr('partner_marital_status'), true))>{{ $option }}</option>@endforeach</select></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Education</label><input type="text" name="partner_education" value="{{ $v('partner_education') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Preferred district</label><input type="text" name="partner_district" value="{{ $v('partner_district') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Profession</label><input type="text" name="partner_profession" value="{{ $v('partner_profession') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Financial condition</label><select name="partner_financial_condition" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5"><option value="">Any</option>@foreach($financial as $option)<option value="{{ $option }}" @selected($v('partner_financial_condition') === $option)>{{ $option }}</option>@endforeach</select></div>
                        <div class="sm:tw-col-span-2"><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Expectations *</label><textarea name="partner_expectations" rows="5" class="form-control tw-rounded-2xl tw-border-slate-200" required>{{ $v('partner_expectations') }}</textarea></div>
                    </div>
                @endif

                @if($step === 9)
                    <div class="tw-space-y-4">
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Do your parents know about this biodata? *</label><select name="parents_know" class="form-select tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required><option value="">Select</option>@foreach($yesNo as $option)<option value="{{ $option }}" @selected($v('parents_know') === $option)>{{ $option }}</option>@endforeach</select></div>
                        @foreach([
                            'truth_testify' => 'I testify that all information is true.',
                            'responsibility' => 'I take responsibility for this submitted biodata.',
                            'privacy_consent' => 'I agree that contact details stay private and are shared only through the proper workflow.',
                        ] as $name => $label)
                            <label class="tw-flex tw-gap-3 tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-slate-50 tw-p-4 tw-text-sm tw-font-bold tw-text-slate-700">
                                <input type="checkbox" name="{{ $name }}" value="1" class="form-check-input" @checked((bool) $v($name))>
                                <span>{{ $label }}</span>
                            </label>
                            @error($name) <p class="tw-text-xs tw-font-bold tw-text-rose-600">{{ $message }}</p> @enderror
                        @endforeach
                    </div>
                @endif

                @if($step === 10)
                    <div class="tw-grid tw-gap-3 sm:tw-grid-cols-2">
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Candidate name *</label><input type="text" name="groom_name" value="{{ $v('groom_name', auth()->user()->name ?? '') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Guardian mobile *</label><input type="text" name="guardian_mobile" value="{{ $v('guardian_mobile') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" required></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Guardian relationship *</label><input type="text" name="guardian_relationship" value="{{ $v('guardian_relationship') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5" placeholder="Father / Mother / Brother" required></div>
                        <div><label class="tw-mb-2 tw-block tw-text-[13px] tw-font-black">Guardian email</label><input type="email" name="guardian_email" value="{{ $v('guardian_email') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-2.5"></div>
                    </div>
                    <div class="tw-rounded-2xl tw-border tw-border-hm-200 tw-bg-emerald-50 tw-p-5 tw-text-sm tw-leading-6 tw-text-hm-900">
                        <strong>Final step:</strong> after submit, your biodata will be marked complete and sent for review. You can still update it later from dashboard.
                    </div>
                @endif

                <div class="tw-flex tw-flex-col-reverse tw-gap-3 hm-actionbar tw-border-t tw-border-slate-100 tw-pt-4 sm:tw-flex-row sm:tw-items-center sm:tw-justify-between">
                    <div class="tw-flex tw-gap-3">
                        @if($step > 1)
                            <button type="submit" name="back" value="1" class="tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-px-5 tw-py-2.5 tw-text-[13px] tw-font-black tw-text-slate-600">← Back</button>
                        @endif
                        <button type="submit" name="draft" value="1" class="tw-rounded-2xl tw-border tw-border-amber-200 tw-bg-amber-50 tw-px-5 tw-py-2.5 tw-text-[13px] tw-font-black tw-text-amber-700">Save temporarily</button>
                    </div>
                    <button type="submit" name="{{ $step === $maxStep ? 'complete' : 'next' }}" value="1" class="tw-rounded-2xl tw-bg-gradient-to-r tw-from-hm-green tw-to-hm-500 tw-px-7 tw-py-2.5 tw-text-[13px] tw-font-black tw-text-white tw-shadow-sm">
                        {{ $step === $maxStep ? 'Submit biodata' : 'Next step' }} →
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function toggleMarriage() {
        const select = document.getElementById('marital_status');
        document.querySelectorAll('.conditional-marriage').forEach(function (el) {
            const show = select && select.value && select.value !== 'Never Married';
            el.style.display = show ? '' : 'none';
        });
    }

    function toggleSiblings() {
        const brothers = parseInt(document.getElementById('brothers')?.value || '0', 10);
        const sisters = parseInt(document.getElementById('sisters')?.value || '0', 10);
        document.querySelectorAll('.sibling-brothers').forEach(el => el.style.display = brothers > 0 ? '' : 'none');
        document.querySelectorAll('.sibling-sisters').forEach(el => el.style.display = sisters > 0 ? '' : 'none');
    }

    document.getElementById('marital_status')?.addEventListener('change', toggleMarriage);
    document.getElementById('brothers')?.addEventListener('input', toggleSiblings);
    document.getElementById('sisters')?.addEventListener('input', toggleSiblings);
    document.getElementById('sameAddress')?.addEventListener('change', function () {
        if (this.checked) {
            document.getElementById('present_address').value = document.getElementById('permanent_address').value;
        }
    });

    toggleMarriage();
    toggleSiblings();
})();
</script>
@endpush
