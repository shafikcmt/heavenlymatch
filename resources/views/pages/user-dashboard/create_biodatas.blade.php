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
    border-left: 6px solid #6f42c1; /* prominent left border */
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
    padding-left: 45px; /* space for small circle */
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
    left: -18px; /* smaller offset for small circle */
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
    top: 20px; /* start below the small circle */
    width: 3px;
    height: calc(100% - 20px);
    background: #e0e0e0;
    border-radius: 2px;
    z-index: 1;
  }

  /* Step label */
  .step .label {
    font-weight: 600;
    font-size: 15px;
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
    box-shadow: 0 5px 15px rgba(0,0,0,0.06);
    transition: all 0.3s ease-in-out;
  }

  .step-content:hover {
    box-shadow: 0 8px 22px rgba(0,0,0,0.12);
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
    box-shadow: 0 0 6px rgba(111,66,193,0.25);
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

  .btn-primary { background: #6f42c1; border: none; color: #fff; }
  .btn-primary:hover { background: #e83e8c; box-shadow: 0 6px 14px rgba(232,62,140,0.25); }

  .btn-secondary { background: #6c757d; border: none; color: #fff; }
  .btn-secondary:hover { background: #5a6268; }

  .btn-success { background: #198754; border: none; color: #fff; }
  .btn-success:hover { background: #157347; }

  .btn-warning { background: #ffc107; border: none; color: #212529; }
  .btn-warning:hover { background: #e0a800; }
</style>

@endpush

@section('content')

@php
$step = $step ?? 1; // if $step is not set, use 1
@endphp

<div class="container py-4">
    <div class="row">
        <!-- Stepper Navigation -->
        <div class="col-md-4">
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
                    <div class="label">Educational Qualifications</div>
                </div>
                <div class="step" data-step="4">
                    <div class="circle">4</div>
                    <div class="label">Family Information</div>
                </div>
                <div class="step" data-step="5">
                    <div class="circle">5</div>
                    <div class="label">Personal Information</div>
                </div>
                <div class="step" data-step="6">
                    <div class="circle">6</div>
                    <div class="label">Occupational Information</div>
                </div>
                <div class="step" data-step="7">
                    <div class="circle">7</div>
                    <div class="label">Marriage Related Information</div>
                </div>
                <div class="step" data-step="8">
                    <div class="circle">8</div>
                    <div class="label">Expected Life Partner</div>
                </div>
                <div class="step" data-step="9">
                    <div class="circle">9</div>
                    <div class="label">Pledge</div>
                </div>
                <div class="step" data-step="10">
                    <div class="circle">10</div>
                    <div class="label">Contact</div>
                </div>
            </div>
        </div>

        <!-- Form Content -->

        <div class="col-md-8">

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
                    <h4>General Info</h4>

                    <div class="mb-3">
                        <label>Marital Status *</label>
                        <select name="marital_status" class="form-select">
                            <option value="">Select</option>
                            <option {{ old('marital_status', $biodata['step_1']['marital_status'] ?? '' )=='Never Married' ? 'selected' : '' }}>Never Married</option>
                            <option {{ old('marital_status', $biodata['step_1']['marital_status'] ?? '' )=='Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option {{ old('marital_status', $biodata['step_1']['marital_status'] ?? '' )=='Widow' ? 'selected' : '' }}>Widow</option>
                        </select>
                        @error('marital_status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Birth Date *</label>
                        <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $biodata['step_1']['birth_date'] ?? '') }}">
                        @error('birth_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Height *</label>
                            <input type="text" name="height" class="form-control" value="{{ old('height', $biodata['step_1']['height'] ?? '') }}">
                            @error('height') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Weight *</label>
                            <input type="text" name="weight" class="form-control" value="{{ old('weight', $biodata['step_1']['weight'] ?? '') }}">
                            @error('weight') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Complexion *</label>
                        <select name="complexion" class="form-select">
                            <option value="">Select</option>
                            <option {{ old('complexion', $biodata['step_1']['complexion'] ?? '' )=='Fair' ? 'selected' : '' }}>Fair</option>
                            <option {{ old('complexion', $biodata['step_1']['complexion'] ?? '' )=='Brown' ? 'selected' : '' }}>Brown</option>
                            <option {{ old('complexion', $biodata['step_1']['complexion'] ?? '' )=='Dark' ? 'selected' : '' }}>Dark</option>
                        </select>
                        @error('complexion') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Blood Group *</label>
                        <select name="blood_group" class="form-select">
                            <option value="">Select</option>
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

                    <div class="mb-3">
                        <label>Nationality</label>
                        <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $biodata['step_1']['nationality'] ?? 'Bangladeshi') }}">
                        @error('nationality') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                @endif

                <!-- Step 2: Address -->
                @if($step == 2)
                <div class="step-content active" data-step="2">
                     <!-- Step Header with Progress -->
                    <div class="step-header">
                        <h2>Step {{ $step }} of 10</h2>
                        <div class="progress">
                        <div class="progress-bar" style="width: {{ ($step/10)*100 }}%;"></div>
                        </div>
                    </div>
                    <h4>Address</h4>

                    <div class="mb-3">
                        <label>Country *</label>
                        <select id="country" class="form-select" required>
                            <option value="Bangladesh">Bangladesh</option>
                        </select>
                        <small class="form-text text-muted">Select your country</small>
                    </div>

                    <!-- Present Address -->
                    <div class="mb-3">
                        <label>Present Address *</label>
                        <select id="present_address_select" class="form-select" required></select>
                        <small class="form-text text-muted">Select Division → District → Upazila</small>
                        @error('present_address') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Hidden inputs -->
                    <input type="hidden" id="present_address" name="present_address">
                    <input type="hidden" id="permanent_address" name="permanent_address">

                    <!-- Village / Area -->
                    <div class="mb-3">
                        <label class="form-label">Village / Area</label>
                        <input type="text" name="village_area" class="form-control" value="{{ old('village_area', $biodata['step_2']['village_area'] ?? '') }}" placeholder="e.g., South Vabanipur">
                        @error('village_area')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Permanent Address -->
                    <div class="mb-3">
                        <label>Permanent Address *</label>
                        <div class="form-check mb-2">
                            <input type="checkbox" id="same_as_present" class="form-check-input">
                            <label class="form-check-label" for="same_as_present">Same as Present Address</label>
                        </div>
                        <select id="permanent_address_select" class="form-select" required></select>
                        <small class="form-text text-muted">Select Division → District → Upazila</small>
                        @error('permanent_address') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <!-- Where did you grow up -->
                    <div class="mb-3">
                        <label class="form-label">Where did you grow up? *</label>
                        <select name="grew_up" class="form-select" required>
                            <option value="">-- Select District --</option>
                            @foreach(['Dhaka','Chattogram','Barishal','Jhalakathi','Sylhet','Khulna','Rajshahi','Mymensingh'] as $district)
                            <option value="{{ $district }}" {{ old('grew_up', $biodata['step_2']['grew_up'] ?? '' )==$district ? 'selected' : '' }}>
                                {{ $district }}
                            </option>
                            @endforeach
                        </select>
                        @error('grew_up')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                @endif

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

                    <!-- Education Method -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Your Education Method *</label>
                        <select id="education-method" name="education_method" class="form-select" required>
                            <option value="">-- Select --</option>
                            @foreach(['General','Islamic','Both'] as $method)
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
                    <div class="mb-3">
                        <label class="form-label fw-bold">Highest Educational Qualification *</label>
                        <small class="text-muted d-block mb-1">Select your top qualification. You can add more later.</small>
                        <div id="education-container">
                            @php
                            $education_types = old('education_type', $biodata['step_3']['education_type'] ?? ['']);
                            @endphp
                            @foreach($education_types as $index => $type)
                            <div class="education-block mb-4 border p-3 rounded shadow-sm bg-light">
                                <div class="mb-3">
                                    <select name="education_type[]" class="form-select education-select" required>
                                        <option value="">-- Select --</option>
                                        @foreach(['SSC','HSC','Diploma','Graduation','Post Graduation','Hafez','Others'] as $option)
                                        <option value="{{ $option }}" {{ $type==$option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="education-fields">
                                    <!-- You can dynamically add fields like year, institution, subject here -->
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" id="add-education" class="btn btn-success btn-sm">+ Add Another Qualification</button>
                        @error('education_type.*')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Other Educational Qualifications -->
                    <div class="mb-3">
                        <label for="other_education" class="form-label fw-bold">Other educational qualifications *</label>
                        <input type="text" class="form-control" id="other_education" name="other_education" placeholder="Enter other qualifications" value="{{ old('other_education', $biodata['step_3']['other_education'] ?? '') }}" required>
                        @error('other_education')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                @endif

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
                        <input type="text" name="father_name" class="form-control" placeholder="Abdul Haque" value="{{ old('father_name', $biodata['step_4']['father_name'] ?? '') }}" required>
                        <small class="text-muted">Only for authority</small>
                        @error('father_name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Is your father alive? *</label>
                        <select name="father_alive" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="1" {{ old('father_alive', $biodata['step_4']['father_alive'] ?? '' )=='1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('father_alive', $biodata['step_4']['father_alive'] ?? '' )=='0' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('father_alive') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Father's Profession *</label>
                        <textarea name="father_profession" class="form-control" rows="2" required placeholder="Businessman, Teacher, etc.">{{ old('father_profession', $biodata['step_4']['father_profession'] ?? '') }}</textarea>
                        @error('father_profession') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Mother Info -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mother's Name *</label>
                        <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name', $biodata['step_4']['mother_name'] ?? '') }}" required>
                        @error('mother_name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Is your mother alive? *</label>
                        <select name="mother_alive" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="1" {{ old('mother_alive', $biodata['step_4']['mother_alive'] ?? '' )=='1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('mother_alive', $biodata['step_4']['mother_alive'] ?? '' )=='0' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('mother_alive') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mother's Profession *</label>
                        <textarea name="mother_profession" class="form-control" rows="2" required placeholder="Housewife, Teacher, etc.">{{ old('mother_profession', $biodata['step_4']['mother_profession'] ?? '') }}</textarea>
                        @error('mother_profession') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Brothers -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">How many brothers do you have? *</label>
                        <input type="number" name="brothers" id="brothers" class="form-control" min="0" value="{{ old('brothers', $biodata['step_4']['brothers'] ?? 0) }}" required>
                        @error('brothers') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 {{ old('brothers', $biodata['step_4']['brothers'] ?? 0) > 0 ? '' : 'd-none' }}" id="brothers_info_box">
                        <label class="form-label fw-bold">Brothers Information</label>
                        <textarea name="brothers_info" class="form-control" rows="3" placeholder="Educational qualifications, marital status, occupation. Separate multiple brothers with commas.">{{ old('brothers_info', $biodata['step_4']['brothers_info'] ?? '') }}</textarea>
                    </div>

                    <!-- Sisters -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">How many sisters do you have? *</label>
                        <input type="number" name="sisters" id="sisters" class="form-control" min="0" value="{{ old('sisters', $biodata['step_4']['sisters'] ?? 0) }}" required>
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
                        <select name="family_financial_status" class="form-select" required>
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
                        <textarea name="family_details" class="form-control" rows="3" required placeholder="Residential house, land, family business, etc.">{{ old('family_details', $biodata['step_4']['family_details'] ?? '') }}</textarea>
                        @error('family_details') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Religious Condition -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Family's Religious Condition *</label>
                        <textarea name="family_religious_condition" class="form-control" rows="3" required placeholder="Describe family religious practices, environment of mahram & non-mahram, etc.">{{ old('family_religious_condition', $biodata['step_4']['family_religious_condition'] ?? '') }}</textarea>
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

                    <!-- Beard -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Do you have beard according to sunnah? Since when? *</label>
                        <input type="text" name="beard_info" class="form-control" placeholder="e.g., 5 years" value="{{ old('beard_info', $biodata['step_5']['beard_info'] ?? '') }}">
                        @error('beard_info') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Clothes above ankles -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Do you wear clothes above the ankles? *</label>
                        <select name="clothes_above_ankles" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="1" {{ old('clothes_above_ankles', $biodata['step_5']['clothes_above_ankles'] ?? '' )=='1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('clothes_above_ankles', $biodata['step_5']['clothes_above_ankles'] ?? '' )=='0' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('clothes_above_ankles') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Prayer -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Do you pray five times a day? How many times a week are missed (Qaza)? *</label>
                        <input type="text" name="prayers_info" class="form-control" placeholder="Yes/No, Qaza times per week" value="{{ old('prayers_info', $biodata['step_5']['prayers_info'] ?? '') }}">
                        @error('prayers_info') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Mahram Compliance -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Do you comply with mahram / non-mahram? *</label>
                        <select name="mahram_nonmahram" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="1" {{ old('mahram_nonmahram', $biodata['step_5']['mahram_nonmahram'] ?? '' )=='1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('mahram_nonmahram', $biodata['step_5']['mahram_nonmahram'] ?? '' )=='0' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('mahram_nonmahram') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
    <label class="form-label fw-bold">Are you able to read the Quran correctly? *</label>
    <select name="quran_recitation" class="form-select" required>
        <option value="">-- Select --</option>
        <option value="Yes" {{ old('quran_recitation', $biodata['step_5']['quran_recitation'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
        <option value="No" {{ old('quran_recitation', $biodata['step_5']['quran_recitation'] ?? '') == 'No' ? 'selected' : '' }}>No</option>
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
                        <label class="form-label fw-bold">Do you watch or listen to dramas / movies / serials / songs? *</label>
                        <select name="watch_entertainment" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="1" {{ old('watch_entertainment', $biodata['step_5']['watch_entertainment'] ?? '' )=='1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('watch_entertainment', $biodata['step_5']['watch_entertainment'] ?? '' )=='0' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('watch_entertainment') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Health -->
<div class="mb-3">
    <label class="form-label fw-bold">Do you have any mental or physical diseases? *</label>
    <select name="diseases" class="form-select" required>
        <option value="">-- Select --</option>
        <option value="Yes" {{ old('diseases', $biodata['step_5']['diseases'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
        <option value="No" {{ old('diseases', $biodata['step_5']['diseases'] ?? '') == 'No' ? 'selected' : '' }}>No</option>
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
                                    <input class="form-check-input" type="checkbox" name="special_category[]" value="{{ $category }}" 
                                        {{ in_array($category, $selected) ? 'checked' : '' }}>
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

                    <!-- Mobile -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Groom's mobile number *</label>
                        <input type="text" name="groom_mobile" class="form-control" value="{{ old('groom_mobile', $biodata['step_5']['groom_mobile'] ?? '') }}" placeholder="01768987779">
                        @error('groom_mobile') <small class="text-danger">{{ $message }}</small> @enderror
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
                        <select name="guardian_agree" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="Yes" {{ old('guardian_agree', $biodata['step_7']['guardian_agree'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No"  {{ old('guardian_agree', $biodata['step_7']['guardian_agree'] ?? '') == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('guardian_agree')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                   <!-- Wife in Veil -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Will you be able to keep your wife in the veil after marriage? *</label>
                        <select name="wife_in_veil" class="form-select" required>
                            <option value="">-- Select --</option>
                            @php
                                $options = ['Yes', 'No', 'InshaAllah'];
                                $selected = old('wife_in_veil', $biodata['step_7']['wife_in_veil'] ?? '');
                            @endphp
                            @foreach($options as $option)
                                <option value="{{ $option }}" {{ $selected == $option ? 'selected' : '' }}>
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
                        <select name="wife_study_allowed" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="Yes" {{ old('wife_study_allowed', $biodata['step_7']['wife_study_allowed'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('wife_study_allowed', $biodata['step_7']['wife_study_allowed'] ?? '') == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('wife_study_allowed')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Wife Job Allowed -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Would you like to allow your wife to do any job after marriage? *</label>
                        <select name="wife_job_allowed" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="Yes" {{ old('wife_job_allowed', $biodata['step_7']['wife_job_allowed'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('wife_job_allowed', $biodata['step_7']['wife_job_allowed'] ?? '') == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                        @error('wife_job_allowed')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Residence After Marriage -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Where will you live with your wife after marriage? *</label>
                        <select name="residence_after_marriage" class="form-select" required>
                            <option value="">-- Select --</option>
                            @foreach(['Own House', 'Wife’s House', 'Rented House', 'Other'] as $option)
                                <option value="{{ $option }}" {{ old('residence_after_marriage', $biodata['step_7']['residence_after_marriage'] ?? '') == $option ? 'selected' : '' }}>
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
                        <select name="expect_gift_from_bride" class="form-select" required>
                            <option value="">-- Select --</option>
                            @foreach(['Yes', 'No'] as $option)
                                <option value="{{ $option }}" {{ old('expect_gift_from_bride', $biodata['step_7']['expect_gift_from_bride'] ?? '') == $option ? 'selected' : '' }}>
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

                            <select id="age-from" class="form-select" required>
                                <option value="">From</option>
                                @for($from = 18; $from <= 55; $from++)
                                    <option value="{{ $from }}" {{ $ageFromValue == $from ? 'selected' : '' }}>{{ $from }}</option>
                                @endfor
                            </select>

                            <select id="age-to" class="form-select" required>
                                <option value="">To</option>
                                @for($to = 23; $to <= 60; $to++) <!-- minimum 5-year gap -->
                                    <option value="{{ $to }}" {{ $ageToValue == $to ? 'selected' : '' }}>{{ $to }}</option>
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
                            if(ageFromSelect.value && ageToSelect.value) {
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
                                    <input class="form-check-input" type="checkbox" name="partner_complexion[]" 
                                        value="{{ $option }}" 
                                        id="complexion_{{ $loop->index }}"
                                        {{ in_array($option, old('partner_complexion', $biodata['step_8']['partner_complexion'] ?? [])) ? 'checked' : '' }}>
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
                                    $toSelected   = explode('-', $selectedRange)[1] ?? '';
                                @endphp

                                <select id="height-from" class="form-select" required>
                                    <option value="">From</option>
                                    @foreach($heights as $height)
                                        <option value="{{ $height }}" {{ $fromSelected == $height ? 'selected' : '' }}>{{ $height }}</option>
                                    @endforeach
                                </select>

                                <select id="height-to" class="form-select" required>
                                    <option value="">To</option>
                                    @foreach($heights as $height)
                                        <option value="{{ $height }}" {{ $toSelected == $height ? 'selected' : '' }}>{{ $height }}</option>
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
                                if(fromSelect.value && toSelect.value) {
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
                        <select name="partner_education" class="form-select" required>
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
                                <option value="{{ $option }}" {{ $selected == $option ? 'selected' : '' }}>
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
                        <select name="partner_district" id="partner_district" class="form-select" required>
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
                                        option.value = district.name;  // use "name" or "bn_name" if you want Bangla
                                        option.text = district.name;   // display text
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
                        <select name="partner_marital_status[]" class="form-select" multiple required>
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
                        <select name="partner_profession" class="form-select" required>
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
                                <option value="{{ $profession }}" {{ $selected == $profession ? 'selected' : '' }}>
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
                            <select name="partner_financial_condition" class="form-select" required>
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
                                    <option value="{{ $option }}" {{ $selected == $option ? 'selected' : '' }}>
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
                        <input type="text" name="parents_know" class="form-control" value="{{ old('parents_know', $biodata['step_9']['parents_know'] ?? '') }}" required>
                        @error('parents_know') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Truth Confirmation -->
                    <div class="mb-3">
                        <label>I confirm that all the information provided is true. *</label>
                        <input type="text" name="truth_testify" class="form-control" value="{{ old('truth_testify', $biodata['step_9']['truth_testify'] ?? '') }}" required>
                        @error('truth_testify') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Responsibility Agreement -->
                    <div class="mb-3">
                        <label>If any information is false, we are not responsible. Do you agree? *</label>
                        <input type="text" name="responsibility" class="form-control" value="{{ old('responsibility', $biodata['step_9']['responsibility'] ?? '') }}" required>
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

                    <!-- Groom Name -->
                    <div class="mb-3">
                        <label>Groom's Name *</label>
                        <input type="text" name="groom_name" class="form-control" value="{{ old('groom_name', $biodata['step_10']['groom_name'] ?? '') }}" required>
                        <small class="form-text text-muted">Enter your full name</small>
                        @error('groom_name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Guardian Mobile -->
                    <div class="mb-3">
                        <label>Guardian's Mobile Number *</label>
                        <input type="text" name="guardian_mobile" class="form-control" value="{{ old('guardian_mobile', $biodata['step_10']['guardian_mobile'] ?? '') }}" required>
                        <small class="form-text text-muted">
                            This number will be given if anyone wants to contact your guardian. After verifying by calling this number, the biodata will be approved.
                            If you write the number of your friend, colleague, cousin or yourself here, biodata will be permanently banned.
                        </small>
                        @error('guardian_mobile') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Guardian Relation -->
                    <div class="mb-3">
                        <label>Relationship with Guardian *</label>
                        <input type="text" name="guardian_relationship" class="form-control" value="{{ old('guardian_relationship', $biodata['step_10']['guardian_relationship'] ?? '') }}" required>
                        @error('guardian_relationship') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label>Email to Receive Biodata *</label>
                        <input type="email" name="guardian_email" class="form-control" value="{{ old('guardian_email', $biodata['step_10']['guardian_email'] ?? '') }}" required>
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
                        <button type="submit" value="1" class="btn btn-warning">Save Temporarily</button>
                </div>

                <input type="hidden" name="step" id="currentStep" value="{{ $step }}">

            </form>
        </div>
    </div>
</div>


{{-- JS --}}
<script>
   

    document.addEventListener("DOMContentLoaded", function() {
        const stepButtons = document.querySelectorAll(".step-btn");
        const stepInput = document.getElementById("currentStep");
        const form = document.querySelector("form");

        if (!form || !stepInput || stepButtons.length === 0) {
            console.warn("Step form or buttons not found in DOM.");
            return; // stop running if not found
        }

        stepButtons.forEach(btn => {
            btn.addEventListener("click", function() {
                const newStep = this.dataset.step;
                stepInput.value = newStep;
                form.submit();
            });
        });
    });


    // Address data
    document.addEventListener("DOMContentLoaded", function() {

        Promise.all([
            fetch('{{ asset("json/divisions.json") }}').then(r => r.json()),
            fetch('{{ asset("json/districts.json") }}').then(r => r.json()),
            fetch('{{ asset("json/upazilas.json") }}').then(r => r.json())
        ]).then(([divisions, districts, upazilas]) => {

            function setupSingleSelect(selectId, hiddenInputId) {
                const select = document.getElementById(selectId);
                const hiddenInput = document.getElementById(hiddenInputId);

                let currentStep = "division";
                let selectedDivision = null;
                let selectedDistrict = null;

                function loadDivisions() {
                    currentStep = "division";
                    select.innerHTML = '<option value="">-- Select Division --</option>' +
                        divisions.map(d => `<option value="${d.id}">${d.name}</option>`).join('');
                }

                function loadDistricts(divisionId) {
                    currentStep = "district";
                    const filtered = districts.filter(d => d.division_id == divisionId);
                    select.innerHTML = '<option value="">-- Select District --</option>' +
                        filtered.map(d => `<option value="${d.id}">${d.name}</option>`).join('');
                }

                function loadUpazilas(districtId) {
                    currentStep = "upazila";
                    const filtered = upazilas.filter(u => u.district_id == districtId);
                    select.innerHTML = '<option value="">-- Select Upazila --</option>' +
                        filtered.map(u => `<option value="${u.id}">${u.name}</option>`).join('');
                }

                select.addEventListener("change", function() {
                    if (currentStep === "division") {
                        selectedDivision = divisions.find(d => d.id == this.value);
                        if (selectedDivision) loadDistricts(selectedDivision.id);

                    } else if (currentStep === "district") {
                        selectedDistrict = districts.find(d => d.id == this.value);
                        if (selectedDistrict) loadUpazilas(selectedDistrict.id);

                    } else if (currentStep === "upazila") {
                        const selectedUpa = upazilas.find(u => u.id == this.value);
                        if (selectedUpa && selectedDistrict && selectedDivision) {
                            hiddenInput.value = `Bangladesh, ${selectedUpa.name}, ${selectedDistrict.name}, ${selectedDivision.name}`;
                            select.innerHTML = `<option value="${hiddenInput.value}">${hiddenInput.value}</option>`;
                            select.value = hiddenInput.value;
                        }
                    }
                });

                loadDivisions();
            }

            setupSingleSelect("present_address_select", "present_address");
            setupSingleSelect("permanent_address_select", "permanent_address");

            const checkbox = document.getElementById("same_as_present");
            const permSelect = document.getElementById("permanent_address_select");
            const permHidden = document.getElementById("permanent_address");

            checkbox.addEventListener("change", function() {
                if (this.checked) {
                    permSelect.style.display = "none";
                    permHidden.value = document.getElementById("present_address").value;
                } else {
                    permSelect.style.display = "block";
                    permHidden.value = "";
                }
            });

        }).catch(err => console.error("Error loading JSON:", err));

    });



    // Education Information 
    document.addEventListener("DOMContentLoaded", function() {
        const generalOptions = [{
                value: "ssc",
                text: "SSC / Dakhil"
            },
            {
                value: "diploma",
                text: "Diploma"
            },
            {
                value: "bachelor",
                text: "Bachelor"
            },
            {
                value: "master",
                text: "Master"
            },
            {
                value: "other",
                text: "Other"
            }
        ];

        const islamicOptions = [{
                value: "hafez",
                text: "Hafez"
            },
            {
                value: "maolana",
                text: "Maolana"
            },
            {
                value: "mufti",
                text: "Mufti"
            },
            {
                value: "mufassir",
                text: "Mufassir"
            },
            {
                value: "adib",
                text: "Adib"
            },
            {
                value: "qari",
                text: "Qari"
            }
        ];

        // Generate input fields based on selected qualification
        function getFields(type) {
            switch (type) {
                case "ssc":
                    return `
                    <div class="mb-3">
                        <label>Passing Year *</label>
                        <input type="text" name="ssc_year[]" class="form-control" placeholder="2014" required>
                    </div>
                    <div class="mb-3">
                        <label>Group *</label>
                        <input type="text" name="ssc_group[]" class="form-control" placeholder="Science" required>
                    </div>`;
                case "diploma":
                    return `
                    <div class="mb-3">
                        <label>What medium did you study after SSC? *</label>
                        <input type="text" name="diploma_medium[]" class="form-control" placeholder="Diploma" required>
                    </div>
                    <div class="mb-3">
                        <label>What is the subject of your diploma? *</label>
                        <input type="text" name="diploma_subject[]" class="form-control" placeholder="Diploma in Computer Science Engineering" required>
                    </div>
                    <div class="mb-3">
                        <label>Name of educational institution *</label>
                        <input type="text" name="diploma_institution[]" class="form-control" placeholder="Barguna Polyte" required>
                    </div>
                    <div class="mb-3">
                        <label>Passing Year *</label>
                        <input type="text" name="diploma_year[]" class="form-control" placeholder="2018" required>
                    </div>`;
                case "bachelor":
                    return `
                    <div class="mb-3">
                        <label>Graduation Subject *</label>
                        <input type="text" name="graduation_subject[]" class="form-control" placeholder="Computer Science" required>
                    </div>
                    <div class="mb-3">
                        <label>Institution *</label>
                        <input type="text" name="graduation_institution[]" class="form-control" placeholder="University Name" required>
                    </div>
                    <div class="mb-3">
                        <label>Passing Year *</label>
                        <input type="text" name="graduation_year[]" class="form-control" placeholder="2022" required>
                    </div>`;
                case "master":
                    return `
                    <div class="mb-3">
                        <label>Postgraduation Subject *</label>
                        <input type="text" name="postgraduation_subject[]" class="form-control" placeholder="Computer Science" required>
                    </div>
                    <div class="mb-3">
                        <label>Institution *</label>
                        <input type="text" name="postgraduation_institution[]" class="form-control" placeholder="Geeta University" required>
                    </div>
                    <div class="mb-3">
                        <label>Passing Year *</label>
                        <input type="text" name="postgraduation_year[]" class="form-control" placeholder="2025" required>
                    </div>`;
                case "hafez":
                case "maolana":
                case "mufti":
                case "mufassir":
                case "adib":
                case "qari":
                    return `
                    <div class="mb-3">
                        <label>Name of Madrasa *</label>
                        <input type="text" name="islamic_institution[]" class="form-control" placeholder="Madrasa Name" required>
                    </div>
                    <div class="mb-3">
                        <label>Passing Year *</label>
                        <input type="text" name="islamic_year[]" class="form-control" placeholder="2020" required>
                    </div>`;
                case "other":
                    return `
                    <div class="mb-3">
                        <label>Other Qualification Details *</label>
                        <textarea name="other_education[]" class="form-control" placeholder="Institution, subject, passing year"></textarea>
                    </div>`;
                default:
                    return "";
            }
        }

        // Update qualification dropdown options based on method
        function updateQualificationOptions(method) {
            document.querySelectorAll(".education-select").forEach(select => {
                select.innerHTML = '<option value="">-- Select --</option>'; // reset
                let options = [];
                if (method === "General") {
                    options = generalOptions;
                } else if (method === "Islamic") {
                    options = islamicOptions;
                } else if (method === "Both") {
                    options = [...generalOptions, ...islamicOptions];
                }
                options.forEach(opt => {
                    let option = document.createElement("option");
                    option.value = opt.value;
                    option.textContent = opt.text;
                    select.appendChild(option);
                });
            });
        }

        // Event: change Education Method
        document.getElementById("education-method").addEventListener("change", function() {
            updateQualificationOptions(this.value);
        });

        // Event: change Qualification dropdown → load fields
        document.addEventListener("change", function(e) {
            if (e.target.classList.contains("education-select")) {
                let type = e.target.value;
                let fieldsContainer = e.target.closest(".education-block").querySelector(".education-fields");
                fieldsContainer.innerHTML = getFields(type);
            }
        });

        // Add new qualification block
        document.getElementById("add-education").addEventListener("click", function() {
            let container = document.getElementById("education-container");
            let newBlock = document.querySelector(".education-block").cloneNode(true);

            newBlock.querySelector(".education-fields").innerHTML = "";
            newBlock.querySelector(".education-select").value = "";

            container.appendChild(newBlock);
            updateQualificationOptions(document.getElementById("education-method").value);
        });
    });

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