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
    padding: 60px 0;
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
    max-width: 600px;
    width: 90%;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Finish button */
.finish-btn {
    background: #B5E2FF;
    border: none;
    color: #000;
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
    background: #B5E2FF;
}

/* Back button */
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
            <div class="card shadow-lg p-4 col-12 col-md-6">
                <h3 class="text-center mb-4 fw-bold text-primary">💍 Create Your Account</h3>
                <p class="text-center text-muted mb-4">Step <span id="stepNumber">1</span> of 2</p>

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

                    <!-- Step 1 -->
                    <div class="step" id="step1">

                      <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control form-control-lg" placeholder="Enter your full name" required>
                        </div>


                        <div class="mb-3">
                            <label class="form-label">Profile For</label>
                            <select name="looking_for" class="form-select" required>
                                <option value="">Select</option>
                                <option value="myself">Myself</option>
                                <option value="daughter">Daughter</option>
                                <option value="son">Son</option>
                                <option value="sister">Sister</option>
                                <option value="brother">Brother</option>
                                <option value="relative">Relative</option>
                                <option value="friend">Friend</option>
                            </select>
                        </div>

                      

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mobile Number *</label>
                            <div class="input-group">
                                <select id="country_code" name="country_code" class="form-select form-select-lg" style="max-width: 220px;" required>
                                    <option value="+880" data-code="+880">🇧🇩 Bangladesh (+880)</option>
                                    <option value="+91" data-code="+91">🇮🇳 India (+91)</option>
                                    <option value="+92" data-code="+92">🇵🇰 Pakistan (+92)</option>
                                    <option value="+1" data-code="+1">🇺🇸 USA (+1)</option>
                                </select>
                                <input type="text" name="mobile_number" id="mobile" class="form-control form-control-lg" placeholder="Enter mobile number" required>
                            </div>
                            <!-- Hidden input to hold combined number -->
                            <input type="hidden" id="full_mobile" name="full_mobile">
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

                    <!-- Step 2 -->
                    <div class="step d-none" id="step2">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg" placeholder="Enter your email" required>
                        </div>

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
                            <button type="submit" class="btn finish-btn">Email Varify</button>
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
    const totalSteps = 2;

    const countryDropdown = document.getElementById('country_code');
    const mobileInput = document.getElementById('mobile');
    const fullMobile = document.getElementById('full_mobile');

    // Show specific step
    function showStep(step) {
        document.querySelectorAll('.step').forEach((el, index) => {
            el.classList.add('d-none');
            if (index === step - 1) el.classList.remove('d-none');
        });
        document.getElementById('stepNumber').textContent = step;
    }

    // Validate inputs in current step
    function validateStep(stepDiv) {
        const inputs = stepDiv.querySelectorAll('input, select');
        let valid = true;
        inputs.forEach(input => { if (!input.checkValidity()) valid = false; });
        return valid;
    }

    // Handle "Next" button
    document.querySelectorAll('.nextBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const stepDiv = document.querySelectorAll('.step')[currentStep - 1];
            if (!validateStep(stepDiv)) { stepDiv.reportValidity(); return; }
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });
    });

    // Handle "Back" button
    document.querySelectorAll('.prevBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });
    });

    // Update full mobile number dynamically
    function updateFullNumber() {
        const code = countryDropdown.value;
        const number = mobileInput.value.trim();
        if (fullMobile) fullMobile.value = code + number;
    }

    countryDropdown.addEventListener('change', updateFullNumber);
    mobileInput.addEventListener('input', updateFullNumber);

    showStep(currentStep);
});
</script>
@endpush
