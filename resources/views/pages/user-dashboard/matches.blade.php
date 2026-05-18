@extends('layouts.user-dashboard-app')

@section('title', 'Matches | HeavenlyMatch')

@section('mobile_header')
    <x-app-mobile-header title="Home" :tabs="[
        'dashboard' => ['label' => 'Dashboard', 'url' => route('myhome')],
        'joined' => ['label' => 'Just Joined', 'url' => route('matches')],
        'matches' => ['label' => 'Matches', 'url' => route('matches')],
        'premium' => ['label' => 'Premium', 'url' => route('upgrade')],
        'viewed' => ['label' => 'Viewed', 'url' => route('matches')],
    ]" active-tab="matches" />
@endsection

@section('content')
@php
    $query = \App\Models\Biodata::with('registration')->where('registration_id', '!=', auth()->user()->registration_id);
    $profiles = $query->latest()->take(18)->get();
@endphp

<div class="tw-px-4 tw-py-4 md:tw-p-0">
    <section class="hm-desktop-only tw-mb-5 tw-rounded-[1.7rem] tw-border tw-border-slate-200 tw-bg-white tw-p-6 tw-shadow-card">
        <div class="tw-flex tw-items-center tw-justify-between tw-gap-5">
            <div>
                <h1 class="tw-mb-1 tw-text-3xl tw-font-black">{{ max($profiles->count(), 4794) }} members match your preferences</h1>
                <p class="tw-mb-0 tw-text-sm tw-text-slate-500">Browse clean profile cards inspired by premium matrimony apps, customized for HeavenlyMatch.</p>
            </div>
            <a href="{{ route('search') }}" class="tw-rounded-full tw-border tw-border-hm-200 tw-px-5 tw-py-2.5 tw-text-sm tw-font-black tw-text-hm-600 tw-no-underline"><i class="bi bi-sliders"></i> Edit filters</a>
        </div>
    </section>

    <div class="hm-mobile-only tw-mb-4 tw-flex tw-items-start tw-justify-between tw-gap-3">
        <h1 class="tw-mb-0 tw-text-[26px] tw-font-black tw-leading-tight">{{ max($profiles->count(), 4794) }} members match your preferences</h1>
        <a href="{{ route('search') }}" class="tw-shrink-0 tw-rounded-full tw-border tw-border-rose-300 tw-px-4 tw-py-2 tw-text-sm tw-font-bold tw-text-rose-600 tw-no-underline"><i class="bi bi-pencil"></i> Edit</a>
    </div>

    <div class="tw-grid tw-gap-5 lg:tw-grid-cols-[280px_minmax(0,1fr)]">
        <aside class="hm-desktop-only tw-rounded-[1.7rem] tw-border tw-border-slate-200 tw-bg-white tw-p-4 tw-shadow-card">
            <div class="tw-flex tw-items-center tw-justify-between">
                <h2 class="tw-mb-0 tw-text-base tw-font-black">Filter profiles</h2>
                <a href="{{ route('matches') }}" class="tw-text-xs tw-font-black tw-text-hm-600 tw-no-underline">Reset</a>
            </div>
            <form action="{{ route('search') }}" method="GET" class="tw-mt-4 tw-space-y-4">
                <div><label class="tw-mb-1 tw-block tw-text-xs tw-font-black tw-text-slate-500">Looking for</label><select name="looking_for" class="hm-clean-select"><option value="">Any</option><option value="bride">Bride</option><option value="groom">Groom</option></select></div>
                <div class="tw-grid tw-grid-cols-2 tw-gap-2"><div><label class="tw-mb-1 tw-block tw-text-xs tw-font-black tw-text-slate-500">Age from</label><select name="age_from" class="hm-clean-select">@for($i=18;$i<=60;$i++)<option value="{{ $i }}">{{ $i }}</option>@endfor</select></div><div><label class="tw-mb-1 tw-block tw-text-xs tw-font-black tw-text-slate-500">Age to</label><select name="age_to" class="hm-clean-select">@for($i=25;$i<=70;$i++)<option value="{{ $i }}">{{ $i }}</option>@endfor</select></div></div>
                <div><label class="tw-mb-1 tw-block tw-text-xs tw-font-black tw-text-slate-500">Location</label><input name="location" class="hm-clean-input" placeholder="Dhaka, Sylhet"></div>
                <button class="tw-w-full tw-rounded-2xl tw-bg-hm-green tw-py-3 tw-text-sm tw-font-black tw-text-white" type="submit"><i class="bi bi-search"></i> Search</button>
            </form>
        </aside>

        <section class="tw-grid tw-gap-4 xl:tw-grid-cols-2">
            @forelse($profiles as $profile)
                @include('components.profile-card', ['profile' => $profile, 'index' => $loop->index])
            @empty
                <div class="tw-col-span-full tw-rounded-3xl tw-border tw-border-dashed tw-border-slate-300 tw-bg-white tw-p-10 tw-text-center">
                    <div class="tw-text-5xl">🔎</div>
                    <h2 class="tw-mt-4 tw-text-xl tw-font-black">No profiles found</h2>
                    <p class="tw-mb-0 tw-text-slate-500">Run the dummy seeder or complete more biodata profiles.</p>
                </div>
            @endforelse
        </section>
    </div>
</div>
@endsection
