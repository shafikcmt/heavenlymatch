@extends('layouts.user-dashboard-app')

@section('title', 'Mailbox | HeavenlyMatch')

@section('mobile_header')
    <x-app-mobile-header title="Mailbox" :back="route('myhome')" />
@endsection

@section('content')
@php
    $interestIds = session('interest_profile_ids', []);
    $profiles = \App\Models\Biodata::with('registration')->whereIn('id', $interestIds)->latest()->get();
@endphp
<div class="tw-px-4 tw-py-4 md:tw-p-0">
    <section class="tw-rounded-[1.7rem] tw-border tw-border-slate-200 tw-bg-white tw-p-4 tw-shadow-card md:tw-p-6">
        <div class="tw-mx-auto tw-flex tw-max-w-2xl tw-rounded-full tw-bg-slate-100 tw-p-1 tw-text-center tw-text-lg tw-font-bold">
            <a href="{{ route('inbox') }}" class="tw-flex-1 tw-rounded-full tw-bg-hm-green tw-py-3 tw-text-white tw-no-underline tw-shadow-card">Received</a>
            <a href="{{ route('sent') }}" class="tw-flex-1 tw-rounded-full tw-py-3 tw-text-slate-500 tw-no-underline">Sent</a>
        </div>
        <div class="hm-pill-scroll tw-mt-5 tw-flex tw-gap-8 tw-overflow-x-auto tw-border-b tw-border-slate-200 tw-text-sm tw-font-black tw-uppercase">
            @foreach(['Pending (0)','Accepted ('.max(1, $profiles->count()).')','Declined (0)','Replied (0)'] as $i => $tab)
                <button class="tw-relative tw-shrink-0 tw-border-0 tw-bg-transparent tw-pb-3 {{ $i===1 ? 'tw-text-hm-green' : 'tw-text-slate-400' }}" type="button">{{ $tab }} @if($i===1)<span class="tw-absolute tw-bottom-0 tw-left-0 tw-right-0 tw-h-1 tw-bg-hm-green"></span>@endif</button>
            @endforeach
        </div>
    </section>

    <section class="tw-mt-4 tw-grid tw-gap-4 xl:tw-grid-cols-2">
        @forelse($profiles as $profile)
            @include('components.profile-card', ['profile' => $profile, 'index' => $loop->index])
        @empty
            <div class="tw-min-h-[52vh] tw-rounded-[1.7rem] tw-bg-[#e3e3e3] tw-p-10 tw-text-center tw-text-slate-500 xl:tw-col-span-2">
                <div class="tw-mx-auto tw-mt-20 tw-grid tw-h-24 tw-w-24 tw-place-items-center tw-rounded-full tw-bg-white/40 tw-text-5xl">⚠</div>
                <h2 class="tw-mt-6 tw-text-2xl tw-font-light">You do not have any pending messages.</h2>
                <p class="tw-mb-0 tw-mt-2">Send interest to a profile and it will appear here.</p>
            </div>
        @endforelse
    </section>
</div>
@endsection
