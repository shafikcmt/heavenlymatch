@extends('layouts.user-dashboard-app')

@section('title', 'Profile Detail | HeavenlyMatch')

@section('mobile_header')
@php
    $detailTitle = ($biodata?->registration_id ?? $registration?->registration_id ?? 'Profile');
    $detailSubtitle = $biodata?->groom_name ?: $registration?->name;
@endphp
<header class="hm-mobile-header hm-mobile-only tw-sticky tw-top-0 tw-z-40">
    <div class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3">
        <a href="{{ url()->previous() ?: route('matches') }}" class="tw-grid tw-h-10 tw-w-10 tw-place-items-center tw-rounded-full tw-text-white tw-no-underline"><i class="bi bi-arrow-left tw-text-2xl"></i></a>
        @if($biodata)
            @include('components.profile-photo', ['profile' => $biodata, 'index' => $biodata->id, 'class' => 'tw-h-12 tw-w-12 tw-rounded-full tw-ring-2 tw-ring-white/40'])
        @endif
        <div class="tw-min-w-0">
            <h1 class="tw-mb-0 tw-truncate tw-text-xl tw-font-bold">{{ $detailTitle }}</h1>
            <p class="tw-mb-0 tw-truncate tw-text-sm tw-text-white/80">{{ $detailSubtitle }}</p>
        </div>
    </div>
</header>
@endsection

@section('content')
@php
    $biodata = $biodata ?? auth()->user()?->biodata;
    $registration = $registration ?? auth()->user();
    $name = $biodata?->groom_name ?: $registration?->name ?: 'HeavenlyMatch Member';
    $age = $biodata?->birth_date ? \Carbon\Carbon::parse($biodata->birth_date)->age : null;
    $score = $biodata ? 80 + (($biodata->id * 3) % 16) : 83;
    $photoIndex = $biodata?->id ?? 1;
    $sections = [
        'Basics' => [
            'Name' => $name,
            'Age' => $age ? $age . ' yrs' : null,
            'Profile created for' => $registration?->profile_for,
            'Gender' => $registration?->gender,
            'Height' => $biodata?->height,
            'Weight' => $biodata?->weight,
            'Marital status' => $biodata?->marital_status,
            'Complexion' => $biodata?->complexion,
            'Blood group' => $biodata?->blood_group,
            'Nationality' => $biodata?->nationality,
        ],
        'Religious' => [
            'Prayer' => $biodata?->prayers_info,
            'Qaza weekly' => $biodata?->prayers_qaza_weekly,
            'Mahram/non-mahram' => $biodata?->mahram_nonmahram,
            'Quran recitation' => $biodata?->quran_recitation,
            'Fiqh' => $biodata?->fiqh,
            'Books read' => $biodata?->books_read,
            'Favourite scholars' => $biodata?->favorite_scholars,
        ],
        'Education & Work' => [
            'Education method' => $biodata?->education_method,
            'Education' => $biodata?->highest_qualification,
            'Subject' => $biodata?->graduation_subject ?: $biodata?->postgraduation_subject,
            'Institution' => $biodata?->graduation_institution ?: $biodata?->postgraduation_institution,
            'Occupation' => $biodata?->occupation,
            'Profession detail' => $biodata?->profession_details,
            'Monthly income' => $biodata?->monthly_income ? 'BDT ' . number_format($biodata->monthly_income) : null,
        ],
        'Location' => [
            'Present address' => $biodata?->present_address,
            'Permanent address' => $biodata?->permanent_address,
            'Grew up' => $biodata?->grew_up,
        ],
        'Family' => [
            'Father profession' => $biodata?->father_profession,
            'Mother profession' => $biodata?->mother_profession,
            'Brothers' => $biodata?->brothers,
            'Sisters' => $biodata?->sisters,
            'Financial status' => $biodata?->family_financial_status,
            'Family details' => $biodata?->family_details,
        ],
        'Partner' => [
            'Age preference' => $biodata?->partner_age,
            'Height preference' => $biodata?->partner_height,
            'Education preference' => $biodata?->partner_education,
            'Marital status' => $biodata?->partner_marital_status,
            'Profession' => $biodata?->partner_profession,
            'District' => $biodata?->partner_district,
            'Expectations' => $biodata?->partner_expectations,
        ],
    ];
@endphp

@if(!$biodata)
    <div class="tw-p-4 md:tw-p-0"><div class="tw-rounded-3xl tw-border tw-border-dashed tw-border-slate-300 tw-bg-white tw-p-10 tw-text-center"><h1 class="tw-text-2xl tw-font-black">No biodata found</h1><p class="tw-text-slate-500">Create your biodata first.</p><a href="{{ route('biodata.create') }}" class="tw-rounded-2xl tw-bg-hm-green tw-px-5 tw-py-3 tw-text-sm tw-font-black tw-text-white tw-no-underline">Create biodata</a></div></div>
@else
<div class="tw-bg-white md:tw-rounded-[1.7rem] md:tw-border md:tw-border-slate-200 md:tw-shadow-card">
    <section class="tw-relative tw-overflow-hidden md:tw-rounded-t-[1.7rem]">
        <div class="tw-h-[280px] tw-bg-slate-200 md:tw-h-[340px]">
            @include('components.profile-photo', ['profile' => $biodata, 'index' => $photoIndex, 'class' => 'tw-h-full tw-w-full tw-rounded-none'])
        </div>
        <a href="{{ route('matches') }}" class="hm-desktop-only tw-absolute tw-left-5 tw-top-5 tw-grid tw-h-11 tw-w-11 tw-place-items-center tw-rounded-full tw-bg-black/35 tw-text-white tw-no-underline tw-backdrop-blur"><i class="bi bi-arrow-left tw-text-2xl"></i></a>
        <button class="tw-absolute tw-right-4 tw-top-1/2 tw-grid tw-h-14 tw-w-14 -tw-translate-y-1/2 tw-place-items-center tw-rounded-l-full tw-bg-slate-900/80 tw-text-white tw-shadow-soft" type="button"><i class="bi bi-chevron-right tw-text-2xl"></i></button>
    </section>

    <section class="tw-p-5 md:tw-p-7">
        <div class="tw-flex tw-items-start tw-justify-between tw-gap-5">
            <div>
                <div class="tw-mb-1 tw-flex tw-items-center tw-gap-2"><span class="tw-text-sm tw-font-black tw-uppercase tw-text-emerald-500">Platinum</span><span class="tw-text-sm tw-font-semibold tw-text-slate-500">{{ $biodata->registration_id }}</span></div>
                <h1 class="tw-mb-2 tw-text-4xl tw-font-black tw-leading-tight tw-text-slate-950 md:tw-text-5xl">{{ $name }}</h1>
                <p class="tw-mb-2 tw-text-xl tw-leading-8 tw-text-slate-600">{{ $age ? $age . ' yrs,' : '' }} {{ $biodata->height ?: 'Height N/A' }}, {{ $biodata->present_address ?: 'Bangladesh' }}</p>
                <form method="POST" action="{{ route('profiles.chat', $biodata->id) }}">@csrf<button class="tw-border-0 tw-bg-transparent tw-p-0 tw-text-lg tw-font-bold tw-text-rose-600" type="submit"><i class="bi bi-chat-square-dots-fill"></i> Chat Now</button></form>
            </div>
            <div class="hm-match-score tw-grid tw-h-24 tw-w-24 tw-shrink-0 tw-place-items-center tw-rounded-full" style="--score: {{ $score }}%;"><div class="tw-grid tw-h-[72px] tw-w-[72px] tw-place-items-center tw-rounded-full tw-bg-white tw-text-center tw-text-hm-green"><strong class="tw-text-2xl tw-leading-none">{{ $score }}%</strong><span class="tw-text-[11px] tw-font-black tw-leading-none">Match<br>Score</span></div></div>
        </div>

        <div class="tw-mt-8 tw-grid tw-grid-cols-3 tw-border-y tw-border-slate-100 tw-py-5 tw-text-center">
            <form method="POST" action="{{ route('profiles.shortlist', $biodata->id) }}">@csrf<button class="tw-border-0 tw-bg-transparent tw-text-slate-700" type="submit"><span class="tw-mx-auto tw-grid tw-h-14 tw-w-14 tw-place-items-center tw-rounded-full tw-bg-rose-50 tw-text-rose-600"><i class="bi bi-star-fill tw-text-2xl"></i></span><span class="tw-mt-2 tw-block tw-text-lg">Shortlist</span></button></form>
            <a href="tel:{{ $biodata->guardian_mobile }}" class="tw-text-slate-700 tw-no-underline"><span class="tw-mx-auto tw-grid tw-h-14 tw-w-14 tw-place-items-center tw-rounded-full tw-bg-slate-50 tw-text-rose-600"><i class="bi bi-telephone-fill tw-text-2xl"></i></span><span class="tw-mt-2 tw-block tw-text-lg">Call</span></a>
            <button class="tw-border-0 tw-bg-transparent tw-text-slate-700" type="button"><span class="tw-mx-auto tw-grid tw-h-14 tw-w-14 tw-place-items-center tw-rounded-full tw-bg-slate-50 tw-text-rose-600"><i class="bi bi-three-dots tw-text-2xl"></i></span><span class="tw-mt-2 tw-block tw-text-lg">More</span></button>
        </div>

        <nav class="hm-section-tabs tw-sticky tw-top-0 tw-z-30 -tw-mx-5 tw-mt-5 tw-flex tw-gap-4 tw-overflow-x-auto tw-border-b tw-border-slate-100 tw-bg-white tw-px-5 tw-py-4 md:-tw-mx-7 md:tw-top-16 md:tw-px-7">
            @foreach(['Basics'=>'bi-person','Religious'=>'bi-journal-richtext','Contact'=>'bi-telephone','Family'=>'bi-people','Partner'=>'bi-heart'] as $tab => $icon)
                <a href="#section-{{ \Illuminate\Support\Str::slug($tab) }}" class="tw-flex tw-shrink-0 tw-flex-col tw-items-center tw-gap-1 tw-text-slate-500 tw-no-underline hover:tw-text-rose-600"><span class="tw-grid tw-h-12 tw-w-12 tw-place-items-center tw-rounded-full tw-border tw-border-slate-300"><i class="bi {{ $icon }} tw-text-xl"></i></span><span class="tw-text-sm tw-font-bold">{{ $tab }}</span></a>
            @endforeach
        </nav>

        <div class="tw-space-y-8 tw-py-7">
            @foreach($sections as $title => $rows)
                <section id="section-{{ \Illuminate\Support\Str::slug($title) }}">
                    <h2 class="tw-mb-4 tw-text-3xl tw-font-light tw-text-slate-950 md:tw-text-4xl">{{ $title === 'Basics' ? 'A few lines about ' . explode(' ', $name)[0] : $title }}</h2>
                    <div class="tw-rounded-3xl tw-bg-white">
                        @foreach($rows as $label => $value)
                            @if(filled($value))
                                <div class="hm-detail-table-row"><div class="hm-label">{{ $label }}</div><div>:</div><div class="hm-value">{{ $value }}</div></div>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </section>
</div>

<div class="hm-mobile-only tw-fixed tw-bottom-[70px] tw-left-0 tw-right-0 tw-z-40 tw-border-t tw-border-slate-200 tw-bg-white tw-px-5 tw-py-3">
    <div class="tw-flex tw-items-center tw-justify-between tw-gap-3"><span class="tw-text-2xl tw-font-light">Like {{ $biodata->biodata_type === 'bride' ? 'Her' : 'Him' }}?</span><a href="{{ route('matches') }}" class="tw-rounded-full tw-border tw-border-slate-300 tw-px-7 tw-py-2.5 tw-text-lg tw-text-slate-500 tw-no-underline">× Skip</a><form method="POST" action="{{ route('profiles.interest', $biodata->id) }}">@csrf<button type="submit" class="tw-rounded-full tw-border-0 tw-bg-rose-600 tw-px-8 tw-py-2.5 tw-text-lg tw-font-bold tw-text-white">✓ Yes</button></form></div>
</div>
@endif
@endsection
