@extends('layouts.app')

@push('styles')
<style>
/* Modal background */
.modal-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    display: none; /* Hidden by default */
    justify-content: center;
    align-items: center;
    z-index: 1050;
}

/* Modal card */
.modal-card {
    background: rgba(255, 255, 255, 0.98);
    border-radius: 20px;
    width: 90%;
    max-width: 550px;
    padding: 30px;
    animation: fadeIn 0.4s ease-in-out;
    position: relative;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

/* Close button */
.close-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: transparent;
    border: none;
    font-size: 1.5rem;
    color: #333;
    cursor: pointer;
    transition: color 0.3s ease;
}
.close-btn:hover {
    color: #ff4b2b;
}

/* Buttons */
.btn-flat {
    border: none;
    font-weight: bold;
    font-size: 0.9rem;
    padding: 8px 20px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.finish-btn {
    background: linear-gradient(90deg, #ff416c, #ff4b2b);
    color: #fff;
}

.finish-btn:hover {
    transform: scale(1.03);
    background: linear-gradient(90deg, #ff4b2b, #ff416c);
}

.back-btn {
    background: linear-gradient(90deg, #6c757d, #495057);
    color: #fff;
}

.back-btn:hover {
    transform: scale(1.03);
}
</style>
@endpush

@section('content')


{{-- 🌸 Registration Modal --}}
<div class="modal-background" id="registrationModal">
    <div class="modal-card shadow-lg">
        <button class="close-btn" id="closeModal">&times;</button>

        <h3 class="text-center mb-3 fw-bold text-primary">💍 Create Your Account</h3>
        <p class="text-center text-muted mb-4">Step <span id="stepNumber">1</span> of 2</p>

        <form id="registrationForm" method="POST" action="{{ route('register.store') }}">
            @csrf

            <!-- Step 1 -->
            <div class="step" id="step1">
                <div class="mb-3">
                    <label class="form-label">Matrimony Profile For</label>
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
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
                </div>

                 <div class="mb-3">
                    <label class="form-label fw-semibold">Mobile Number *</label>
                    <div class="input-group">
                        <select id="country_code" class="form-select" style="max-width: 150px;" required>
                            <option value="+880" data-code="+880">🇧🇩 +880</option>
                            <option value="+91" data-code="+91">🇮🇳 +91</option>
                            <option value="+92" data-code="+92">🇵🇰 +92</option>
                            <option value="+1" data-code="+1">🇺🇸 +1</option>
                        </select>
                        <input type="text" id="mobile" class="form-control" placeholder="Enter mobile number" required>
                    </div>
                    <input type="hidden" name="mobile_number" id="full_mobile">
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
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>

               

                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn back-btn prevBtn">⬅️ Back</button>
                    <button type="submit" class="btn finish-btn">🎉 Finish</button>
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
    const totalSteps = 2;

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

    // Close modal
    const closeModalBtn = document.getElementById('closeModal');
    const modal = document.getElementById('registrationModal');
    closeModalBtn.addEventListener('click', function () {
        modal.style.display = 'none';
        window.history.pushState({}, '', '/'); // go back to home route
    });

    // Close when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            window.history.pushState({}, '', '/');
        }
    });

    // Combine phone number
    const countryDropdown = document.getElementById('country_code');
    const mobileInput = document.getElementById('mobile');
    const fullMobile = document.getElementById('full_mobile');
    countryDropdown.addEventListener('change', updateFullNumber);
    mobileInput.addEventListener('input', updateFullNumber);
    function updateFullNumber() {
        const code = countryDropdown.value;
        const number = mobileInput.value.trim();
        fullMobile.value = code + number;
    }

    showStep(currentStep);

    // Auto-open modal if current route = register.show
    @if(Route::currentRouteName() === 'register.show')
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    @endif
});
</script>
@endpush
