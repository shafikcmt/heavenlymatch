@php
    $user = auth()->user();
    $biodata = $user?->biodata;
    $required = ['biodata_type','marital_status','birth_date','height','weight','permanent_address','present_address','education_method','highest_qualification','father_name','mother_name','occupation','partner_age','groom_name','guardian_mobile'];
    $filled = $biodata ? collect($required)->filter(fn($field) => filled($biodata->{$field} ?? null))->count() : 0;
    $percent = $biodata ? (int) round(($filled / count($required)) * 100) : 0;
    $items = [
        ['route' => 'myhome', 'icon' => 'bi-house-door-fill', 'label' => 'Home'],
        ['route' => 'matches', 'icon' => 'bi-people-fill', 'label' => 'Matches'],
        ['route' => 'search', 'icon' => 'bi-search', 'label' => 'Search'],
        ['route' => 'inbox', 'icon' => 'bi-envelope-fill', 'label' => 'Mailbox'],
        ['route' => 'shortlist', 'icon' => 'bi-star-fill', 'label' => 'Shortlist'],
        ['route' => 'biodata.create', 'icon' => 'bi-pencil-square', 'label' => 'Edit Biodata'],
        ['route' => 'profiledetail', 'icon' => 'bi-person-fill', 'label' => 'Profile'],
        ['route' => 'upgrade', 'icon' => 'bi-gem', 'label' => 'Upgrade Plan'],
    ];
@endphp
<div class="tw-sticky tw-top-20 tw-overflow-hidden tw-rounded-[1.7rem] tw-border tw-border-slate-200 tw-bg-white tw-p-4 tw-shadow-card">
    <div class="tw-flex tw-items-center tw-gap-3">
        @if($biodata)
            @include('components.profile-photo', ['profile' => $biodata, 'index' => 2, 'class' => 'tw-h-16 tw-w-16 tw-rounded-2xl'])
        @else
            <div class="tw-grid tw-h-16 tw-w-16 tw-place-items-center tw-rounded-2xl tw-bg-gradient-to-br tw-from-hm-green tw-to-hm-500 tw-text-xl tw-font-black tw-text-white">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
        @endif
        <div class="tw-min-w-0">
            <div class="tw-truncate tw-text-base tw-font-black tw-text-slate-950">{{ $user->name ?? 'User' }}</div>
            <div class="tw-truncate tw-text-xs tw-text-slate-500">{{ $user->registration_id ?? '' }}</div>
            <div class="tw-mt-1 tw-rounded-full tw-bg-emerald-50 tw-px-2 tw-py-0.5 tw-text-[11px] tw-font-black tw-text-hm-green">{{ $percent }}% profile score</div>
        </div>
    </div>

    <div class="tw-mt-4 tw-rounded-2xl tw-bg-slate-50 tw-p-3">
        <div class="tw-flex tw-items-center tw-justify-between tw-text-xs tw-font-bold tw-text-slate-500"><span>Profile completion</span><span>{{ $percent }}%</span></div>
        <div class="tw-mt-2 tw-h-2 tw-overflow-hidden tw-rounded-full tw-bg-white"><div class="tw-h-full tw-rounded-full tw-bg-gradient-to-r tw-from-hm-green tw-to-hm-500" style="width: {{ $percent }}%"></div></div>
        <a href="{{ route('biodata.create') }}" class="tw-mt-3 tw-block tw-rounded-xl tw-bg-gradient-to-r tw-from-hm-green tw-to-hm-500 tw-px-3 tw-py-2.5 tw-text-center tw-text-sm tw-font-black tw-text-white tw-no-underline">Update Biodata</a>
    </div>

    <nav class="tw-mt-4 tw-space-y-1">
        @foreach($items as $item)
            <a href="{{ route($item['route']) }}" class="tw-flex tw-items-center tw-gap-3 tw-rounded-2xl tw-px-3 tw-py-3 tw-text-sm tw-font-bold tw-no-underline {{ request()->routeIs($item['route']) ? 'tw-bg-emerald-50 tw-text-hm-green' : 'tw-text-slate-600 hover:tw-bg-slate-50 hover:tw-text-hm-green' }}">
                <i class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}
            </a>
        @endforeach
    </nav>
</div>
