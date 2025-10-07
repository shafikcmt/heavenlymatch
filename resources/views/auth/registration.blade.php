@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg p-4 col-12 col-md-6" style="border-radius: 20px;">
        <h3 class="text-center mb-4 fw-bold text-primary">💍 Create Your Account</h3>
        <p class="text-center text-muted mb-4">Step <span id="stepNumber">1</span> of 4</p>

        <!-- Alert Messages -->
        <div id="alertBox" class="alert d-none" role="alert"></div>

        @if($errors->any())
            <div class="alert alert-danger animate__animated animate__fadeIn">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="registrationForm" method="POST" action="{{ route('register.store') }}">
            @csrf

            <!-- Step 1: Name & Gender -->
            <div class="step" id="step1">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="name" class="form-control form-control-lg" placeholder="Enter your full name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Gender</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="male" required>
                        <label class="form-check-label">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="female">
                        <label class="form-check-label">Female</label>
                    </div>
                </div>
                <button type="button" class="btn btn-primary w-100 py-2 nextBtn">Next ➡️</button>
            </div>

            <!-- Step 2: Email -->
            <div class="step d-none" id="step2">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Enter your email" required>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-secondary prevBtn">⬅️ Back</button>
                    <button type="button" class="btn btn-primary nextBtn">Next ➡️</button>
                </div>
            </div>

            <!-- Step 3: Mobile -->
            <div class="step d-none" id="step3">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Country Code</label>
                    <select name="country_code" class="form-select form-select-lg" required>
                        <option value="+880">🇧🇩 +880 (Bangladesh)</option>
                        <option value="+91">🇮🇳 +91 (India)</option>
                        <option value="+92">🇵🇰 +92 (Pakistan)</option>
                        <option value="+1">🇺🇸 +1 (USA)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mobile Number</label>
                    <input type="text" name="mobile_number" class="form-control form-control-lg" placeholder="Enter mobile number" required>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-secondary prevBtn">⬅️ Back</button>
                    <button type="button" class="btn btn-primary nextBtn">Next ➡️</button>
                </div>
            </div>

            <!-- Step 4: Password -->
            <div class="step d-none" id="step4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Enter password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control form-control-lg" placeholder="Confirm password" required>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-secondary prevBtn">⬅️ Back</button>
                    <button type="submit" class="btn btn-success w-100">🎉 Finish & Create Account</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    let currentStep = 1;
    const totalSteps = 4;

    function showStep(step) {
        document.querySelectorAll('.step').forEach((el, index) => {
            el.classList.add('d-none');
            if (index === step - 1) el.classList.remove('d-none');
        });
        document.getElementById('stepNumber').textContent = step;
    }

    function validateStep(stepDiv) {
        const inputs = stepDiv.querySelectorAll('input, select');
        let valid = true;
        inputs.forEach(input => { if (!input.checkValidity()) valid = false; });
        return valid;
    }

    document.querySelectorAll('.nextBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const stepDiv = document.querySelectorAll('.step')[currentStep - 1];
            if (!validateStep(stepDiv)) { stepDiv.reportValidity(); return; }
            if (currentStep < totalSteps) { currentStep++; showStep(currentStep); }
        });
    });

    document.querySelectorAll('.prevBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            if (currentStep > 1) { currentStep--; showStep(currentStep); }
        });
    });

    showStep(currentStep);
});
</script>
@endpush
