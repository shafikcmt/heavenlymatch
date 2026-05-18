@extends('layouts.user-dashboard-app')

@section('title', 'Home | HeavenlyMatch')

@section('mobile_header')
    <x-app-mobile-header title="Home" :tabs="[
        'dashboard' => ['label' => 'Dashboard', 'url' => route('myhome')],
        'joined' => ['label' => 'Just Joined', 'url' => route('matches')],
        'matches' => ['label' => 'Matches', 'url' => route('matches')],
        'premium' => ['label' => 'Premium', 'url' => route('upgrade')],
    ]" active-tab="dashboard" />
@endsection

@section('content')
@php
    $user = auth()->user();
    $biodata = $user?->biodata;
    $required = ['biodata_type','marital_status','birth_date','height','weight','complexion','blood_group','permanent_address','present_address','education_method','highest_qualification','father_name','mother_name','clothing_style','prayers_info','occupation','profession_details','guardian_agree','partner_age','partner_expectations','parents_know','groom_name','guardian_mobile'];
    $filled = $biodata ? collect($required)->filter(fn($field) => filled($biodata->{$field} ?? null))->count() : 0;
    $percent = $biodata ? (int) round(($filled / count($required)) * 100) : 0;
    $profiles = \App\Models\Biodata::with('registration')->where('registration_id', '!=', $user->registration_id)->latest()->take(4)->get();
@endphp

<div class="tw-px-4 tw-py-4 md:tw-p-0">
    <section class="tw-overflow-hidden tw-rounded-[1.7rem] tw-bg-gradient-to-br tw-from-hm-green tw-via-[#0c8a6d] tw-to-hm-500 tw-p-5 tw-text-white tw-shadow-card md:tw-p-7">
        <div class="tw-flex tw-flex-col tw-gap-5 md:tw-flex-row md:tw-items-center md:tw-justify-between">
            <div class="tw-flex tw-items-center tw-gap-4">
                @if($biodata)
                    @include('components.profile-photo', ['profile' => $biodata, 'index' => 2, 'class' => 'tw-h-20 tw-w-20 tw-rounded-[1.7rem] tw-ring-4 tw-ring-white/20'])
                @else
                    <div class="tw-grid tw-h-20 tw-w-20 tw-place-items-center tw-rounded-[1.7rem] tw-bg-white/15 tw-text-3xl tw-font-black">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                @endif
                <div>
                    <p class="tw-mb-1 tw-text-xs tw-font-bold tw-uppercase tw-tracking-widest tw-text-white/70">Welcome back</p>
                    <h1 class="tw-mb-1 tw-text-2xl tw-font-black md:tw-text-4xl">{{ $user->name }}</h1>
                    <p class="tw-mb-0 tw-text-sm tw-text-white/80">{{ $user->registration_id }} · Membership: Free</p>
                </div>
            </div>
            <div class="tw-min-w-[220px] tw-rounded-3xl tw-bg-white/14 tw-p-4 tw-backdrop-blur">
                <div class="tw-flex tw-items-center tw-justify-between tw-text-sm tw-font-bold"><span>Profile score</span><span>{{ $percent }}%</span></div>
                <div class="tw-mt-3 tw-h-2.5 tw-overflow-hidden tw-rounded-full tw-bg-white/30"><div class="tw-h-full tw-rounded-full tw-bg-white" style="width: {{ $percent }}%"></div></div>
                <a href="{{ route('biodata.create') }}" class="tw-mt-4 tw-block tw-rounded-2xl tw-bg-white tw-px-4 tw-py-2.5 tw-text-center tw-text-sm tw-font-black tw-text-hm-green tw-no-underline">{{ $biodata ? 'Edit profile' : 'Create profile' }}</a>
            </div>
        </div>
    </section>

    <section class="tw-mt-5 tw-grid tw-gap-3 md:tw-grid-cols-4">
        @foreach([
            ['route'=>'matches','icon'=>'bi-people-fill','label'=>'Matches','value'=>'4794'],
            ['route'=>'inbox','icon'=>'bi-envelope-fill','label'=>'Mailbox','value'=>'1'],
            ['route'=>'shortlist','icon'=>'bi-star-fill','label'=>'Shortlist','value'=>count(session('shortlisted_profile_ids', []))],
            ['route'=>'upgrade','icon'=>'bi-gem','label'=>'Upgrade','value'=>'Pro'],
        ] as $card)
            <a href="{{ route($card['route']) }}" class="hm-action-card tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-p-4 tw-text-slate-900 tw-no-underline tw-shadow-card">
                <div class="tw-flex tw-items-center tw-justify-between">
                    <span class="tw-grid tw-h-12 tw-w-12 tw-place-items-center tw-rounded-2xl tw-bg-emerald-50 tw-text-hm-green"><i class="bi {{ $card['icon'] }} tw-text-xl"></i></span>
                    <span class="tw-text-lg tw-font-black">{{ $card['value'] }}</span>
                </div>
                <div class="tw-mt-3 tw-text-sm tw-font-black">{{ $card['label'] }}</div>
            </a>
        @endforeach
    </section>

    <section class="tw-mt-6">
        <div class="tw-mb-3 tw-flex tw-items-center tw-justify-between">
            <div>
                <h2 class="tw-mb-0 tw-text-xl tw-font-black">Members matching your preferences</h2>
                <p class="tw-mb-0 tw-text-sm tw-text-slate-500">Clean mobile-first cards with shortlist, chat and interest actions.</p>
            </div>
            <a href="{{ route('matches') }}" class="tw-rounded-full tw-border tw-border-hm-200 tw-px-4 tw-py-2 tw-text-sm tw-font-black tw-text-hm-600 tw-no-underline">View all</a>
        </div>
        <div class="tw-grid tw-gap-4 xl:tw-grid-cols-2">
            @forelse($profiles as $profile)
                @include('components.profile-card', ['profile' => $profile, 'index' => $loop->index])
            @empty
                <div class="tw-rounded-3xl tw-border tw-border-dashed tw-border-slate-300 tw-bg-white tw-p-8 tw-text-center">
                    <div class="tw-text-5xl">🔎</div>
                    <h3 class="tw-mt-3 tw-text-xl tw-font-black">No profiles yet</h3>
                    <p class="tw-mb-0 tw-text-slate-500">Run the dummy seeder to see beautiful profile cards.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
