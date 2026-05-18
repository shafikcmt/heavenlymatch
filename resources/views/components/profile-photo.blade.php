@props(['profile' => null, 'index' => 0, 'class' => 'tw-h-20 tw-w-20 tw-rounded-full', 'large' => false])
@php
    $type = optional($profile)->biodata_type === 'groom' ? 'male' : 'female';
    $number = (($index ?? 0) % 4) + 1;
    $fallback = asset("images/dummy-profiles/{$type}-{$number}.svg");
    $stored = optional($profile)->groom_photo;
    $src = $stored ? asset('storage/' . ltrim($stored, '/')) : $fallback;
@endphp
<img src="{{ $src }}" alt="Profile photo" class="hm-profile-photo {{ $class }}" loading="lazy">
