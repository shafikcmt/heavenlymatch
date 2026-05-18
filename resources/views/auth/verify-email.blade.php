@extends('layouts.app')

@section('title', 'Verify Email | HeavenlyMatch')

@section('content')
<section class="tw-min-h-screen tw-bg-[radial-gradient(circle_at_top_left,#fff1f8,transparent_35%),linear-gradient(135deg,#ffffff_0%,#fff7fc_45%,#f7f4ff_100%)] tw-px-4 tw-py-10">
    <div class="tw-mx-auto tw-max-w-xl tw-rounded-[2rem] tw-border tw-border-slate-200 tw-bg-white tw-p-6 tw-shadow-soft sm:tw-p-8">
        <div class="tw-text-center">
            <div class="tw-mx-auto tw-grid tw-h-16 tw-w-16 tw-place-items-center tw-rounded-3xl tw-bg-hm-50 tw-text-3xl">✉️</div>
            <h1 class="tw-mt-4 tw-text-3xl tw-font-black tw-text-slate-950">Verify your email</h1>
            <p class="tw-mt-2 tw-text-sm tw-leading-6 tw-text-slate-500">Enter the 6-digit code from your inbox, or click the verification link in the email.</p>
        </div>

        @if(session('success'))
            <div class="tw-mt-5 tw-rounded-3xl tw-border tw-border-emerald-200 tw-bg-emerald-50 tw-p-4 tw-text-sm tw-font-bold tw-text-emerald-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="tw-mt-5 tw-rounded-3xl tw-border tw-border-rose-200 tw-bg-rose-50 tw-p-4 tw-text-sm tw-font-bold tw-text-rose-700">{{ session('error') }}</div>
        @endif
        @if(!empty($devCode))
            <div class="tw-mt-5 tw-rounded-3xl tw-border tw-border-amber-200 tw-bg-amber-50 tw-p-4 tw-text-sm tw-font-bold tw-text-amber-800">Local/dev verification code: {{ $devCode }}</div>
        @endif
        @if($errors->any())
            <div class="tw-mt-5 tw-rounded-3xl tw-border tw-border-rose-200 tw-bg-rose-50 tw-p-4 tw-text-sm tw-font-bold tw-text-rose-700">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('email.verify.code') }}" class="tw-mt-6 tw-space-y-4">
            @csrf
            <div>
                <label class="tw-mb-2 tw-block tw-text-sm tw-font-black">Email address</label>
                <input type="email" name="email" value="{{ old('email', $email ?? session('email')) }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-3" required>
                <p class="tw-mt-2 tw-text-xs tw-text-slate-500">You can edit this if your session expired.</p>
            </div>
            <div>
                <label class="tw-mb-2 tw-block tw-text-sm tw-font-black">Verification code</label>
                <input type="text" name="code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-4 tw-text-center tw-text-2xl tw-tracking-[.5em]" placeholder="000000" required>
            </div>
            <button type="submit" class="tw-w-full tw-rounded-2xl tw-bg-gradient-to-r tw-from-hm-700 tw-to-hm-500 tw-px-5 tw-py-3 tw-text-sm tw-font-black tw-text-white tw-shadow-sm">Verify email →</button>
        </form>

        <div class="tw-my-6 tw-flex tw-items-center tw-gap-4 tw-text-xs tw-font-black tw-uppercase tw-text-slate-400"><span class="tw-h-px tw-flex-1 tw-bg-slate-200"></span>or<span class="tw-h-px tw-flex-1 tw-bg-slate-200"></span></div>

        <form method="POST" action="{{ route('email.send.code') }}">
            @csrf
            <input type="hidden" name="email" value="{{ old('email', $email ?? session('email')) }}">
            <button type="submit" id="resendBtn" class="tw-w-full tw-rounded-2xl tw-border tw-border-hm-200 tw-bg-white tw-px-5 tw-py-3 tw-text-sm tw-font-black tw-text-hm-700" @if(($remaining ?? 0) > 0) disabled @endif>
                @if(($remaining ?? 0) > 0) Resend in {{ $remaining }}s @else Resend verification code @endif
            </button>
        </form>

        <div class="tw-mt-6 tw-rounded-3xl tw-bg-slate-50 tw-p-4 tw-text-sm tw-leading-6 tw-text-slate-600">
            <strong class="tw-text-slate-900">Not receiving email?</strong><br>
            Check spam/junk. If resend fails, update your mail settings and run <code>php artisan config:clear</code>.
        </div>

        <div class="tw-mt-6 tw-text-center">
            <a href="{{ route('login') }}" class="tw-text-sm tw-font-black tw-text-slate-600 tw-no-underline">Back to login</a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    let remaining = {{ (int) ($remaining ?? 0) }};
    const btn = document.getElementById('resendBtn');
    if (!btn || remaining <= 0) return;
    const timer = setInterval(function () {
        remaining--;
        if (remaining <= 0) {
            btn.disabled = false;
            btn.textContent = 'Resend verification code';
            clearInterval(timer);
        } else {
            btn.textContent = 'Resend in ' + remaining + 's';
        }
    }, 1000);
})();
</script>
@endpush
