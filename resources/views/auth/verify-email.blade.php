@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg p-4 col-12 col-md-6" style="border-radius: 15px;">
        <h3 class="text-center mb-4 text-primary fw-bold">💌 Verify Your Email</h3>

        <!-- Alert Box -->
        <div id="alertBox" class="alert d-none" role="alert"></div>

        <!-- Verify Code Form -->
        <form method="POST" action="{{ route('email.verify.code') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <div class="mb-3">
                <label class="form-label fw-semibold">Verification Code</label>
                <input type="text" name="code" class="form-control form-control-lg" placeholder="Enter 6-digit code" required>
            </div>
            <button type="submit" class="btn btn-success w-100 mb-3">✅ Verify Email</button>
        </form>

        <!-- Resend Code Form -->
        <form method="POST" action="{{ route('email.send.code') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" class="btn btn-outline-primary w-100">🔄 Resend Code</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Function to show animated alert
function showAlert(type, message) {
    const alertBox = document.getElementById('alertBox');
    alertBox.className = `alert alert-${type} animate__animated animate__fadeInDown`;
    alertBox.textContent = message;
    alertBox.classList.remove('d-none');

    // Auto-hide after 4 seconds with fade out
    setTimeout(() => {
        alertBox.classList.replace('animate__fadeInDown', 'animate__fadeOutUp');
    }, 4000);
    setTimeout(() => {
        alertBox.classList.add('d-none');
    }, 5000);
}

// Show server-side flash messages
@php
    $success = session('success');
    $error = session('error');
@endphp

@if($success)
    showAlert('success', '{{ $success }}');
@endif

@if($error)
    showAlert('danger', '{{ $error }}');
@endif
</script>

<!-- Include Animate.css CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endpush
