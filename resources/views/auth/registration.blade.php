@extends('layouts.app')
@push('styles')
<style>
/* Full width background behind the registration card */
.registration-background {
    background: url('https://heavenlymatch.net/public/images/login_page_backgrond_banner.jpg') no-repeat center center fixed;
    background-size: cover;
    width: 100%;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 60px 0; /* optional spacing from navbar/footer */
}

/* Wrapper to center card */
.registration-card-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
}

/* Card styling */
.card {
    border-radius: 20px;
    animation: fadeIn 0.5s ease-in-out;
    background: rgba(255, 255, 255, 0.95);
    max-width: 600px; /* optional max width */
    width: 90%;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Finish button - small flat */
.finish-btn {
    background: linear-gradient(90deg, #ff416c, #ff4b2b);
    border: none;
    color: #fff;
    font-weight: bold;
    font-size: 0.9rem;
    padding: 8px 20px;
    border-radius: 0;
    box-shadow: 0 3px 8px rgba(255, 75, 43, 0.3);
    transition: all 0.3s ease;
}

.finish-btn:hover {
    transform: scale(1.03);
    box-shadow: 0 5px 12px rgba(255, 75, 43, 0.5);
    background: linear-gradient(90deg, #ff4b2b, #ff416c);
}

/* Back button - small flat */
.back-btn {
    background: linear-gradient(90deg, #6c757d, #495057);
    border: none;
    color: #fff;
    font-weight: bold;
    font-size: 0.85rem;
    padding: 8px 20px;
    border-radius: 0;
    box-shadow: 0 2px 6px rgba(73, 80, 87, 0.3);
    transition: all 0.3s ease;
}

.back-btn:hover {
    transform: scale(1.03);
    box-shadow: 0 4px 10px rgba(73, 80, 87, 0.5);
    background: linear-gradient(90deg, #495057, #6c757d);
}
</style>
@endpush
@section('content')
<div class="registration-background">
    <div class="registration-card-wrapper">
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
                            <button type="button" class="btn back-btn prevBtn">⬅️ Back</button>
                            <button type="submit" class="btn finish-btn">🎉 Finish & Create Account</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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
