@extends('layouts.user-dashboard-app')

@section('title', 'Sent | HeavenlyMatch')

@section('mobile_header')
    <x-app-mobile-header title="Mailbox" :back="route('inbox')" />
@endsection

@section('content')
@php
    $interestIds = session('interest_profile_ids', []);
    $profiles = \App\Models\Biodata::with('registration')->whereIn('id', $interestIds)->latest()->get();
@endphp
<div class="tw-px-4 tw-py-4 md:tw-p-0">
    <section class="tw-rounded-[1.7rem] tw-border tw-border-slate-200 tw-bg-white tw-p-4 tw-shadow-card md:tw-p-6">
        <div class="tw-mx-auto tw-flex tw-max-w-2xl tw-rounded-full tw-bg-slate-100 tw-p-1 tw-text-center tw-text-lg tw-font-bold">
            <a href="{{ route('inbox') }}" class="tw-flex-1 tw-rounded-full tw-py-3 tw-text-slate-500 tw-no-underline">Received</a>
            <a href="{{ route('sent') }}" class="tw-flex-1 tw-rounded-full tw-bg-hm-green tw-py-3 tw-text-white tw-no-underline tw-shadow-card">Sent</a>
        </div>
    </section>
    <section class="tw-mt-4 tw-grid tw-gap-4 xl:tw-grid-cols-2">
        @forelse($profiles as $profile)
            @include('components.profile-card', ['profile' => $profile, 'index' => $loop->index])
        @empty
            <div class="tw-min-h-[52vh] tw-rounded-[1.7rem] tw-bg-white tw-p-10 tw-text-center tw-text-slate-500 xl:tw-col-span-2"><div class="tw-mx-auto tw-mt-20 tw-grid tw-h-24 tw-w-24 tw-place-items-center tw-rounded-full tw-bg-slate-100 tw-text-5xl">📨</div><h2 class="tw-mt-6 tw-text-2xl tw-font-light">No sent requests yet.</h2></div>
        @endforelse
    </section>
</div>
@endsection
