@php
    $user = auth()->user();
    $biodata = $user?->biodata;
    $status = $biodata?->is_completed ? ($biodata->approval_status ?: 'pending') : 'incomplete';
@endphp
<header class="tw-sticky tw-top-0 tw-z-40 tw-border-b tw-border-slate-200 tw-bg-white/95 tw-shadow-sm tw-backdrop-blur">
    <div class="tw-mx-auto tw-flex tw-h-16 tw-max-w-[1240px] tw-items-center tw-justify-between tw-gap-5 tw-px-5">
        <a href="{{ route('myhome') }}" class="tw-flex tw-items-center tw-gap-3 tw-text-xl tw-font-extrabold tw-text-slate-950 tw-no-underline">
            <span class="tw-grid tw-h-10 tw-w-10 tw-place-items-center tw-rounded-2xl tw-bg-gradient-to-br tw-from-hm-green tw-to-hm-500 tw-text-white tw-shadow-card">❤</span>
            <span>HeavenlyMatch</span>
        </a>
        <nav class="tw-flex tw-items-center tw-gap-1">
            @foreach([
                ['myhome','Home'], ['matches','Matches'], ['search','Search'], ['inbox','Mailbox'], ['upgrade','Upgrade']
            ] as [$route, $label])
                <a class="tw-rounded-full tw-px-4 tw-py-2 tw-text-sm tw-font-bold tw-no-underline {{ request()->routeIs($route) ? 'tw-bg-emerald-50 tw-text-hm-green' : 'tw-text-slate-600 hover:tw-bg-slate-100' }}" href="{{ route($route) }}">{{ $label }}</a>
            @endforeach
        </nav>
        <div class="tw-flex tw-items-center tw-gap-3">
            <a href="{{ route('inbox') }}" class="tw-relative tw-grid tw-h-10 tw-w-10 tw-place-items-center tw-rounded-full tw-bg-slate-50 tw-text-slate-700 tw-no-underline hover:tw-bg-emerald-50 hover:tw-text-hm-green">
                <i class="bi bi-bell tw-text-xl"></i><span class="tw-absolute tw-right-0 tw-top-0 tw-grid tw-h-5 tw-w-5 tw-place-items-center tw-rounded-full tw-bg-rose-500 tw-text-[11px] tw-font-black tw-text-white">3</span>
            </a>
            <div class="dropdown">
                <button class="tw-flex tw-items-center tw-gap-2 tw-rounded-full tw-border tw-border-slate-200 tw-bg-white tw-p-1 tw-pr-3 tw-text-sm tw-font-bold tw-text-slate-700 tw-shadow-sm" data-bs-toggle="dropdown" type="button">
                    <span class="tw-grid tw-h-9 tw-w-9 tw-place-items-center tw-rounded-full tw-bg-gradient-to-br tw-from-hm-green tw-to-hm-500 tw-font-black tw-text-white">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span>
                    <span class="tw-max-w-[150px] tw-truncate">{{ $user->name ?? 'User' }}</span>
                    <i class="bi bi-chevron-down tw-text-xs"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end tw-w-72 tw-rounded-3xl tw-border-0 tw-p-2 tw-shadow-soft">
                    <li class="tw-px-3 tw-py-3">
                        <div class="tw-text-sm tw-font-black tw-text-slate-900">{{ $user->name ?? 'User' }}</div>
                        <div class="tw-text-xs tw-text-slate-500">{{ $user->registration_id ?? '' }} · {{ ucfirst($status) }}</div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item tw-rounded-2xl" href="{{ route('profiledetail') }}"><i class="bi bi-person-vcard me-2"></i> Profile</a></li>
                    <li><a class="dropdown-item tw-rounded-2xl" href="{{ route('biodata.create') }}"><i class="bi bi-pencil-square me-2"></i> Edit biodata</a></li>
                    <li><a class="dropdown-item tw-rounded-2xl" href="{{ route('shortlist') }}"><i class="bi bi-star me-2"></i> Shortlist</a></li>
                    <li><a class="dropdown-item tw-rounded-2xl" href="{{ route('upgrade') }}"><i class="bi bi-gem me-2"></i> Upgrade</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><form action="{{ route('logout') }}" method="POST">@csrf<button class="dropdown-item tw-rounded-2xl tw-text-rose-600" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Logout</button></form></li>
                </ul>
            </div>
        </div>
    </div>
</header>
