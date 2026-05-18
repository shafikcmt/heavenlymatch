@extends('layouts.user-dashboard-app')

@section('title', 'Search | HeavenlyMatch')

@section('mobile_header')
    <x-app-mobile-header title="Search" :tabs="[
        'basic' => ['label' => 'Basic', 'url' => route('search')],
        'education' => ['label' => 'Education', 'url' => route('search')],
        'location' => ['label' => 'Location', 'url' => route('search')],
        'religion' => ['label' => 'Religion', 'url' => route('search')],
    ]" active-tab="basic" />
@endsection

@section('content')
@php
    $query = \App\Models\Biodata::with('registration')->where('registration_id', '!=', auth()->user()->registration_id);
    if (request('looking_for')) $query->where('biodata_type', request('looking_for'));
    if (request('marital_status')) $query->where('marital_status', request('marital_status'));
    if (request('education')) $query->where('highest_qualification', 'like', '%' . request('education') . '%');
    if (request('profession')) $query->where('occupation', 'like', '%' . request('profession') . '%');
    if (request('location')) $query->where(function($q){ $q->where('present_address', 'like', '%' . request('location') . '%')->orWhere('permanent_address', 'like', '%' . request('location') . '%'); });
    $profiles = $query->latest()->take(18)->get();
@endphp

<div class="tw-px-4 tw-py-4 md:tw-p-0">
    <section class="tw-rounded-[1.7rem] tw-border tw-border-slate-200 tw-bg-white tw-p-4 tw-shadow-card md:tw-p-6">
        <div class="tw-flex tw-items-center tw-justify-between tw-gap-3">
            <div>
                <h1 class="tw-mb-1 tw-text-2xl tw-font-black">Find your match</h1>
                <p class="tw-mb-0 tw-text-sm tw-text-slate-500">Smart filters with app-style results.</p>
            </div>
            <button type="button" data-hm-toggle="#filterPanel" class="tw-rounded-full tw-bg-hm-green tw-px-4 tw-py-2.5 tw-text-sm tw-font-black tw-text-white md:tw-hidden"><i class="bi bi-funnel"></i> Filter</button>
        </div>

        <form id="filterPanel" method="GET" action="{{ route('search') }}" class="tw-mt-5 tw-grid tw-gap-3 md:tw-grid-cols-4">
            <div><label class="tw-mb-1 tw-block tw-text-xs tw-font-black tw-text-slate-500">Looking for</label><select name="looking_for" class="hm-clean-select"><option value="">Any</option><option value="bride" @selected(request('looking_for')==='bride')>Bride</option><option value="groom" @selected(request('looking_for')==='groom')>Groom</option></select></div>
            <div><label class="tw-mb-1 tw-block tw-text-xs tw-font-black tw-text-slate-500">Age from</label><select name="age_from" class="hm-clean-select"><option value="">Any</option>@for($i=18;$i<=65;$i++)<option value="{{ $i }}" @selected(request('age_from') == $i)>{{ $i }}</option>@endfor</select></div>
            <div><label class="tw-mb-1 tw-block tw-text-xs tw-font-black tw-text-slate-500">Age to</label><select name="age_to" class="hm-clean-select"><option value="">Any</option>@for($i=18;$i<=65;$i++)<option value="{{ $i }}" @selected(request('age_to') == $i)>{{ $i }}</option>@endfor</select></div>
            <div><label class="tw-mb-1 tw-block tw-text-xs tw-font-black tw-text-slate-500">Marital status</label><select name="marital_status" class="hm-clean-select"><option value="">Any</option>@foreach(['Never Married','Married','Divorced','Widow','Widower'] as $option)<option value="{{ $option }}" @selected(request('marital_status')===$option)>{{ $option }}</option>@endforeach</select></div>
            <div><label class="tw-mb-1 tw-block tw-text-xs tw-font-black tw-text-slate-500">Education</label><input type="text" name="education" value="{{ request('education') }}" class="hm-clean-input" placeholder="Bachelor, Hifz"></div>
            <div><label class="tw-mb-1 tw-block tw-text-xs tw-font-black tw-text-slate-500">Profession</label><input type="text" name="profession" value="{{ request('profession') }}" class="hm-clean-input" placeholder="Engineer, Teacher"></div>
            <div class="md:tw-col-span-2"><label class="tw-mb-1 tw-block tw-text-xs tw-font-black tw-text-slate-500">Location</label><input type="text" name="location" value="{{ request('location') }}" class="hm-clean-input" placeholder="Dhaka, Chattogram, Sylhet"></div>
            <div class="tw-flex tw-gap-2 md:tw-col-span-4"><button type="submit" class="tw-rounded-2xl tw-bg-gradient-to-r tw-from-hm-green tw-to-hm-500 tw-px-6 tw-py-3 tw-text-sm tw-font-black tw-text-white"><i class="bi bi-search"></i> Search profiles</button><a href="{{ route('search') }}" class="tw-rounded-2xl tw-border tw-border-slate-200 tw-px-6 tw-py-3 tw-text-sm tw-font-black tw-text-slate-600 tw-no-underline">Reset</a></div>
        </form>
    </section>

    <div class="tw-mt-5 tw-flex tw-items-center tw-justify-between"><h2 class="tw-mb-0 tw-text-xl tw-font-black">{{ $profiles->count() }} results</h2><a href="{{ route('matches') }}" class="tw-text-sm tw-font-black tw-text-hm-600 tw-no-underline">Suggested matches</a></div>
    <section class="tw-mt-3 tw-grid tw-gap-4 xl:tw-grid-cols-2">
        @forelse($profiles as $profile)
            @include('components.profile-card', ['profile' => $profile, 'index' => $loop->index])
        @empty
            <div class="tw-col-span-full tw-rounded-3xl tw-border tw-border-dashed tw-border-slate-300 tw-bg-white tw-p-10 tw-text-center"><div class="tw-text-5xl">🧭</div><h2 class="tw-mt-4 tw-text-xl tw-font-black">No matched profiles</h2><p class="tw-mb-0 tw-text-slate-500">Try widening your filters.</p></div>
        @endforelse
    </section>
</div>
@endsection
