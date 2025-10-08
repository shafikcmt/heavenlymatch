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
        <form method="POST" action="{{ route('email.send.code') }}" id="resendForm">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" id="resendBtn" class="btn btn-outline-primary w-100">🔄 Resend Code</button>
        </form>

        <p id="timerText" class="text-center mt-2 text-muted"></p>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Show animated alert
function showAlert(type, message) {
    const alertBox = document.getElementById('alertBox');
    alertBox.className = `alert alert-${type} animate__animated animate__fadeInDown`;
    alertBox.textContent = message;
    alertBox.classList.remove('d-none');
    setTimeout(() => {
        alertBox.classList.replace('animate__fadeInDown', 'animate__fadeOutUp');
    }, 4000);
    setTimeout(() => alertBox.classList.add('d-none'), 5000);
}

// Flash messages
@php
$success = session('success');
$error = session('error');
$lastSent = auth()->user()->email_verification_sent_at ?? now();
@endphp

@if($success)
showAlert('success', '{{ $success }}');
@endif

@if($error)
showAlert('danger', '{{ $error }}');
@endif

// Resend timer
const resendBtn = document.getElementById('resendBtn');
const timerText = document.getElementById('timerText');

let remaining = @json(max(0, 120 - now()->diffInSeconds($lastSent)));

function startTimer(seconds) {
    if(seconds <= 0) return;
    resendBtn.disabled = true;
    resendBtn.style.pointerEvents = 'none';

    const interval = setInterval(() => {
        let minutes = Math.floor(seconds / 60);
        let secs = seconds % 60;
        timerText.textContent = `You can resend code in ${minutes}:${secs < 10 ? '0'+secs : secs}`;
        seconds--;
        if(seconds < 0) {
            clearInterval(interval);
            resendBtn.disabled = false;
            resendBtn.style.pointerEvents = 'auto';
            timerText.textContent = '';
        }
    }, 1000);
}

startTimer(remaining);
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endpush
