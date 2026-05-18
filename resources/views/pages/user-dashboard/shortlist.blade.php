@extends('layouts.user-dashboard-app')

@section('title', 'Shortlist | HeavenlyMatch')

@section('mobile_header')
    <x-app-mobile-header title="Shortlist" :back="route('myhome')" />
@endsection

@section('content')
@php
    $ids = session('shortlisted_profile_ids', []);
    $profiles = \App\Models\Biodata::with('registration')->whereIn('id', $ids)->latest()->get();
@endphp
<div class="tw-px-4 tw-py-4 md:tw-p-0">
    <div class="tw-mb-4 tw-rounded-[1.7rem] tw-border tw-border-slate-200 tw-bg-white tw-p-5 tw-shadow-card"><h1 class="tw-mb-1 tw-text-2xl tw-font-black">My shortlist</h1><p class="tw-mb-0 tw-text-sm tw-text-slate-500">Profiles you saved are stored in your current session until database tables are added.</p></div>
    <section class="tw-grid tw-gap-4 xl:tw-grid-cols-2">
        @forelse($profiles as $profile)
            @include('components.profile-card', ['profile' => $profile, 'index' => $loop->index])
        @empty
            <div class="tw-col-span-full tw-rounded-3xl tw-border tw-border-dashed tw-border-slate-300 tw-bg-white tw-p-10 tw-text-center"><div class="tw-text-5xl">⭐</div><h2 class="tw-mt-4 tw-text-xl tw-font-black">No shortlisted profiles</h2><p class="tw-text-slate-500">Tap Shortlist on a profile card to save it.</p><a href="{{ route('matches') }}" class="tw-rounded-2xl tw-bg-hm-green tw-px-5 tw-py-3 tw-text-sm tw-font-black tw-text-white tw-no-underline">Browse matches</a></div>
        @endforelse
    </section>
</div>
@endsection
