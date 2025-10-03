<!-- resources/views/biodata-form.blade.php -->
@extends('layouts.app')

@push('styles')
    <style>
    .stepper {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}
.step {
    cursor: pointer;
    padding: 10px;
    border-bottom: 2px solid #ccc;
}
.step.active {
    font-weight: bold;
    border-bottom: 2px solid #007bff;
}
.step-content {
    display: none;
}
.step-content.active {
    display: block;
}

</style>
@endpush

@section('content')
<div class="stepper">
    <div class="step active" data-step="1">
        <div class="circle">1</div>
        <div class="label">General Info</div>
    </div>
    <div class="step" data-step="2">
        <div class="circle">2</div>
        <div class="label">Address</div>
    </div>
    <div class="step" data-step="3">
        <div class="circle">3</div>
        <div class="label">Contact</div>
    </div>
</div>

<form method="POST" action="">
    @csrf
    <!-- Step 1 -->
    <div class="step-content active" data-step="1">
        <h3>General Info</h3>
        <input type="text" name="groom_name" placeholder="Groom Name">
        <button type="button" class="next-btn">Next</button>
    </div>

    <!-- Step 2 -->
    <div class="step-content" data-step="2">
        <h3>Address</h3>
        <input type="text" name="address" placeholder="Address">
        <button type="button" class="back-btn">Back</button>
        <button type="button" class="next-btn">Next</button>
    </div>

    <!-- Step 3 -->
    <div class="step-content" data-step="3">
        <h3>Contact</h3>
        <input type="text" name="guardian_mobile" placeholder="Guardian Mobile">
        <input type="email" name="guardian_email" placeholder="Guardian Email">
        <button type="button" class="back-btn">Back</button>
        <button type="submit">Submit</button>
    </div>
</form>


@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    let steps = document.querySelectorAll(".step");
    let contents = document.querySelectorAll(".step-content");
    let currentStep = 1;

    function showStep(step) {
        // Update sidebar steps
        steps.forEach(s => {
            s.classList.remove("active");
            if (parseInt(s.dataset.step) === step) {
                s.classList.add("active");
            }
        });

        // Update form sections
        contents.forEach(c => {
            c.classList.remove("active");
            if (parseInt(c.dataset.step) === step) {
                c.classList.add("active");
            }
        });

        currentStep = step;
    }

    // Next Button
    document.querySelectorAll(".next-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            if (currentStep < steps.length) {
                showStep(currentStep + 1);
            }
        });
    });

    // Back Button
    document.querySelectorAll(".back-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            if (currentStep > 1) {
                showStep(currentStep - 1);
            }
        });
    });

    // Sidebar Step Click
    steps.forEach(step => {
        step.addEventListener("click", function () {
            showStep(parseInt(this.dataset.step));
        });
    });

    // Initialize
    showStep(currentStep);
});
</script>
@endsection
