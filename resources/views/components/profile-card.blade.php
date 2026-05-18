@php
    $profile = $profile ?? null;
    $index = $index ?? 0;
    $name = $profile?->groom_name ?: optional($profile?->registration)->name ?: 'HeavenlyMatch Member';
    $id = $profile?->registration_id ?: 'HM000000';
    $age = $profile?->birth_date ? \Carbon\Carbon::parse($profile->birth_date)->age : null;
    $height = $profile?->height ?: 'Height N/A';
    $location = $profile?->present_address ?: $profile?->permanent_address ?: 'Bangladesh';
    $education = $profile?->highest_qualification ?: 'Education N/A';
    $occupation = $profile?->occupation ?: 'Profession N/A';
    $score = 78 + (($index * 7) % 18);
    $viewUrl = route('profiledetail.show', $profile?->id ?? 0);
@endphp
<article class="hm-action-card tw-overflow-hidden tw-rounded-2xl tw-border tw-border-slate-200 tw-bg-white tw-shadow-[0_4px_16px_rgba(16,24,40,.07)]">
    <div class="tw-flex tw-gap-4 tw-p-4">
        <a href="{{ $viewUrl }}" class="tw-shrink-0 tw-no-underline">
            @include('components.profile-photo', ['profile' => $profile, 'index' => $index, 'class' => 'tw-h-[92px] tw-w-[92px] tw-rounded-[1.6rem] md:tw-h-24 md:tw-w-24'])
        </a>
        <div class="tw-min-w-0 tw-flex-1">
            <div class="tw-flex tw-items-start tw-justify-between tw-gap-2">
                <div class="tw-min-w-0">
                    <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-2">
                        <a href="{{ $viewUrl }}" class="tw-text-sm tw-font-bold tw-text-slate-700 tw-no-underline">{{ $id }}</a>
                        <span class="tw-rounded-full tw-bg-emerald-50 tw-px-2 tw-py-0.5 tw-text-[11px] tw-font-black tw-uppercase tw-text-emerald-600">{{ $score }}% match</span>
                    </div>
                    <a href="{{ $viewUrl }}" class="tw-mt-1 tw-block tw-truncate tw-text-lg tw-font-extrabold tw-text-slate-950 tw-no-underline">{{ $name }}</a>
                </div>
                <div class="dropdown">
                    <button type="button" class="tw-rounded-full tw-border-0 tw-bg-transparent tw-p-1 tw-text-slate-400" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical tw-text-xl"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end tw-rounded-2xl tw-border-0 tw-p-2 tw-shadow-soft">
                        <li><a class="dropdown-item tw-rounded-xl" href="{{ $viewUrl }}">View profile</a></li>
                        <li><button class="dropdown-item tw-rounded-xl" type="button">Report profile</button></li>
                    </ul>
                </div>
            </div>
            <p class="tw-mb-0 tw-mt-2 tw-text-[15px] tw-leading-6 tw-text-slate-600">
                {{ $age ? $age . ' yrs,' : '' }} {{ $height }}, {{ $education }}, {{ $occupation }}, {{ $location }}
            </p>
        </div>
    </div>
    <div class="tw-grid tw-grid-cols-3 tw-border-t tw-border-slate-100 tw-text-sm tw-font-bold tw-text-slate-600">
        <form method="POST" action="{{ route('profiles.shortlist', $profile?->id ?? 0) }}">@csrf<button class="tw-flex tw-w-full tw-items-center tw-justify-center tw-gap-2 tw-border-0 tw-bg-white tw-py-3 tw-text-rose-600" type="submit"><i class="bi bi-star-fill"></i><span class="tw-hidden sm:tw-inline">Shortlist</span></button></form>
        <form method="POST" action="{{ route('profiles.chat', $profile?->id ?? 0) }}">@csrf<button class="tw-flex tw-w-full tw-items-center tw-justify-center tw-gap-2 tw-border-0 tw-bg-white tw-py-3 tw-text-slate-500" type="submit"><i class="bi bi-chat-square-dots-fill"></i><span class="tw-hidden sm:tw-inline">Chat</span></button></form>
        <form method="POST" action="{{ route('profiles.interest', $profile?->id ?? 0) }}">@csrf<button class="tw-flex tw-w-full tw-items-center tw-justify-center tw-gap-2 tw-border-0 tw-bg-white tw-py-3 tw-text-rose-600" type="submit"><i class="bi bi-check-circle-fill"></i><span class="tw-hidden sm:tw-inline">Interest</span></button></form>
    </div>
</article>
