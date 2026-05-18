@extends('layouts.app')

@section('title', 'Login | HeavenlyMatch')

@section('content')
<section class="tw-min-h-screen tw-bg-[radial-gradient(circle_at_top_left,#fff1f8,transparent_35%),linear-gradient(135deg,#ffffff_0%,#fff7fc_45%,#f7f4ff_100%)] tw-px-4 tw-py-10">
    <div class="tw-mx-auto tw-grid tw-w-full tw-max-w-5xl tw-gap-6 lg:tw-grid-cols-[0.9fr_1fr]">
        <aside class="tw-hidden tw-rounded-[2rem] tw-bg-gradient-to-br tw-from-hm-700 tw-via-hm-600 tw-to-hm-500 tw-p-8 tw-text-white tw-shadow-soft lg:tw-block">
            <div class="tw-inline-flex tw-rounded-full tw-bg-white/15 tw-px-4 tw-py-2 tw-text-sm tw-font-black">❤ HeavenlyMatch Matrimony</div>
            <h1 class="tw-mt-10 tw-text-5xl tw-font-black tw-leading-tight">Welcome back to your safe matrimony dashboard.</h1>
            <p class="tw-mt-5 tw-text-sm tw-leading-7 tw-text-white/80">Login to continue biodata completion, profile search, matches and membership tools.</p>
        </aside>

        <div class="tw-rounded-[2rem] tw-border tw-border-slate-200 tw-bg-white tw-p-6 tw-shadow-soft sm:tw-p-8">
            <div class="tw-text-center">
                <div class="tw-mx-auto tw-grid tw-h-16 tw-w-16 tw-place-items-center tw-rounded-3xl tw-bg-hm-50 tw-text-3xl">🔐</div>
                <h1 class="tw-mt-4 tw-text-3xl tw-font-black tw-text-slate-950">Login</h1>
                <p class="tw-text-sm tw-text-slate-500">Access your HeavenlyMatch account</p>
            </div>

            @if(session('error'))
                <div class="tw-mt-5 tw-rounded-3xl tw-border tw-border-rose-200 tw-bg-rose-50 tw-p-4 tw-text-sm tw-font-bold tw-text-rose-700">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="tw-mt-5 tw-rounded-3xl tw-border tw-border-rose-200 tw-bg-rose-50 tw-p-4 tw-text-sm tw-font-bold tw-text-rose-700">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ Route::has('login.post') ? route('login.post') : url('/login') }}" class="tw-mt-6 tw-space-y-4">
                @csrf
                <div>
                    <label class="tw-mb-2 tw-block tw-text-sm tw-font-black">Email address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-3" placeholder="you@example.com" required autofocus>
                </div>
                <div>
                    <label class="tw-mb-2 tw-block tw-text-sm tw-font-black">Password</label>
                    <div class="tw-relative">
                        <input type="password" id="password" name="password" class="form-control tw-rounded-2xl tw-border-slate-200 tw-py-3 tw-pr-12" placeholder="Enter password" required>
                        <button type="button" onclick="togglePassword()" class="tw-absolute tw-right-3 tw-top-1/2 -tw-translate-y-1/2 tw-rounded-full tw-border-0 tw-bg-transparent tw-text-slate-400"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <div class="tw-flex tw-items-center tw-justify-between tw-text-sm">
                    <label class="tw-flex tw-items-center tw-gap-2 tw-font-bold tw-text-slate-600"><input type="checkbox" name="remember" class="form-check-input"> Remember me</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="tw-font-bold tw-text-hm-700 tw-no-underline">Forgot password?</a>
                    @endif
                </div>
                <button type="submit" class="tw-w-full tw-rounded-2xl tw-bg-gradient-to-r tw-from-hm-700 tw-to-hm-500 tw-px-5 tw-py-3 tw-text-sm tw-font-black tw-text-white tw-shadow-sm">Login →</button>
            </form>

            <div class="tw-mt-6 tw-text-center tw-text-sm tw-text-slate-600">
                New here? <a href="{{ route('register.show') }}" class="tw-font-black tw-text-hm-700 tw-no-underline">Create account</a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endpush
