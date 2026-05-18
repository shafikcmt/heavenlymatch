@props([
    'title' => 'Home',
    'subtitle' => null,
    'back' => null,
    'tabs' => [],
    'activeTab' => null,
])
<header class="hm-mobile-header hm-mobile-only tw-sticky tw-top-0 tw-z-40">
    <div class="tw-flex tw-items-center tw-justify-between tw-gap-3 tw-px-4 tw-pb-5 tw-pt-4">
        <div class="tw-flex tw-min-w-0 tw-items-center tw-gap-3">
            @if($back)
                <a href="{{ $back }}" class="tw-grid tw-h-10 tw-w-10 tw-place-items-center tw-rounded-full tw-text-white tw-no-underline hover:tw-bg-white/10"><i class="bi bi-arrow-left tw-text-2xl"></i></a>
            @endif
            <div class="tw-min-w-0">
                <h1 class="tw-mb-0 tw-truncate tw-text-2xl tw-font-extrabold tw-uppercase tw-tracking-wide">{{ $title }}</h1>
                @if($subtitle)<p class="tw-mb-0 tw-text-xs tw-text-white/75">{{ $subtitle }}</p>@endif
            </div>
        </div>
        <div class="tw-flex tw-items-center tw-gap-3">
            <a href="{{ route('inbox') }}" class="tw-relative tw-grid tw-h-10 tw-w-10 tw-place-items-center tw-rounded-full tw-text-white tw-no-underline hover:tw-bg-white/10">
                <i class="bi bi-bell tw-text-2xl"></i>
                <span class="tw-absolute tw-right-1 tw-top-1 tw-grid tw-h-4 tw-w-4 tw-place-items-center tw-rounded-full tw-bg-rose-500 tw-text-[10px] tw-font-black">3</span>
            </a>
            <a href="{{ route('upgrade') }}" class="tw-grid tw-h-10 tw-w-10 tw-place-items-center tw-rounded-full tw-text-white tw-no-underline hover:tw-bg-white/10"><i class="bi bi-headset tw-text-2xl"></i></a>
        </div>
    </div>
    @if(count($tabs))
        <nav class="hm-mobile-tabbar tw-flex tw-gap-7 tw-overflow-x-auto tw-px-4 tw-text-sm tw-font-bold tw-uppercase tw-text-white/55">
            @foreach($tabs as $key => $tab)
                <a href="{{ $tab['url'] ?? '#' }}" class="tw-relative tw-shrink-0 tw-pb-4 tw-text-white tw-no-underline {{ $activeTab === $key ? 'tw-opacity-100' : 'tw-opacity-55' }}">
                    {{ $tab['label'] }}
                    @if($activeTab === $key)
                        <span class="tw-absolute tw-bottom-0 tw-left-0 tw-right-0 tw-h-1 tw-rounded-t-full tw-bg-white"></span>
                    @endif
                </a>
            @endforeach
        </nav>
    @endif
</header>
