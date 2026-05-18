@extends('layouts.user-dashboard-app')

@section('title', 'Upgrade | HeavenlyMatch')

@section('mobile_header')
    <x-app-mobile-header title="Upgrade" :back="route('myhome')" />
@endsection

@section('content')
@php
    $plans = [
        ['name' => 'Gold', 'price' => 'BDT 3900', 'views' => '40 verified mobile numbers', 'messages' => 'Unlimited messages and chat online', 'popular' => false],
        ['name' => 'Diamond', 'price' => 'BDT 6900', 'views' => '100 verified mobile numbers', 'messages' => 'Priority messages and family support', 'popular' => true],
        ['name' => 'Platinum', 'price' => 'BDT 9900', 'views' => 'Unlimited verified contacts', 'messages' => 'Profile boost and premium placement', 'popular' => false],
    ];
@endphp
<div class="tw-px-4 tw-py-5 md:tw-p-0">
    <section class="tw-rounded-[1.7rem] tw-bg-gradient-to-br tw-from-hm-green tw-to-hm-500 tw-p-6 tw-text-white tw-shadow-card md:tw-p-8">
        <h1 class="tw-mb-2 tw-text-3xl tw-font-black">Upgrade membership</h1>
        <p class="tw-mb-0 tw-max-w-2xl tw-text-white/80">Unlock contact numbers, send unlimited interests, chat online and speed up your partner search.</p>
    </section>

    <div class="hm-pill-scroll tw-my-6 tw-flex tw-gap-3 tw-overflow-x-auto">
        @foreach(['3 Months','6 Months','1 Year'] as $i => $duration)
            <button class="tw-shrink-0 tw-rounded-full tw-border tw-border-hm-green tw-px-8 tw-py-3 tw-text-lg tw-font-bold {{ $i === 0 ? 'tw-bg-hm-green tw-text-white' : 'tw-bg-white tw-text-hm-green' }}" type="button">{{ $duration }}</button>
        @endforeach
    </div>

    <div class="hm-pill-scroll tw-flex tw-gap-5 tw-overflow-x-auto tw-pb-4 md:tw-grid md:tw-grid-cols-3 md:tw-overflow-visible">
        @foreach($plans as $plan)
            <article class="tw-min-w-[300px] tw-rounded-[1.8rem] tw-border {{ $plan['popular'] ? 'tw-border-hm-300 tw-ring-2 tw-ring-hm-100' : 'tw-border-slate-200' }} tw-bg-white tw-p-6 tw-shadow-card">
                <div class="tw-flex tw-items-start tw-justify-between tw-gap-3"><h2 class="tw-mb-0 tw-text-2xl tw-font-black">{{ $plan['name'] }}</h2><strong class="tw-text-2xl tw-font-black">{{ $plan['price'] }}</strong></div>
                @if($plan['popular'])<span class="tw-mt-3 tw-inline-flex tw-rounded-full tw-bg-rose-50 tw-px-3 tw-py-1 tw-text-xs tw-font-black tw-text-rose-600">Most popular</span>@endif
                <div class="tw-mt-8 tw-space-y-6 tw-text-lg tw-leading-7">
                    <div class="tw-flex tw-gap-4"><i class="bi bi-envelope tw-text-2xl tw-text-slate-400"></i><span>{{ $plan['messages'] }}</span></div>
                    <div class="tw-flex tw-gap-4"><i class="bi bi-phone tw-text-2xl tw-text-slate-400"></i><span>{{ $plan['views'] }}</span></div>
                </div>
                <div class="tw-my-8 tw-border-t tw-border-slate-200"></div>
                <p class="tw-mb-4 tw-text-lg">You have to pay <strong>{{ $plan['price'] }}</strong></p>
                <button type="button" class="tw-w-full tw-rounded-full tw-border-0 tw-bg-hm-green tw-py-3 tw-text-lg tw-font-black tw-text-white tw-shadow-card">Pay Now</button>
            </article>
        @endforeach
    </div>

    <section class="tw-mt-8 tw-text-center">
        <div class="tw-mx-auto tw-grid tw-h-28 tw-w-28 tw-place-items-center tw-rounded-full tw-bg-rose-50 tw-text-6xl">👑</div>
        <h2 class="tw-mt-5 tw-text-3xl tw-font-light">Why premium membership?</h2>
        <div class="tw-mt-6 tw-grid tw-gap-4 md:tw-grid-cols-3">
            @foreach([
                ['bi-telephone', 'View verified contacts'], ['bi-chat-dots', 'Chat with matches'], ['bi-graph-up-arrow', 'Boost profile visibility']
            ] as [$icon,$text])
                <div class="tw-rounded-3xl tw-bg-white tw-p-5 tw-shadow-card"><i class="bi {{ $icon }} tw-text-3xl tw-text-rose-500"></i><h3 class="tw-mt-3 tw-text-base tw-font-black">{{ $text }}</h3></div>
            @endforeach
        </div>
    </section>
</div>
@endsection
