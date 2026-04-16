@extends('layouts.user-dashboard-app')

@section('sidebar')
.
@endsection

@section('subnavbar')
.
@endsection

@push('styles')
{{-- CSS --}}

<style>
    .stepper {
        background: #fff;
        border-radius: 12px;
        padding: 25px 20px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        transition: all 0.3s ease-in-out;
        border-left: 6px solid #6f42c1;
        /* prominent left border */
        margin-bottom: 40px;
        position: relative;
    }

    /* Hover effect on stepper */
    .stepper:hover {
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.18);
        transform: translateY(-2px);
    }

    /* Step item */
    .step {
        position: relative;
        padding-left: 30px;
        /* space for small circle */
        margin-bottom: 35px;
        display: flex;
        align-items: center;
    }

    /* Last step */
    .step:last-child {
        margin-bottom: 0;
    }

    /* Small circle indicator */
    .step .circle {
        position: absolute;
        left: -8px;
        /* smaller offset for small circle */
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #e0e0e0;
        border: 2px solid #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 12px;
        color: #6f42c1;
        box-shadow: 0 0 0 2px #6f42c1;
        transition: all 0.3s ease-in-out;
        z-index: 2;
    }

    /* Active circle */
    .step.active .circle {
        background: #e83e8c;
        color: #fff;
        box-shadow: 0 0 0 3px #ff5fa2;
        transform: scale(1.3);
    }

    /* Vertical line connecting small circles */
    .step:not(:last-child)::after {
        content: "";
        position: absolute;
        left: -8px;
        top: 20px;
        /* start below the small circle */
        width: 3px;
        height: calc(100% - 20px);
        background: #e0e0e0;
        border-radius: 2px;
        z-index: 1;
    }

    /* Step label */
    .step .label {
        font-weight: 600;
        font-size: 12px;
        color: #212529;
        transition: color 0.3s;
    }

    /* Hover label effect */
    .step:hover .label {
        color: #6f42c1;
    }

    /* Optional description */
    .step p {
        margin: 0;
        font-size: 13px;
        color: #6c757d;
    }

    /* Step Content Card */
    .step-content {
        background: #f9f9fb;
        border: 1px solid #e0e0e0;
        border-left: 5px solid #6f42c1;
        border-radius: 10px;
        padding: 30px 25px;
        margin-bottom: 35px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease-in-out;
    }

    .step-content:hover {
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    /* Step header with progress */
    .step-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .step-header h2 {
        font-size: 18px;
        font-weight: 700;
        color: #343a40;
        margin: 0;
    }

    .progress {
        height: 8px;
        border-radius: 5px;
        overflow: hidden;
        background: #e0e0e0;
        flex: 1;
        margin-left: 15px;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(135deg, #6f42c1, #e83e8c);
        width: 0;
        transition: width 0.4s ease-in-out;
    }

    .step-content h4 {
        font-size: 20px;
        font-weight: 700;
        color: #343a40;
        margin-bottom: 18px;
        position: relative;
        padding-left: 12px;
    }

    .step-content h4::before {
        content: "";
        position: absolute;
        left: 0;
        top: 5px;
        width: 4px;
        height: 70%;
        background: linear-gradient(135deg, #6f42c1, #e83e8c);
        border-radius: 3px;
    }

    /* Form Inputs */
    .step-content .form-control {
        border-radius: 6px;
        padding: 11px 14px;
        font-size: 15px;
        border: 1px solid #ced4da;
        transition: all 0.3s ease-in-out;
    }

    .step-content .form-control:focus {
        border-color: #6f42c1;
        box-shadow: 0 0 6px rgba(111, 66, 193, 0.25);
    }

    /* Buttons */
    .step-buttons {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 25px;
    }

    .step-buttons .btn {
        border-radius: 6px;
        padding: 10px 20px;
        font-weight: 600;
        min-width: 130px;
        transition: all 0.3s ease-in-out;
    }

    .btn-primary {
        background: #6f42c1;
        border: none;
        color: #fff;
    }

    .btn-primary:hover {
        background: #e83e8c;
        box-shadow: 0 6px 14px rgba(232, 62, 140, 0.25);
    }

    .btn-secondary {
        background: #6c757d;
        border: none;
        color: #fff;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .btn-success {
        background: #198754;
        border: none;
        color: #fff;
    }

    .btn-success:hover {
        background: #157347;
    }

    .btn-warning {
        background: #ffc107;
        border: none;
        color: #212529;
    }

    .btn-warning:hover {
        background: #e0a800;
    }


    /* Make every input and select the same height */
    .form-control,
    .form-select {
        height: 48px !important;
        padding: 10px 12px !important;
        font-size: 15px;
    }

    /* Make label spacing consistent */
    label.form-label,
    label {
        margin-bottom: 5px;
        font-weight: 500;
    }

    /* Make error message consistent */
    small.text-danger {
        margin-top: 4px;
        display: block;
    }

    /* ENSURE column boxes look together */
    .equal-box {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
</style>

@endpush

@section('content')

@php
$step = $step ?? 1; // if $step is not set, use 1
@endphp

<div class="container py-4">
    <div class="row">
        <!-- Stepper Navigation -->
        <div class="col-md-3">
            <div class="stepper">
                <div class="step active" data-step="1">
                    <div class="circle">1</div>
                    <div class="label">Personal Information</div>
                </div>
                <div class="step" data-step="2">
                    <div class="circle">2</div>
                    <div class="label">Address</div>
                </div>
                <div class="step" data-step="3">
                    <div class="circle">3</div>
                    <div class="label">Educational & Career</div>
                </div>
                <div class="step" data-step="4">
                    <div class="circle">4</div>
                    <div class="label">Family Information</div>
                </div>
                <div class="step" data-step="5">
                    <div class="circle">5</div>
                    <div class="label">Personal Information & Lifestyle</div>
                </div>
                <div class="step" data-step="6">
                    <div class="circle">6</div>
                    <div class="label">Occupational Information</div>
                </div>
                <div class="step" data-step="7">
                    <div class="circle">7</div>
                    <div class="label">Marriage & Future plan</div>
                </div>
                <div class="step" data-step="8">
                    <div class="circle">8</div>
                    <div class="label">Expected Life Partner</div>
                </div>
                <div class="step" data-step="9">
                    <div class="circle">9</div>
                    <div class="label">Interest & Hobbies</div>
                </div>
                <div class="step" data-step="10">
                    <div class="circle">10</div>
                    <div class="label">Contact Details</div>
                </div>
            </div>
        </div>

        <!-- Form Content -->

        <div class="col-md-9">

            <form id="biodataForm" method="POST" action="{{ route('biodata.store', $step) }}" enctype="multipart/form-data">
                @csrf
                <!-- ✅ Step 1: General Info -->
                @if($step == 1)
                <div class="step-content active" data-step="1">
                    <!-- Step Header with Progress -->
                    <div class="step-header">
                        <h2>Step {{ $step }} of 10</h2>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($step/10)*100 }}%;"></div>
                        </div>
                    </div>
                    <h4>Personal Information</h4>
                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-4 mb-3">
                            <label>Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $registration->name ?? '') }}" placeholder="Enter your full name">
                            @error('name')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="col-md-4 mb-3">
                            <label>Gender *</label>
                            <select name="gender" class="form-control">
                                <option selected value="Male" {{ old('gender', $registration->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $registration->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender', $registration->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>

                            @error('gender')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Profile Created For -->
                        <div class="col-md-4 mb-3">
                            <label>Profile Created For *</label>
                            <select name="profile_created_for" class="form-control">
                                <option selected value="Self" {{ old('profile_created_for', $registration->profile_created_for ?? '') == 'Self' ? 'selected' : '' }}>Self</option>
                                <option value="Son" {{ old('profile_created_for', $registration->profile_created_for ?? '') == 'Son' ? 'selected' : '' }}>Son</option>
                                <option value="Daughter" {{ old('profile_created_for', $registration->profile_created_for ?? '') == 'Daughter' ? 'selected' : '' }}>Daughter</option>
                                <option value="Brother" {{ old('profile_created_for', $registration->profile_created_for ?? '') == 'Brother' ? 'selected' : '' }}>Brother</option>
                                <option value="Sister" {{ old('profile_created_for', $registration->profile_created_for ?? '') == 'Sister' ? 'selected' : '' }}>Sister</option>
                                <option value="Relative" {{ old('profile_created_for', $registration->profile_created_for ?? '') == 'Relative' ? 'selected' : '' }}>Relative</option>
                                <option value="Friend" {{ old('profile_created_for', $registration->profile_created_for ?? '') == 'Friend' ? 'selected' : '' }}>Friend</option>
                            </select>

                            @error('profile_created_for')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>


                    <style>
                        /* Make all form groups same height */
                        .equal-box {
                            display: flex;
                            flex-direction: column;
                            height: 100%;
                        }
                    </style>

                    <div class="row">

                        <!-- Marital Status -->
                        <div class="col-md-4 mb-3">
                            <div class="equal-box">
                                <label class="form-label">Marital Status *</label>
                                <select name="marital_status" class="form-select">
                                    <option value="Never Married" {{ old('marital_status', $biodata['step_1']['marital_status'] ?? '' )=='Never Married' ? 'selected' : '' }}>Never Married</option>
                                    <option value="Divorced" {{ old('marital_status', $biodata['step_1']['marital_status'] ?? '' )=='Divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="Widow" {{ old('marital_status', $biodata['step_1']['marital_status'] ?? '' )=='Widow' ? 'selected' : '' }}>Widow</option>
                                </select>
                                @error('marital_status')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Height -->
                        <div class="col-md-4 mb-3">
                            <div class="equal-box">
                                <label for="height" class="form-label">Height *</label>
                                <select name="height" id="height" class="form-select">
                                    @php
                                    $heights = [
                                    "4'6\"" => "137 cm", "4'7\"" => "140 cm", "4'8\"" => "142 cm", "4'9\"" => "145 cm",
                                    "4'10\"" => "147 cm", "4'11\"" => "150 cm", "5'0\"" => "152 cm", "5'1\"" => "155 cm",
                                    "5'2\"" => "157 cm", "5'3\"" => "160 cm", "5'4\"" => "162 cm", "5'5\"" => "165 cm",
                                    "5'6\"" => "167 cm", "5'7\"" => "170 cm", "5'8\"" => "173 cm", "5'9\"" => "175 cm",
                                    "5'10\"" => "178 cm", "5'11\"" => "180 cm", "6'0\"" => "183 cm", "6'1\"" => "185 cm",
                                    "6'2\"" => "188 cm", "6'3\"" => "190 cm", "6'4\"" => "193 cm", "6'5\"" => "196 cm",
                                    "6'6\"" => "198 cm"
                                    ];
                                    @endphp

                                    @foreach($heights as $ft => $cm)
                                    <option value="{{ $ft }}" {{ old('height', $biodata['step_1']['height'] ?? '' )==$ft ? 'selected' : '' }}>
                                        {{ $ft }} ({{ $cm }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('height')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Weight -->
                        <div class="col-md-4 mb-3">
                            <div class="equal-box">
                                <label for="weight" class="form-label">Weight *</label>
                                <select name="weight" id="weight" class="form-select">
                                    @php
                                    $weights = [];
                                    for ($i = 30; $i <= 120; $i++) { $weights[]=$i . ' kg' ; } @endphp @foreach($weights as $w) <option value="{{ $w }}" {{ old('weight', $biodata['step_1']['weight'] ?? '' )==$w ? 'selected' : '' }}>
                                        {{ $w }}
                                        </option>
                                        @endforeach
                                </select>
                                @error('weight')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                    </div>




                    <div class="row g-3">

                        <!-- Birth Date Column -->
                        <div class="col-md-6">
                            <label>Birth Date *</label>
                            @php
                            $savedDate = old('birth_date', $biodata['step_1']['birth_date'] ?? '');
                            $savedDay = $savedDate ? date('d', strtotime($savedDate)) : '';
                            $savedMonth = $savedDate ? date('m', strtotime($savedDate)) : '';
                            $savedYear = $savedDate ? date('Y', strtotime($savedDate)) : '';
                            @endphp

                            <div class="combined-date-box">
                                <select id="day" class="date-part">
                                    <option value="">Day</option>
                                    @for($d = 1; $d <= 31; $d++) <option value="{{ $d }}" {{ $savedDay==$d ? 'selected' : '' }}>{{ $d }}</option>
                                        @endfor
                                </select>

                                <select id="month" class="date-part">
                                    <option value="">Month</option>
                                    @for($m = 1; $m <= 12; $m++) <option value="{{ $m }}" {{ $savedMonth==$m ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                        </option>
                                        @endfor
                                </select>

                                <select id="year" class="date-part">
                                    <option value="">Year</option>
                                    @for($y = date('Y'); $y >= 1950; $y--)
                                    <option value="{{ $y }}" {{ $savedYear==$y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <input type="hidden" name="birth_date" id="birth_date" value="{{ $savedDate }}">

                            @error('birth_date')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Disease Column -->
                        <div class="col-md-6">
                            <label>Mental or Physical Diseases *</label>
                            <select name="disease_status" id="disease_status" class="form-control h-100">
                                <option value="">Select Option</option>
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>

                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="diseaseModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Please Describe the Disease</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <textarea name="disease_description" class="form-control" placeholder="Describe your condition"></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <style>
                        .combined-date-box {
                            display: flex;
                            align-items: center;
                            gap: 0;
                            border: 1px solid #ccc;
                            border-radius: 5px;
                            overflow: hidden;
                            height: 45px;
                        }

                        .date-part {
                            flex: 1;
                            border: none !important;
                            border-right: 1px solid #ccc !important;
                            padding: 8px 35px 8px 12px;
                            /* right padding for arrow */
                            font-size: 14px;
                            appearance: none;
                            -webkit-appearance: none;
                            -moz-appearance: none;
                            background-image: url("data:image/svg+xml,%3Csvg width='10' height='5' viewBox='0 0 10 5' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0L5 5L10 0H0Z' fill='%23333'/%3E%3C/svg%3E");
                            background-repeat: no-repeat;
                            background-position: right 10px center;
                            background-size: 10px 5px;
                        }

                        .date-part:last-child {
                            border-right: none !important;
                        }

                        .date-part:focus {
                            outline: none !important;
                            box-shadow: none !important;
                        }

                        select.form-control.h-100 {
                            height: 45px;
                        }
                    </style>





                    <script>
                        document.addEventListener('DOMContentLoaded', function() {

                            // Birth Date logic
                            function updateBirthDate() {
                                const d = document.getElementById('day').value;
                                const m = document.getElementById('month').value;
                                const y = document.getElementById('year').value;

                                if (d && m && y) {
                                    document.getElementById('birth_date').value = `${y}-${String(m).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                                }
                            }

                            ['day', 'month', 'year'].forEach(id => {
                                document.getElementById(id).addEventListener('change', updateBirthDate);
                            });

                            // Disease modal logic
                            const diseaseSelect = document.getElementById('disease_status');
                            diseaseSelect.addEventListener('change', function() {
                                if (this.value === 'Yes') {

                                    // Remove focus and wait a frame to prevent vibration
                                    this.blur();

                                    requestAnimationFrame(() => {
                                        var modal = new bootstrap.Modal(document.getElementById('diseaseModal'), {
                                            backdrop: 'static', // prevent clicking outside to close if needed
                                            keyboard: true
                                        });
                                        modal.show();
                                    });
                                }
                            });

                        });
                    </script>

                    <!-- Row 1: Complexion + Blood Group (Two Columns) -->
                    <div class="row">
                        <!-- Complexion -->
                        <div class="col-md-6 mb-3">
                            <label>Complexion *</label>
                            <select name="complexion" class="form-select">
                                <option {{ old('complexion', $biodata['step_1']['complexion'] ?? '' )=='Fair' ? 'selected' : '' }}>Fair</option>
                                <option {{ old('complexion', $biodata['step_1']['complexion'] ?? '' )=='Brown' ? 'selected' : '' }}>Brown</option>
                                <option {{ old('complexion', $biodata['step_1']['complexion'] ?? '' )=='Dark' ? 'selected' : '' }}>Dark</option>
                            </select>
                            @error('complexion') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Blood Group -->
                        <div class="col-md-6 mb-3">
                            <label>Blood Group *</label>
                            <select name="blood_group" class="form-select">
                                <option {{ old('blood_group', $biodata['step_1']['blood_group'] ?? '' )=='A+' ? 'selected' : '' }}>A+</option>
                                <option {{ old('blood_group', $biodata['step_1']['blood_group'] ?? '' )=='A-' ? 'selected' : '' }}>A-</option>
                                <option {{ old('blood_group', $biodata['step_1']['blood_group'] ?? '' )=='B+' ? 'selected' : '' }}>B+</option>
                                <option {{ old('blood_group', $biodata['step_1']['blood_group'] ?? '' )=='B-' ? 'selected' : '' }}>B-</option>
                                <option {{ old('blood_group', $biodata['step_1']['blood_group'] ?? '' )=='O+' ? 'selected' : '' }}>O+</option>
                                <option {{ old('blood_group', $biodata['step_1']['blood_group'] ?? '' )=='O-' ? 'selected' : '' }}>O-</option>
                                <option {{ old('blood_group', $biodata['step_1']['blood_group'] ?? '' )=='AB+' ? 'selected' : '' }}>AB+</option>
                                <option {{ old('blood_group', $biodata['step_1']['blood_group'] ?? '' )=='AB-' ? 'selected' : '' }}>AB-</option>
                            </select>
                            @error('blood_group') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <!-- Row 2: Language FULL WIDTH on single row -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Languages*</label>

                            <div class="multi-select position-relative">
                                <div id="selectedLanguages" class="d-flex flex-wrap gap-2 mb-2"></div>

                                <input type="text" id="langInput" class="form-control" placeholder="Type to search..." onclick="showDropdown()" onkeyup="filterLanguage()" autocomplete="off" />

                                <ul id="langList" class="list-group position-absolute w-100 mt-1 shadow-sm" style="z-index: 10; display: none;"></ul>
                            </div>
                        </div>
                    </div>


                    <style>
                        #langList li.list-group-item {
                            cursor: pointer;
                        }

                        #langList li.list-group-item:hover {
                            background-color: #0d6efd;
                            color: white;
                        }
                    </style>

                    <script>
                        let allLanguages = ['English', 'Hindi', 'Bangla', 'Arabic', 'Urdu'];
                        let selectedLanguages = [];

                        function showDropdown() {
                            document.getElementById('langList').style.display = 'block';
                            renderDropdown();
                        }

                        function hideDropdown() {
                            document.getElementById('langList').style.display = 'none';
                        }

                        function renderDropdown() {
                            const list = document.getElementById('langList');
                            list.innerHTML = '';

                            allLanguages.forEach(lang => {
                                if (!selectedLanguages.includes(lang)) {
                                    const li = document.createElement('li');
                                    li.className = 'list-group-item list-group-item-action';
                                    li.textContent = lang;
                                    li.onclick = () => addLanguage(lang);
                                    list.appendChild(li);
                                }
                            });
                        }

                        function addLanguage(lang) {
                            if (!selectedLanguages.includes(lang)) {
                                selectedLanguages.push(lang);
                                renderSelectedLanguages();
                                renderDropdown();
                                document.getElementById('langInput').value = '';
                            }
                        }

                        function removeLanguage(lang) {
                            selectedLanguages = selectedLanguages.filter(l => l !== lang);
                            renderSelectedLanguages();
                            renderDropdown();
                        }

                        function renderSelectedLanguages() {
                            const container = document.getElementById('selectedLanguages');
                            container.innerHTML = '';
                            selectedLanguages.forEach(lang => {
                                const badge = document.createElement('span');
                                badge.className = 'badge bg-primary';
                                badge.innerHTML = lang + ' <span class="remove" onclick="removeLanguage(\'' + lang + '\')">&times;</span>';
                                container.appendChild(badge);
                            });
                        }

                        function filterLanguage() {
                            const input = document.getElementById('langInput').value.toLowerCase();
                            const list = document.getElementById('langList');
                            list.innerHTML = '';

                            allLanguages.forEach(lang => {
                                if (!selectedLanguages.includes(lang) && lang.toLowerCase().includes(input)) {
                                    const li = document.createElement('li');
                                    li.className = 'list-group-item list-group-item-action';
                                    li.textContent = lang;
                                    li.onclick = () => addLanguage(lang);
                                    list.appendChild(li);
                                }
                            });
                        }

                        document.addEventListener('click', function(event) {
                            const multiSelect = document.querySelector('.multi-select');
                            if (!multiSelect.contains(event.target)) hideDropdown();
                        });
                    </script>
                </div>
                @endif


                <!-- Step 2: Address -->
                @if($step == 2)
                <div class="step-content active" data-step="2">

                    <!-- Step Header -->
                    <div class="step-header mb-4">
                        <h2>Step {{ $step }} of 10</h2>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($step/10)*100 }}%;"></div>
                        </div>
                    </div>

                    <h4 class="mb-3">Address</h4>

                    <!-- =============== PRESENT ADDRESS ================= -->
                    <div class="row border p-3 rounded mb-4">
                        <h5 class="mb-3">Present Address</h5>

                        <!-- Division -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Division *</label>
                            <select name="present_division" class="form-select"></select>
                        </div>

                        <!-- District -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">District *</label>
                            <select name="present_district" class="form-select"></select>
                        </div>

                        <!-- Upazila -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upazila *</label>
                            <select name="present_upazila" class="form-select"></select>
                        </div>

                        <!-- Village -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Village / Area *</label>
                            <input type="text" name="present_village" class="form-control" placeholder="e.g. South Vabanipur">
                        </div>
                    </div>

                    <!-- =============== PERMANENT ADDRESS ================= -->
                    <div class="row border p-3 rounded">
                        <h5 class="mb-3">Permanent Address</h5>

                        <div class="col-md-12 mb-3">
                            <input type="checkbox" id="same_as_present">
                            <label for="same_as_present">Same as Present Address</label>
                        </div>

                        <!-- Division -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Division *</label>
                            <select name="permanent_division" class="form-select"></select>
                        </div>

                        <!-- District -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">District *</label>
                            <select name="permanent_district" class="form-select"></select>
                        </div>

                        <!-- Upazila -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upazila *</label>
                            <select name="permanent_upazila" class="form-select"></select>
                        </div>

                        <!-- Village -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Village / Area *</label>
                            <input type="text" name="permanent_village" class="form-control" placeholder="e.g. North Vabanipur">
                        </div>

                        <!-- Country -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nationality *</label>
                            <select name="permanent_country" class="form-select">
                                <option value="">-- Select Your Country --</option>
                                <option value="Bangladesh" selected>Bangladesh</option>
                                <option value="India">India</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="USA">United States</option>
                                <option value="UK">United Kingdom</option>
                                <option value="UAE">United Arab Emirates</option>
                            </select>
                        </div>

                        <!-- Grow Up -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Where did you grow up? *</label>
                            <select name="grow_up" class="form-select">
                                <option value="">-- Select District --</option>
                                <option>Dhaka</option>
                                <option>Chattogram</option>
                                <option>Barishal</option>
                                <option>Jhalakathi</option>
                                <option>Sylhet</option>
                                <option>Khulna</option>
                                <option>Rajshahi</option>
                                <option>Mymensingh</option>
                            </select>
                        </div>
                    </div>
                </div>
                @endif


                <!-- ======================== JS ======================== -->

                <script>
                    let divisions = [];
                    let districts = [];
                    let upazilas = [];

                    // Load JSON data on page load
                    async function loadData() {
                        divisions = await fetch("/json/divisions.json").then(res => res.json());
                        districts = await fetch("/json/districts.json").then(res => res.json());
                        upazilas = await fetch("/json/upazilas.json").then(res => res.json());

                        loadDivisions("present_division");
                        loadDivisions("permanent_division");
                    }

                    window.onload = loadData;

                    // ---------- Load Divisions ----------
                    function loadDivisions(selectName) {
                        const select = document.querySelector(`[name='${selectName}']`);
                        select.innerHTML = `<option value="">Select Division</option>`;
                        divisions.forEach(d => {
                            select.innerHTML += `<option value="${d.id}">${d.name}</option>`;
                        });
                    }

                    // ---------- Load Districts ----------
                    function loadDistricts(divisionId, targetName) {
                        const target = document.querySelector(`[name='${targetName}']`);
                        target.innerHTML = `<option value="">Select District</option>`;
                        districts.filter(d => d.division_id == divisionId).forEach(d => {
                            target.innerHTML += `<option value="${d.id}">${d.name}</option>`;
                        });
                    }

                    // ---------- Load Upazilas ----------
                    function loadUpazilas(districtId, targetName) {
                        const target = document.querySelector(`[name='${targetName}']`);
                        target.innerHTML = `<option value="">Select Upazila</option>`;
                        upazilas.filter(u => u.district_id == districtId).forEach(u => {
                            target.innerHTML += `<option value="${u.id}">${u.name}</option>`;
                        });
                    }

                    // ----------------- Event Listeners -----------------

                    // Present Address select changes
                    document.addEventListener("change", function(e) {
                        if (e.target.name === "present_division") {
                            loadDistricts(e.target.value, "present_district");
                        }
                        if (e.target.name === "present_district") {
                            loadUpazilas(e.target.value, "present_upazila");
                        }
                    });

                    // Permanent Address select changes
                    document.addEventListener("change", function(e) {
                        if (e.target.name === "permanent_division") {
                            loadDistricts(e.target.value, "permanent_district");
                        }
                        if (e.target.name === "permanent_district") {
                            loadUpazilas(e.target.value, "permanent_upazila");
                        }
                    });

                    // -------- Same as Present Checkbox ----------
                    document.getElementById("same_as_present").addEventListener("change", function() {
                        if (this.checked) {

                            // Division
                            document.querySelector("[name='permanent_division']").value =
                                document.querySelector("[name='present_division']").value;
                            loadDistricts(document.querySelector("[name='present_division']").value, "permanent_district");

                            // District
                            document.querySelector("[name='permanent_district']").value =
                                document.querySelector("[name='present_district']").value;
                            loadUpazilas(document.querySelector("[name='present_district']").value, "permanent_upazila");

                            // Upazila
                            document.querySelector("[name='permanent_upazila']").value =
                                document.querySelector("[name='present_upazila']").value;

                            // Village
                            document.querySelector("[name='permanent_village']").value =
                                document.querySelector("[name='present_village']").value;

                            // Country always Bangladesh by default
                            document.querySelector("[name='permanent_country']").value = "Bangladesh";

                        } else {
                            // Reset Permanent
                            document.querySelector("[name='permanent_division']").value = "";
                            document.querySelector("[name='permanent_district']").innerHTML = `<option value="">Select District</option>`;
                            document.querySelector("[name='permanent_upazila']").innerHTML = `<option value="">Select Upazila</option>`;
                            document.querySelector("[name='permanent_village']").value = "";
                            document.querySelector("[name='permanent_country']").value = "";
                        }
                    });
                </script>


                <!-- Step 3: Educational Qualifications -->
                @if($step == 3)
                <div class="step-content active" data-step="3">
                    <!-- Step Header with Progress -->
                    <div class="step-header">
                        <h2>Step {{ $step }} of 10</h2>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($step/10)*100 }}%;"></div>
                        </div>
                    </div>

                    <h4 class="mb-3">Educational Qualifications</h4>

                    <div class="row">
                        <!-- Education Method -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Your Education Method *</label>
                            <select id="education-method" name="education_method" class="form-select">
                                @foreach(['General','Qawmi','Alia'] as $method)
                                <option value="{{ $method }}" {{ old('education_method', $biodata['step_3']['education_method'] ?? '' )==$method ? 'selected' : '' }}>
                                    {{ $method }}
                                </option>
                                @endforeach
                            </select>
                            @error('education_method')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Highest Qualification -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Highest Educational Qualification *</label>
                          

                         
                                @php
                                $education_types = old('education_type', $biodata['step_3']['education_type'] ?? ['']);
                                @endphp

                                @foreach($education_types as $index => $type)
                                <div class="education-block mb-3 p-3 border rounded bg-light shadow-sm">
                                    <select name="education_type[]" class="form-select education-select">
                                        <option value="">-- Select --</option>
                                    </select>

                                    <div class="education-fields mt-3"></div>
                                </div>
                                @endforeach
                           

                            <button type="button" id="add-education" class="btn btn-success btn-sm">
                                + Add Qualification
                            </button>

                            @error('education_type.*')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Other Educational Qualifications -->
                    <div class="mb-3">
                        <label for="other_education" class="form-label fw-bold">
                            Other educational qualifications *
                        </label>
                        <input type="text" class="form-control" id="other_education" name="other_education" placeholder="Enter other qualifications" value="{{ old('other_education', $biodata['step_3']['other_education'] ?? '') }}">
                        @error('other_education')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                @endif
                <script>
                    const generalOptions = [
                        'Below SSC', 'SSC', 'HSC', 'Diploma',
                        'Graduation', 'Post Graduation', 'Doctorate'
                    ];

                    const qawmiOptions = [
                        'Primary Islamic education',
                        'Ibtidaiyah',
                        'Mutawassitah',
                        'Sanabia Uliya',
                        'Fazilat',
                        'Takmil',
                        'Takhassus'
                    ];

                    // Update dropdown values
                    function updateQualificationOptions(method) {
                        document.querySelectorAll(".education-select").forEach(select => {
                            select.innerHTML = `<option value="">-- Select --</option>`;

                            // General & Alia → Same
                            let options = (method === "Qawmi") ? qawmiOptions : generalOptions;

                            options.forEach(text => {
                                const option = document.createElement("option");
                                option.value = text;
                                option.textContent = text;
                                select.appendChild(option);
                            });
                        });
                    }

                    // Dynamic fields
                    function getFields(type) {
                        return `
        <div class="row">
            <div class="col-md-4 mb-2">
                <label>Institution</label>
                <input type="text" name="institution[]" class="form-control">
            </div>
            <div class="col-md-4 mb-2">
                <label>Major / Subject</label>
                <input type="text" name="subject[]" class="form-control">
            </div>
            <div class="col-md-4 mb-2">
                <label>Passing Year</label>
                <input type="number" min="1950" max="2050" name="passing_year[]" class="form-control">
            </div>
        </div>
    `;
                    }

                    // Change Education Method
                    document.getElementById("education-method").addEventListener("change", function() {
                        updateQualificationOptions(this.value);
                    });

                    // Change Qualification type
                    document.addEventListener("change", function(e) {
                        if (e.target.classList.contains("education-select")) {
                            const container = e.target.closest(".education-block").querySelector(".education-fields");
                            container.innerHTML = getFields(e.target.value);
                        }
                    });

                    // Add new qualification block
                    document.getElementById("add-education").addEventListener("click", function() {
                        const method = document.getElementById("education-method").value;

                        const block = document.createElement("div");
                        block.className = "education-block mb-3 p-3 border rounded bg-light shadow-sm";

                        block.innerHTML = `
        <select name="education_type[]" class="form-select education-select">
            <option value="">-- Select --</option>
        </select>
        <div class="education-fields mt-3"></div>
    `;

                        document.getElementById("education-container").appendChild(block);
                        updateQualificationOptions(method);
                    });

                    // Auto init after page load
                    document.addEventListener("DOMContentLoaded", function() {
                        updateQualificationOptions(document.getElementById("education-method").value);
                    });
                </script>
              


                <!-- Step 4: Family Information -->
                @if($step == 4)
                <div class="step-content active" data-step="4">
                    <!-- Step Header with Progress -->
                    <div class="step-header">
                        <h2>Step {{ $step }} of 10</h2>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($step/10)*100 }}%;"></div>
                        </div>
                    </div>
                    <h4 class="mb-3">Family Information</h4>

                    <!-- Father Info -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Father's Name *</label>
                        <input type="text" name="father_name" class="form-control" placeholder="Abdul Haque" value="{{ old('father_name', $biodata['step_4']['father_name'] ?? '') }}">
                        <small class="text-muted">Only for authority</small>
                        @error('father_name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Is your father alive? *</label>
                        <select name="father_alive" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="1" {{ old('father_alive', $biodata['step_4']['father_alive'] ?? '' )=='1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('father_alive', $biodata['step_4']['father_alive'] ?? '' )=='0' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('father_alive') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Father's Profession *</label>
                        <textarea name="father_profession" class="form-control" rows="2" placeholder="Businessman, Teacher, etc.">{{ old('father_profession', $biodata['step_4']['father_profession'] ?? '') }}</textarea>
                        @error('father_profession') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Mother Info -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mother's Name *</label>
                        <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name', $biodata['step_4']['mother_name'] ?? '') }}">
                        @error('mother_name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Is your mother alive? *</label>
                        <select name="mother_alive" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="1" {{ old('mother_alive', $biodata['step_4']['mother_alive'] ?? '' )=='1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('mother_alive', $biodata['step_4']['mother_alive'] ?? '' )=='0' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('mother_alive') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mother's Profession *</label>
                        <textarea name="mother_profession" class="form-control" rows="2" placeholder="Housewife, Teacher, etc.">{{ old('mother_profession', $biodata['step_4']['mother_profession'] ?? '') }}</textarea>
                        @error('mother_profession') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Brothers -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">How many brothers do you have? *</label>
                        <input type="number" name="brothers" id="brothers" class="form-control" min="0" value="{{ old('brothers', $biodata['step_4']['brothers'] ?? 0) }}">
                        @error('brothers') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 {{ old('brothers', $biodata['step_4']['brothers'] ?? 0) > 0 ? '' : 'd-none' }}" id="brothers_info_box">
                        <label class="form-label fw-bold">Brothers Information</label>
                        <textarea name="brothers_info" class="form-control" rows="3" placeholder="Educational qualifications, marital status, occupation. Separate multiple brothers with commas.">{{ old('brothers_info', $biodata['step_4']['brothers_info'] ?? '') }}</textarea>
                    </div>

                    <!-- Sisters -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">How many sisters do you have? *</label>
                        <input type="number" name="sisters" id="sisters" class="form-control" min="0" value="{{ old('sisters', $biodata['step_4']['sisters'] ?? 0) }}">
                        @error('sisters') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 {{ old('sisters', $biodata['step_4']['sisters'] ?? 0) > 0 ? '' : 'd-none' }}" id="sisters_info_box">
                        <label class="form-label fw-bold">Sisters Information</label>
                        <textarea name="sisters_info" class="form-control" rows="3" placeholder="Educational qualifications, marital status, occupation. Separate multiple sisters with commas.">{{ old('sisters_info', $biodata['step_4']['sisters_info'] ?? '') }}</textarea>
                    </div>

                    <!-- Uncles -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Profession of Uncles</label>
                        <textarea name="uncle_profession" class="form-control" rows="2">{{ old('uncle_profession', $biodata['step_4']['uncle_profession'] ?? '') }}</textarea>
                    </div>

                    <!-- Financial Status -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Family Financial Status *</label>
                        <select name="family_financial_status" class="form-select">
                            <option value="">-- Select --</option>
                            @foreach(['Lower class','Middle class','Upper middle class','Rich'] as $option)
                            <option value="{{ $option }}" {{ old('family_financial_status', $biodata['step_4']['family_financial_status'] ?? '' )==$option ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                            @endforeach
                        </select>
                        @error('family_financial_status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description of Family's Financial Situation *</label>
                        <textarea name="family_details" class="form-control" rows="3" placeholder="Residential house, land, family business, etc.">{{ old('family_details', $biodata['step_4']['family_details'] ?? '') }}</textarea>
                        @error('family_details') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Religious Condition -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Family's Religious Condition *</label>
                        <textarea name="family_religious_condition" class="form-control" rows="3" placeholder="Describe family religious practices, environment of mahram & non-mahram, etc.">{{ old('family_religious_condition', $biodata['step_4']['family_religious_condition'] ?? '') }}</textarea>
                        @error('family_religious_condition') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                @endif

                <!-- Step 5: Personal Information -->
                @if($step == 5)
                <div class="step-content active" data-step="5">
                    <!-- Step Header with Progress -->
                    <div class="step-header">
                        <h2>Step {{ $step }} of 10</h2>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($step/10)*100 }}%;"></div>
                        </div>
                    </div>
                    <h4 class="mb-3">Personal Information</h4>

                    <!-- Clothes -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">What kind of clothes do you usually wear outside the house? *</label>
                        <input type="text" name="clothing_style" class="form-control" placeholder="e.g., Wear burqa with hijab, no niqab" value="{{ old('clothing_style', $biodata['step_5']['clothing_style'] ?? '') }}">
                        @error('clothing_style') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    @php
                    // Fetch logged-in user’s gender from registration table
                    $gender = Auth::user()->gender ?? $biodata['step_5']['gender'] ?? null;
                    @endphp

                    <!-- Hidden gender input (optional, for form submission) -->
                    <input type="hidden" name="gender" value="{{ $gender }}">

                    <!-- Gender-based dynamic fields -->
                    <div id="gender-specific-fields">
                        @if ($gender === 'male')
                        <!-- Beard -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Do you have beard according to sunnah? Since when? *</label>
                            <input type="text" name="beard_info" class="form-control" placeholder="e.g., 5 years" value="{{ old('beard_info', $biodata['step_5']['beard_info'] ?? '') }}">
                            @error('beard_info') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Clothes above ankles -->
                        <div class="mb-3">
                            <label for="clothes_above_ankles" class="form-label fw-bold">
                                Do you wear clothes above the ankles? *
                            </label>
                            <select name="clothes_above_ankles" id="clothes_above_ankles" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="yes" {{ old('clothes_above_ankles', $biodata['step_5']['clothes_above_ankles'] ?? '' )=='yes' ? 'selected' : '' }}>Yes</option>
                                <option value="no" {{ old('clothes_above_ankles', $biodata['step_5']['clothes_above_ankles'] ?? '' )=='no' ? 'selected' : '' }}>No</option>
                            </select>
                            @error('clothes_above_ankles')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        @elseif ($gender === 'female')
                        <!-- Niqab / Veil -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Since when have you been wearing a veil with a niqab? *</label>
                            <input type="text" name="niqab_since" class="form-control" placeholder="e.g., 3 years" value="{{ old('niqab_since', $biodata['step_5']['niqab_since'] ?? '') }}">
                            @error('niqab_since') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        @endif
                    </div>


                    <!-- Prayer -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Do you pray five times a day? How many times a week are missed (Qaza)? *</label>
                        <input type="text" name="prayers_info" class="form-control" placeholder="Yes/No, Qaza times per week" value="{{ old('prayers_info', $biodata['step_5']['prayers_info'] ?? '') }}">
                        @error('prayers_info') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Mahram Compliance -->
                    <div class="mb-3">
                        <label for="mahram_nonmahram" class="form-label fw-bold">
                            Do you comply with mahram / non-mahram? *
                        </label>
                        <select name="mahram_nonmahram" id="mahram_nonmahram" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="yes" {{ old('mahram_nonmahram', $biodata['step_5']['mahram_nonmahram'] ?? '' )=='yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ old('mahram_nonmahram', $biodata['step_5']['mahram_nonmahram'] ?? '' )=='no' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('mahram_nonmahram')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="mb-3">
                        <label class="form-label fw-bold">Are you able to read the Quran correctly? *</label>
                        <select name="quran_recitation" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="Yes" {{ old('quran_recitation', $biodata['step_5']['quran_recitation'] ?? '' )=='Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('quran_recitation', $biodata['step_5']['quran_recitation'] ?? '' )=='No' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('quran_recitation')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <!-- Fiqh -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Which Fiqh do you follow? *</label>
                        <select name="fiqh" class="form-select">
                            <option value="">-- Select --</option>
                            @foreach(['Hanafi','Shafi','Maliki','Hanbali'] as $option)
                            <option value="{{ $option }}" {{ old('fiqh', $biodata['step_5']['fiqh'] ?? '' )==$option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                        @error('fiqh') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Entertainment -->
                    <div class="mb-3">
                        <label for="watch_entertainment" class="form-label fw-bold">
                            Do you watch or listen to dramas / movies / serials / songs? *
                        </label>
                        <select name="watch_entertainment" id="watch_entertainment" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="yes" {{ old('watch_entertainment', $biodata['step_5']['watch_entertainment'] ?? '' )=='yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ old('watch_entertainment', $biodata['step_5']['watch_entertainment'] ?? '' )=='no' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('watch_entertainment')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Health -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Do you have any mental or physical diseases? *</label>
                        <select name="diseases" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="Yes" {{ old('diseases', $biodata['step_5']['diseases'] ?? '' )=='Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('diseases', $biodata['step_5']['diseases'] ?? '' )=='No' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('diseases')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Shrine beliefs -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">What are your ideas or beliefs about the shrine (Mazar)? *</label>
                        <textarea name="beliefs_on_mazar" class="form-control" rows="3">{{ old('beliefs_on_mazar', $biodata['step_5']['beliefs_on_mazar'] ?? '') }}</textarea>
                        @error('beliefs_on_mazar') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Books -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Write the names of at least 3 Islamic books you have read *</label>
                        <input type="text" name="books_read" class="form-control" value="{{ old('books_read', $biodata['step_5']['books_read'] ?? '') }}" placeholder="Separate by comma">
                        @error('books_read') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select the category applicable to you</label>
                        @php
                        $categories = ['Disabled','Infertile','Converted Muslim','Orphan','Interested in becoming a second wife','Tablig'];
                        $selected = old('special_category', $biodata['step_5']['special_category'] ?? []);
                        @endphp
                        <div>
                            @foreach($categories as $category)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="special_category[]" value="{{ $category }}" {{ in_array($category, $selected) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $category }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>


                    <!-- Conversion -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mention the time and story of your conversion to Islam *</label>
                        <textarea name="conversion_story" class="form-control" rows="4">{{ old('conversion_story', $biodata['step_5']['conversion_story'] ?? '') }}</textarea>
                        @error('conversion_story') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Hobbies -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Write about your hobbies, likes, dislikes, tastes, dreams, etc. *</label>
                        <textarea name="hobbies" class="form-control" rows="4">{{ old('hobbies', $biodata['step_5']['hobbies'] ?? '') }}</textarea>
                        @error('hobbies') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Photo -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Upload Groom's Photo</label>
                        <input type="file" name="groom_photo" class="form-control" accept="image/*">
                        @if(isset($biodata['step_5']['groom_photo']))
                        <small class="form-text text-muted">
                            Already uploaded: <a href="{{ asset('storage/'.$biodata['step_5']['groom_photo']) }}" target="_blank">View Photo</a>
                        </small>
                        @endif
                        @error('groom_photo') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                @endif

                <!-- Step 6: Occupational Information -->
                @if($step == 6)
                <div class="step-content active" data-step="6">
                    <!-- Step Header with Progress -->
                    <div class="step-header">
                        <h2>Step {{ $step }} of 10</h2>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($step/10)*100 }}%;"></div>
                        </div>
                    </div>
                    <h4 class="mb-3">Occupational Information</h4>

                    <!-- Occupation -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Occupation *</label>
                        <input type="text" name="occupation" class="form-control" placeholder="e.g., Madrasa Teacher, Software Engineer" value="{{ old('occupation', $biodata['step_6']['occupation'] ?? '') }}">
                        @error('occupation') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Description of Profession -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description of Profession *</label>
                        <textarea name="profession_details" class="form-control" rows="3" placeholder="You may write where your working place is, which company you are working in, whether your earnings are halal or not, etc.">{{ old('profession_details', $biodata['step_6']['profession_details'] ?? '') }}</textarea>
                        @error('profession_details') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Monthly Income -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Monthly Income *</label>
                        <input type="number" name="monthly_income" class="form-control" placeholder="20000" value="{{ old('monthly_income', $biodata['step_6']['monthly_income'] ?? '') }}">
                        @error('monthly_income') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                @endif

                <!-- Step 7: Marriage Related Information -->
                @if($step == 7)
                <div class="step-content active" data-step="7">
                    <!-- Step Header with Progress -->
                    <div class="step-header">
                        <h2>Step {{ $step }} of 10</h2>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($step/10)*100 }}%;"></div>
                        </div>
                    </div>
                    <h4 class="mb-3">Marriage Related Information</h4>

                    <!-- Guardian Agreement -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Do your guardians agree to your marriage? *</label>
                        <select name="guardian_agree" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="Yes" {{ old('guardian_agree', $biodata['step_7']['guardian_agree'] ?? '' )=='Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('guardian_agree', $biodata['step_7']['guardian_agree'] ?? '' )=='No' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('guardian_agree')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Wife in Veil -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Will you be able to keep your wife in the veil after marriage? *</label>
                        <select name="wife_in_veil" class="form-select">
                            <option value="">-- Select --</option>
                            @php
                            $options = ['Yes', 'No', 'InshaAllah'];
                            $selected = old('wife_in_veil', $biodata['step_7']['wife_in_veil'] ?? '');
                            @endphp
                            @foreach($options as $option)
                            <option value="{{ $option }}" {{ $selected==$option ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                            @endforeach
                        </select>
                        @error('wife_in_veil')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Wife Study Allowed -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Would you like to allow your wife to study after marriage? *</label>
                        <select name="wife_study_allowed" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="Yes" {{ old('wife_study_allowed', $biodata['step_7']['wife_study_allowed'] ?? '' )=='Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('wife_study_allowed', $biodata['step_7']['wife_study_allowed'] ?? '' )=='No' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('wife_study_allowed')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Wife Job Allowed -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Would you like to allow your wife to do any job after marriage? *</label>
                        <select name="wife_job_allowed" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="Yes" {{ old('wife_job_allowed', $biodata['step_7']['wife_job_allowed'] ?? '' )=='Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('wife_job_allowed', $biodata['step_7']['wife_job_allowed'] ?? '' )=='No' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('wife_job_allowed')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Residence After Marriage -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Where will you live with your wife after marriage? *</label>
                        <select name="residence_after_marriage" class="form-select">
                            <option value="">-- Select --</option>
                            @foreach(['Own House', 'Wife’s House', 'Rented House', 'Other'] as $option)
                            <option value="{{ $option }}" {{ old('residence_after_marriage', $biodata['step_7']['residence_after_marriage'] ?? '' )==$option ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                            @endforeach
                        </select>
                        @error('residence_after_marriage')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Expect Gift from Bride -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Would you or your family expect any gift from the bride's family? *</label>
                        <select name="expect_gift_from_bride" class="form-select">
                            <option value="">-- Select --</option>
                            @foreach(['Yes', 'No'] as $option)
                            <option value="{{ $option }}" {{ old('expect_gift_from_bride', $biodata['step_7']['expect_gift_from_bride'] ?? '' )==$option ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                            @endforeach
                        </select>
                        @error('expect_gift_from_bride')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                @endif

                <!-- Step 8: Expected Life Partner -->
                @if($step == 8)
                <div class="step-content active" data-step="8">
                    <!-- Step Header with Progress -->
                    <div class="step-header">
                        <h2>Step {{ $step }} of 10</h2>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($step/10)*100 }}%;"></div>
                        </div>
                    </div>
                    <h4 class="mb-3">Expected Life Partner</h4>
                    <!-- Partner Age Range -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Preferred Age Range *</label>
                        <div class="d-flex gap-2">
                            @php
                            $ageFrom = old('partner_age', $biodata['step_8']['partner_age'] ?? '');
                            $ageFromValue = explode('-', $ageFrom)[0] ?? '';
                            $ageToValue = explode('-', $ageFrom)[1] ?? '';
                            @endphp

                            <select id="age-from" class="form-select">
                                <option value="">From</option>
                                @for($from = 18; $from <= 55; $from++) <option value="{{ $from }}" {{ $ageFromValue==$from ? 'selected' : '' }}>{{ $from }}</option>
                                    @endfor
                            </select>

                            <select id="age-to" class="form-select">
                                <option value="">To</option>
                                @for($to = 23; $to <= 60; $to++) <!-- minimum 5-year gap -->
                                    <option value="{{ $to }}" {{ $ageToValue==$to ? 'selected' : '' }}>{{ $to }}</option>
                                    @endfor
                            </select>

                            <!-- Hidden input to store combined value -->
                            <input type="hidden" name="partner_age" id="partner-age" value="{{ $ageFrom }}">
                        </div>
                        <small class="text-muted">Select the preferred age range.</small>
                        @error('partner_age') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <script>
                        const ageFromSelect = document.getElementById('age-from');
                        const ageToSelect = document.getElementById('age-to');
                        const hiddenAgeInput = document.getElementById('partner-age');

                        function updateAge() {
                            if (ageFromSelect.value && ageToSelect.value) {
                                hiddenAgeInput.value = ageFromSelect.value + '-' + ageToSelect.value;
                            } else {
                                hiddenAgeInput.value = '';
                            }
                        }

                        ageFromSelect.addEventListener('change', updateAge);
                        ageToSelect.addEventListener('change', updateAge);
                    </script>



                    <!-- Complexion -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Complexion *</label>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach(['Dark','Brown','Bright Brown','Fair','Bright Fair'] as $option)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="partner_complexion[]" value="{{ $option }}" id="complexion_{{ $loop->index }}" {{ in_array($option, old('partner_complexion', $biodata['step_8']['partner_complexion'] ?? [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="complexion_{{ $loop->index }}">
                                    {{ $option }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <small class="text-muted">You may select multiple items. Do not write 'Any' or 'Adjustable'.</small>
                        @error('partner_complexion') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Height Range -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Preferred Partner Height (in feet) *</label>
                        <div class="d-flex gap-2">
                            @php
                            $heights = [
                            '4.5','4.6','4.7','4.8','4.9',
                            '5.0','5.1','5.2','5.3','5.4','5.5',
                            '5.6','5.7','5.8','5.9',
                            '6.0','6.1','6.2','6.3','6.4','6.5'
                            ];
                            $selectedRange = old('partner_height', $biodata['step_8']['partner_height'] ?? '');
                            $fromSelected = explode('-', $selectedRange)[0] ?? '';
                            $toSelected = explode('-', $selectedRange)[1] ?? '';
                            @endphp

                            <select id="height-from" class="form-select">
                                <option value="">From</option>
                                @foreach($heights as $height)
                                <option value="{{ $height }}" {{ $fromSelected==$height ? 'selected' : '' }}>{{ $height }}</option>
                                @endforeach
                            </select>

                            <select id="height-to" class="form-select">
                                <option value="">To</option>
                                @foreach($heights as $height)
                                <option value="{{ $height }}" {{ $toSelected==$height ? 'selected' : '' }}>{{ $height }}</option>
                                @endforeach
                            </select>

                            <!-- Hidden input to store combined value -->
                            <input type="hidden" name="partner_height" id="partner-height" value="{{ $selectedRange }}">
                        </div>
                        <small class="text-muted">Select the height range.</small>
                        @error('partner_height') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <script>
                        const fromSelect = document.getElementById('height-from');
                        const toSelect = document.getElementById('height-to');
                        const hiddenInput = document.getElementById('partner-height');

                        function updateHeight() {
                            if (fromSelect.value && toSelect.value) {
                                hiddenInput.value = fromSelect.value + '-' + toSelect.value;
                            } else {
                                hiddenInput.value = '';
                            }
                        }

                        fromSelect.addEventListener('change', updateHeight);
                        toSelect.addEventListener('change', updateHeight);
                    </script>


                    <!-- Education -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Educational Qualification *</label>
                        <select name="partner_education" class="form-select">
                            <option value="">-- Select Qualification --</option>
                            @php
                            $educationOptions = [
                            'SSC',
                            'HSC',
                            'Diploma',
                            'Graduation',
                            'Post Graduation',
                            'Hafez',
                            'Others'
                            ];
                            $selected = old('partner_education', $biodata['step_8']['partner_education'] ?? '');
                            @endphp
                            @foreach($educationOptions as $option)
                            <option value="{{ $option }}" {{ $selected==$option ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                            @endforeach
                        </select>
                        @error('partner_education')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- District -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">District *</label>
                        <select name="partner_district" id="partner_district" class="form-select">
                            <option value="">-- Select District --</option>
                        </select>
                        <small class="text-muted">Mention specific districts.</small>
                        @error('partner_district')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            fetch("{{ asset('json/districts.json') }}")
                                .then(response => response.json())
                                .then(data => {
                                    const districtSelect = document.getElementById('partner_district');
                                    const oldDistrict = "{{ old('partner_district', $biodata['step_8']['partner_district'] ?? '') }}";

                                    data.forEach(district => {
                                        const option = document.createElement('option');
                                        option.value = district.name; // use "name" or "bn_name" if you want Bangla
                                        option.text = district.name; // display text
                                        if (district.name === oldDistrict) {
                                            option.selected = true;
                                        }
                                        districtSelect.appendChild(option);
                                    });
                                })
                                .catch(error => console.error('Error loading districts:', error));
                        });
                    </script>




                    <!-- Marital Status -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Marital Status *</label>
                        <select name="partner_marital_status[]" class="form-select" multiple>
                            @php
                            $maritalOptions = ['Never Married', 'Divorced', 'Widow'];
                            $selected = old('partner_marital_status', $biodata['step_8']['partner_marital_status'] ?? []);
                            @endphp
                            @foreach($maritalOptions as $option)
                            <option value="{{ $option }}" {{ in_array($option, $selected) ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">You may select multiple options.</small>
                        @error('partner_marital_status')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    <!-- Profession -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Profession *</label>
                        <select name="partner_profession" class="form-select">
                            <option value="">-- Select Profession --</option>
                            @php
                            $professions = [
                            'Engineer',
                            'Doctor',
                            'Teacher',
                            'Business',
                            'Government Employee',
                            'Private Job',
                            'Farmer',
                            'Others'
                            ];
                            $selected = old('partner_profession', $biodata['step_8']['partner_profession'] ?? '');
                            @endphp
                            @foreach($professions as $profession)
                            <option value="{{ $profession }}" {{ $selected==$profession ? 'selected' : '' }}>
                                {{ $profession }}
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Do not write 'Any' or 'Adjustable'.</small>
                        @error('partner_profession')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    <!-- Financial Condition -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Financial Condition *</label>
                        <select name="partner_financial_condition" class="form-select">
                            <option value="">-- Select Financial Condition --</option>
                            @php
                            $financialOptions = [
                            'Poor',
                            'Average',
                            'Good',
                            'Very Good',
                            'Wealthy'
                            ];
                            $selected = old('partner_financial_condition', $biodata['step_8']['partner_financial_condition'] ?? '');
                            @endphp
                            @foreach($financialOptions as $option)
                            <option value="{{ $option }}" {{ $selected==$option ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Be specific about financial status.</small>
                        @error('partner_financial_condition')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    <!-- Expected Qualities -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Expected qualities or attributes *</label>
                        <textarea name="partner_expectations" class="form-control" rows="4">{{ old('partner_expectations', $biodata['step_8']['partner_expectations'] ?? '') }}</textarea>
                        @error('partner_expectations') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                @endif


                <!-- Step 9:  Pledge -->
                @if($step == 9)
                <div class="step-content active" data-step="9">
                    <!-- Step Header with Progress -->
                    <div class="step-header">
                        <h2>Step {{ $step }} of 10</h2>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($step/10)*100 }}%;"></div>
                        </div>
                    </div>
                    <h4>Pledge</h4>

                    <!-- Parents Knowledge -->
                    <div class="mb-3">
                        <label>Do your parents know that you are submitting biodata ? *</label>
                        <input type="text" name="parents_know" class="form-control" value="{{ old('parents_know', $biodata['step_9']['parents_know'] ?? '') }}">
                        @error('parents_know') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Truth Confirmation -->
                    <div class="mb-3">
                        <label>I confirm that all the information provided is true. *</label>
                        <input type="text" name="truth_testify" class="form-control" value="{{ old('truth_testify', $biodata['step_9']['truth_testify'] ?? '') }}">
                        @error('truth_testify') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Responsibility Agreement -->
                    <div class="mb-3">
                        <label>If any information is false, we are not responsible. Do you agree? *</label>
                        <input type="text" name="responsibility" class="form-control" value="{{ old('responsibility', $biodata['step_9']['responsibility'] ?? '') }}">
                        @error('responsibility') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                @endif

                <!-- Step 10:  Contact -->
                @if($step == 10)
                <div class="step-content active" data-step="10">
                    <!-- Step Header with Progress -->
                    <div class="step-header">
                        <h2>Step {{ $step }} of 10</h2>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($step/10)*100 }}%;"></div>
                        </div>
                    </div>
                    <h4>Contact</h4>

                    <!-- Guardian Mobile -->
                    <div class="mb-3">
                        <label>Guardian's Mobile Number *</label>
                        <input type="text" name="guardian_mobile" class="form-control" value="{{ old('guardian_mobile', $biodata['step_10']['guardian_mobile'] ?? '') }}">
                        <small class="form-text text-muted">
                            This number will be given if anyone wants to contact your guardian. After verifying by calling this number, the biodata will be approved.
                            If you write the number of your friend, colleague, cousin or yourself here, biodata will be permanently banned.
                        </small>
                        @error('guardian_mobile') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Guardian Relation -->
                    <div class="mb-3">
                        <label>Relationship with Guardian *</label>
                        <input type="text" name="guardian_relationship" class="form-control" value="{{ old('guardian_relationship', $biodata['step_10']['guardian_relationship'] ?? '') }}">
                        @error('guardian_relationship') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label>Email to Receive Biodata *</label>
                        <input type="email" name="guardian_email" class="form-control" value="{{ old('guardian_email', $biodata['step_10']['guardian_email'] ?? '') }}">
                        <small class="form-text text-muted">
                            To avoid unwanted incidents, enter the guardian's guardian_email address if possible.
                        </small>
                        @error('guardian_email') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>


                </div>
                @endif

                @php
                $step = $step ?? 1;
                $maxStep = $maxStep ?? 10;
                @endphp
                <!-- Step navigation buttons -->
                <div class="d-flex justify-content-between mt-3">
                    @if($step > 1)
                    <button type="submit" name="back" value="1" class="btn btn-secondary">Back</button>
                    @endif

                    @if($step < $maxStep) <button type="submit" value="1" name="next" class="btn btn-primary">Next</button>
                        @else
                        <button type="submit" class="btn btn-success">Finish</button>
                        @endif
                </div>

                <input type="hidden" name="step" id="currentStep" value="{{ $step }}">


            </form>
        </div>
    </div>
</div>


{{-- JS --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const steps = document.querySelectorAll(".step");
        const stepButtons = document.querySelectorAll(".step-btn"); // clickable step headers (divs)
        const stepInput = document.getElementById("currentStep");
        const form = document.querySelector("form");

        if (!form || !stepInput || steps.length === 0) {
            console.warn("Stepper elements missing.");
            return;
        }

        // current step helper
        function getCurrentStep() {
            return parseInt(stepInput.value) || 1;
        }

        function setActiveStep(stepNumber) {
            steps.forEach(step => {
                step.classList.remove("active");
                if (parseInt(step.dataset.step) <= stepNumber) {
                    step.classList.add("active");
                }
            });
        }

        // initialize UI
        setActiveStep(getCurrentStep());

        // keep track of last user action so submit handler knows what happened
        let lastAction = null; // "step", "next", "back" or null

        // STEP HEADER CLICK -> immediate move (no validation)
        stepButtons.forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                const goto = parseInt(this.dataset.step);
                if (isNaN(goto)) return;

                lastAction = "step";
                stepInput.value = goto;
                setActiveStep(goto);

                // Submit to let server render that step (no validation required on client)
                form.submit();
            });
        });

        // find next/back buttons (they are <button name="next"> and <button name="back">)
        const nextBtn = form.querySelector("button[name='next']");
        const backBtn = form.querySelector("button[name='back']");

        if (nextBtn) {
            nextBtn.addEventListener("click", function(e) {
                // Do NOT change step here — server must validate current step.
                lastAction = "next";
                // Ensure hidden step stays current step (so server validates this step)
                stepInput.value = getCurrentStep();
                // allow normal submit to proceed
            });
        }

        if (backBtn) {
            backBtn.addEventListener("click", function(e) {
                lastAction = "back";
                // decrement step so server knows to move back
                stepInput.value = Math.max(1, getCurrentStep() - 1);
                // allow normal submit to proceed
            });
        }

        // Fallback: user might submit form with Enter key (no button click). Try to detect intent.
        form.addEventListener("submit", function(e) {
            // If lastAction set by click handlers, respect it
            if (lastAction === "step" || lastAction === "next" || lastAction === "back") {
                // lastAction already set and stepInput already updated by the click handlers
                // Clear lastAction after allowing submission
                setTimeout(() => {
                    lastAction = null;
                }, 0);
                return; // allow submit
            }

            // Otherwise try to detect the focused element at submit time
            const active = document.activeElement;
            if (active && active.tagName) {
                const name = active.getAttribute && active.getAttribute("name");
                if (name === "back") {
                    // user pressed Enter while focused on Back
                    stepInput.value = Math.max(1, getCurrentStep() - 1);
                    lastAction = "back";
                    return;
                }
                if (name === "next") {
                    stepInput.value = getCurrentStep(); // keep same for validation
                    lastAction = "next";
                    return;
                }
            }

            // As a safe default: treat plain submit as "next" (server should handle validation)
            stepInput.value = getCurrentStep();
            lastAction = "next";
            // clear state after submit (async)
            setTimeout(() => {
                lastAction = null;
            }, 0);
        });
    });


    // Basic Information

    // Combine day, month, year into YYYY-MM-DD
    function updateBirthDate() {
        const d = document.getElementById('day').value;
        const m = document.getElementById('month').value;
        const y = document.getElementById('year').value;

        if (d && m && y) {
            const formatted = `${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
            document.getElementById('birth_date').value = formatted;
        }
    }

    document.getElementById('day').addEventListener('change', updateBirthDate);
    document.getElementById('month').addEventListener('change', updateBirthDate);
    document.getElementById('year').addEventListener('change', updateBirthDate);








    // Family Information
    document.addEventListener("DOMContentLoaded", function() {
        const brothersInput = document.getElementById("brothers");
        const brothersInfoBox = document.getElementById("brothers_info_box");

        const sistersInput = document.getElementById("sisters");
        const sistersInfoBox = document.getElementById("sisters_info_box");

        // Show/hide brothers info
        brothersInput.addEventListener("input", function() {
            if (parseInt(this.value) > 0) {
                brothersInfoBox.classList.remove("d-none");
            } else {
                brothersInfoBox.classList.add("d-none");
            }
        });

        // Show/hide sisters info
        sistersInput.addEventListener("input", function() {
            if (parseInt(this.value) > 0) {
                sistersInfoBox.classList.remove("d-none");
            } else {
                sistersInfoBox.classList.add("d-none");
            }
        });
    });

    //Personal Information
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector("form");
        const stepInput = document.getElementById("currentStep");

        if (!form || !stepInput) {
            console.warn("Step form or hidden input not found in DOM.");
            return;
        }

        form.addEventListener("submit", function(e) {
            const currentStep = stepInput.value;

            // Validation only for Step 5
            if (currentStep === "5") {
                const groomPhoto = document.querySelector('input[name="groom_photo"]');
                if (!groomPhoto || !groomPhoto.value) {
                    e.preventDefault(); // stop form submit
                    alert("Please upload the groom's photo before continuing.");
                    return;
                }
            }
        });
    });
</script>
@endsection