@extends('layouts.user-dashboard-app')

@section('title', 'Profile Detail')

@push('styles')
<style>
    body {
        background: #f8f9fa;
    }

    .profile-header {
        background: linear-gradient(135deg, #007bff, #6f42c1);
        color: #fff;
        padding: 30px 20px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-carousel img {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        border: 5px solid #fff;
        object-fit: cover;
        margin: auto;
        cursor: pointer;
    }

    .profile-box {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
    }

    .section-heading {
        background: #f1f3f5;
        font-weight: 600;
        font-size: 16px;
        padding: 10px 12px;
        border-left: 4px solid #007bff;
        margin: 20px 0 10px 0;
    }

    .info-list li {
        margin-bottom: 8px;
        font-size: 15px;
    }

    .info-card {
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.4s ease;
        animation: fadeIn 0.8s ease-in-out;
    }

    .info-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .table {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
    }

    .table thead {
        background: linear-gradient(135deg, #007bff, #6f42c1);
        font-weight: 600;
        font-size: 1.1rem;
        letter-spacing: 0.3px;
    }

    .table thead th {
        color: #333;
        border-color: #6f42c1;
    }

    .table th {
        width: 35%;
        background-color: #f8f9fa;
        color: #0d6efd;
        font-weight: 600;
        border-color: #dee2e6;
    }

    .table td {
        color: #444;
        border-color: #dee2e6;
        transition: all 0.3s ease;
    }

    .table tr:hover {
        background-color: #f1f5ff;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .btn-download {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: #fff;
        border: none;
        border-radius: 25px;
        padding: 8px 16px;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
        transition: all 0.3s ease-in-out;
        position: relative;
        overflow: hidden;
    }

    .btn-download:hover {
        transform: scale(1.05);
        background: linear-gradient(135deg, #20c997, #28a745);
        box-shadow: 0 6px 15px rgba(32, 201, 151, 0.5);
    }

    .btn-download i {
        transition: transform 0.3s ease-in-out;
    }

    .btn-download:hover i {
        transform: translateY(-2px);
    }

    .btn-download::after {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.3);
        transition: left 0.4s ease-in-out;
    }

    .btn-download:hover::after {
        left: 100%;
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    <!-- Profile Header with Carousel -->
    <div class="profile-header">
        <div id="profileCarousel" class="carousel slide profile-carousel" data-bs-ride="carousel">
            <div class="carousel-inner">
                @if(!empty($photos))
                @foreach($photos as $index => $photo)
                <div class="carousel-item @if($index == 0) active @endif">
                    <img src="{{ asset('storage/' . $photo) }}" class="d-block zoomable" alt="Profile Photo {{ $index+1 }}">
                </div>
                @endforeach
                @else
                <div class="carousel-item active">
                    <img src="{{ asset('storage/groom_photos/default.png') }}" class="d-block zoomable" alt="Default Profile Photo">
                </div>
                @endif
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#profileCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#profileCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <h3>{{ $biodata->name ?? $registration->name ?? 'Not Set' }}</h3>
        <p>
            {{ $biodata->birth_date 
    ? \Carbon\Carbon::parse($biodata->birth_date)->diffInYears(now()) . ' years' 
    : 'N/A' }} , {{ $biodata->height ?? 'N/A' }} inc |
            {{ $biodata->present_address ?? 'N/A' }}
        </p>
        <a href="{{ route('biodata.download', $biodata->id) }}" class="btn btn-download btn-sm" onclick="confirmDownload({{ $biodata->id }})">
            <i class="bi bi-download me-1"></i> Download Biodata
        </a>
    </div>

    <!-- Profile Box -->
    <div class="profile-box">

        <!-- General Info -->
        <div class="card info-card border-0 shadow mt-4 position-relative">
            <div class="card-body p-3">

                <!-- Edit Button -->
                <button type="button" class="btn btn-sm btn-primary position-absolute top-0 end-0 m-3" data-bs-toggle="modal" data-bs-target="#editGeneralInfoModal">
                    <i class="bi bi-pencil-square"></i> Edit
                </button>

                <table class="table table-bordered align-middle mb-0">
                    <thead class="text-white bg-gradient">
                        <tr>
                            <th colspan="2">
                                <i class="bi bi-person-fill me-2"></i> General Info
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Biodata Type</th>
                            <td>{{ ucfirst($registration->gender) ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Marital Status</th>
                            <td>{{ $biodata->marital_status ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Birth Date</th>
                            <td>{{ $biodata->birth_date ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Height</th>
                            <td>{{ $biodata->height ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Complexion</th>
                            <td>{{ $biodata->complexion ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Weight</th>
                            <td>{{ $biodata->weight ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Blood Group</th>
                            <td>{{ $biodata->blood_group ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Nationality</th>
                            <td>{{ $biodata->nationality ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editGeneralInfoModal" tabindex="-1" aria-labelledby="editGeneralInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editGeneralInfoModalLabel">
                            <i class="bi bi-pencil-square me-2"></i>Edit General Info
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('biodata.updateGeneralInfo', $biodata->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">
                            <div class="row g-3">
                                <!-- Biodata Type (Read-Only) -->
                                <div class="col-md-6">
                                    <label class="form-label">Biodata Type</label>
                                    <input type="text" class="form-control" value="{{ ucfirst($registration->gender) }}" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Marital Status</label>
                                    <input type="text" name="marital_status" value="{{ $biodata->marital_status }}" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Birth Date</label>
                                    <input type="date" name="birth_date" value="{{ $biodata->birth_date }}" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Height</label>
                                    <input type="text" name="height" value="{{ $biodata->height }}" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Complexion</label>
                                    <input type="text" name="complexion" value="{{ $biodata->complexion }}" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Weight</label>
                                    <input type="text" name="weight" value="{{ $biodata->weight }}" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Blood Group</label>
                                    <input type="text" name="blood_group" value="{{ $biodata->blood_group }}" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nationality</label>
                                    <input type="text" name="nationality" value="{{ $biodata->nationality }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- Address -->
        <div class="card info-card border-0 shadow mt-4 position-relative">
            <div class="card-body p-3">

                <!-- Edit Button -->
                <button type="button" class="btn btn-sm btn-primary position-absolute top-0 end-0 m-3" data-bs-toggle="modal" data-bs-target="#editAddressModal">
                    <i class="bi bi-pencil-square"></i> Edit
                </button>

                <table class="table table-bordered align-middle mb-0">
                    <thead class="text-white bg-gradient">
                        <tr>
                            <th colspan="2">
                                <i class="bi bi-geo-alt-fill me-2"></i> Address
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Present Address</th>
                            <td>{{ $biodata->present_address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Village Area</th>
                            <td>{{ $biodata->village_area ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Permanent Address</th>
                            <td>{{ $biodata->permanent_address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Grew Up</th>
                            <td>{{ $biodata->grew_up ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editAddressModalLabel">
                            <i class="bi bi-pencil-square me-2"></i>Edit Address
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('biodata.updateAddress', $biodata->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">Present Address</label>
                                    <input type="text" name="present_address" value="{{ $biodata->present_address }}" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Village Area</label>
                                    <input type="text" name="village_area" value="{{ $biodata->village_area }}" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Permanent Address</label>
                                    <input type="text" name="permanent_address" value="{{ $biodata->permanent_address }}" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Grew Up</label>
                                    <input type="text" name="grew_up" value="{{ $biodata->grew_up }}" class="form-control">
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>



        <!-- Educational Qualifications Card -->
        <div class="card info-card border-0 shadow mt-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">
                        <i class="bi bi-mortarboard-fill me-2"></i> Educational Qualifications
                    </h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editEducationModal">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                </div>

                <table class="table table-bordered align-middle mb-0">
                    <tbody>
                        <tr>
                            <th>Your Education Method</th>
                            <td>{{ $biodata->education_method ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Highest Educational Qualification</th>
                            <td>{{ $biodata->highest_qualification ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Other Education</th>
                            <td>{{ $biodata->other_education ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>SSC / Dakhil / Equivalent Passing Year</th>
                            <td>{{ $biodata->ssc_year ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Group (SSC)</th>
                            <td>{{ $biodata->ssc_group ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Diploma Subject</th>
                            <td>{{ $biodata->diploma_subject ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Diploma Medium / Group</th>
                            <td>{{ $biodata->diploma_medium ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Diploma Institution</th>
                            <td>{{ $biodata->diploma_institution ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Diploma Passing Year</th>
                            <td>{{ $biodata->diploma_year ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Graduation Subject</th>
                            <td>{{ $biodata->graduation_subject ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Graduation Institution</th>
                            <td>{{ $biodata->graduation_institution ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Graduation Passing Year</th>
                            <td>{{ $biodata->graduation_year ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Postgraduation Subject</th>
                            <td>{{ $biodata->postgraduation_subject ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Postgraduation Institution</th>
                            <td>{{ $biodata->postgraduation_institution ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Postgraduation Passing Year</th>
                            <td>{{ $biodata->postgraduation_year ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Islamic Titles for Profiles</th>
                            <td>{{ $biodata->islamic_titles ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Islamic Institution</th>
                            <td>{{ $biodata->islamic_institution ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Islamic Passing Year</th>
                            <td>{{ $biodata->islamic_year ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="editEducationModal" tabindex="-1" aria-labelledby="editEducationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editEducationModalLabel">Edit Educational Qualifications</h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('biodata.update.education', $biodata->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Your Education Method</label>
                                    <input type="text" name="education_method" class="form-control" value="{{ $biodata->education_method }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Highest Educational Qualification</label>
                                    <input type="text" name="highest_qualification" class="form-control" value="{{ $biodata->highest_qualification }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Other Education</label>
                                    <input type="text" name="other_education" class="form-control" value="{{ $biodata->other_education }}">
                                </div>
                                <div class="col-md-6">
                                    <label>SSC Year</label>
                                    <input type="text" name="ssc_year" class="form-control" value="{{ $biodata->ssc_year }}">
                                </div>
                                <div class="col-md-6">
                                    <label>SSC Group</label>
                                    <input type="text" name="ssc_group" class="form-control" value="{{ $biodata->ssc_group }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Diploma Subject</label>
                                    <input type="text" name="diploma_subject" class="form-control" value="{{ $biodata->diploma_subject }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Diploma Medium</label>
                                    <input type="text" name="diploma_medium" class="form-control" value="{{ $biodata->diploma_medium }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Diploma Institution</label>
                                    <input type="text" name="diploma_institution" class="form-control" value="{{ $biodata->diploma_institution }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Diploma Year</label>
                                    <input type="text" name="diploma_year" class="form-control" value="{{ $biodata->diploma_year }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Graduation Subject</label>
                                    <input type="text" name="graduation_subject" class="form-control" value="{{ $biodata->graduation_subject }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Graduation Institution</label>
                                    <input type="text" name="graduation_institution" class="form-control" value="{{ $biodata->graduation_institution }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Graduation Year</label>
                                    <input type="text" name="graduation_year" class="form-control" value="{{ $biodata->graduation_year }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Postgraduation Subject</label>
                                    <input type="text" name="postgraduation_subject" class="form-control" value="{{ $biodata->postgraduation_subject }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Postgraduation Institution</label>
                                    <input type="text" name="postgraduation_institution" class="form-control" value="{{ $biodata->postgraduation_institution }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Postgraduation Year</label>
                                    <input type="text" name="postgraduation_year" class="form-control" value="{{ $biodata->postgraduation_year }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Islamic Titles</label>
                                    <input type="text" name="islamic_titles" class="form-control" value="{{ $biodata->islamic_titles }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Islamic Institution</label>
                                    <input type="text" name="islamic_institution" class="form-control" value="{{ $biodata->islamic_institution }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Islamic Year</label>
                                    <input type="text" name="islamic_year" class="form-control" value="{{ $biodata->islamic_year }}">
                                </div>
                            </div>

                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Info Card -->
        <div class="card info-card border-0 shadow mt-4">
            <div class="card-body p-3">
                <!-- Heading with Edit Button -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">
                        <i class="bi bi-heart-fill me-2"></i> Personal Information
                    </h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editPersonalModal">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                </div>

                <!-- Table -->
                <table class="table table-bordered align-middle mb-0">
                    <tbody>
                        <tr>
                            <th>Clothing Style</th>
                            <td>{{ $biodata->clothing_style ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Beard Info (Sunnah, Since When)</th>
                            <td>{{ $biodata->beard_info ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Clothes Above Ankles</th>
                            <td>{{ $biodata->clothes_above_ankles ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Qaza (Missed Prayers per Week)</th>
                            <td>{{ $biodata->prayers_info ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Compliance with Mahram / Non-Mahram</th>
                            <td>{{ $biodata->mahram_nonmahram ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Quran Recitation Ability</th>
                            <td>{{ $biodata->quran_recitation ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Fiqh Followed</th>
                            <td>{{ $biodata->fiqh ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Watching / Listening Entertainment</th>
                            <td>{{ $biodata->watch_entertainment ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Mental or Physical Diseases</th>
                            <td>{{ $biodata->diseases ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Beliefs About Shrine (Mazar)</th>
                            <td>{{ $biodata->beliefs_on_mazar ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Islamic Books Read (At least 3)</th>
                            <td>{{ $biodata->books_read ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Applicable Special Category</th>
                            <td>{{ $biodata->special_category ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Hobbies, Likes/Dislikes, Dreams</th>
                            <td>{{ $biodata->hobbies ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Groom's Mobile Number</th>
                            <td>{{ $biodata->groom_mobile ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editPersonalModal" tabindex="-1" aria-labelledby="editPersonalModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('biodata.update.personal', $biodata->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPersonalModalLabel">Edit Personal Information</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Form Fields -->
                            <div class="mb-3">
                                <label class="form-label">Clothing Style</label>
                                <input type="text" name="clothing_style" class="form-control" value="{{ $biodata->clothing_style }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Beard Info</label>
                                <input type="text" name="beard_info" class="form-control" value="{{ $biodata->beard_info }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Clothes Above Ankles</label>
                                <input type="text" name="clothes_above_ankles" class="form-control" value="{{ $biodata->clothes_above_ankles }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Qaza (Missed Prayers per Week)</label>
                                <input type="text" name="prayers_info" class="form-control" value="{{ $biodata->prayers_info }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Compliance with Mahram / Non-Mahram</label>
                                <input type="text" name="mahram_nonmahram" class="form-control" value="{{ $biodata->mahram_nonmahram }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Quran Recitation Ability</label>
                                <input type="text" name="quran_recitation" class="form-control" value="{{ $biodata->quran_recitation }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Fiqh Followed</label>
                                <input type="text" name="fiqh" class="form-control" value="{{ $biodata->fiqh }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Watching / Listening Entertainment</label>
                                <input type="text" name="watch_entertainment" class="form-control" value="{{ $biodata->watch_entertainment }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mental or Physical Diseases</label>
                                <input type="text" name="diseases" class="form-control" value="{{ $biodata->diseases }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Beliefs About Shrine (Mazar)</label>
                                <input type="text" name="beliefs_on_mazar" class="form-control" value="{{ $biodata->beliefs_on_mazar }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Islamic Books Read (At least 3)</label>
                                <input type="text" name="books_read" class="form-control" value="{{ $biodata->books_read }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Applicable Special Category</label>
                                <input type="text" name="special_category" class="form-control" value="{{ $biodata->special_category }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hobbies, Likes/Dislikes, Dreams</label>
                                <input type="text" name="hobbies" class="form-control" value="{{ $biodata->hobbies }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Groom's Mobile Number</label>
                                <input type="text" name="groom_mobile" class="form-control" value="{{ $biodata->groom_mobile }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Information</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Family Info -->
        <div class="card info-card border-0 shadow mt-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">
                        <i class="bi bi-people-fill me-2"></i> Family Information
                    </h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editFamilyModal">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                </div>

                <table class="table table-bordered align-middle mb-0">
                    <tbody>
                        <tr>
                            <th>Father's Name</th>
                            <td>{{ $biodata->father_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Is your father alive?</th>
                            <td>{{ $biodata->father_alive ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Description of father's profession</th>
                            <td>{{ $biodata->father_profession ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Mother's Name</th>
                            <td>{{ $biodata->mother_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Is your mother alive?</th>
                            <td>{{ $biodata->mother_alive ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Description of mother's profession</th>
                            <td>{{ $biodata->mother_profession ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Number of brothers</th>
                            <td>{{ $biodata->brothers ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Number of sisters</th>
                            <td>{{ $biodata->sisters ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Description of uncle's profession</th>
                            <td>{{ $biodata->uncle_profession ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Family financial status</th>
                            <td>{{ $biodata->family_financial_status ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Description of family's financial situation</th>
                            <td>{{ $biodata->family_details ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Family's religious condition</th>
                            <td>{{ $biodata->family_religious_condition ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="editFamilyModal" tabindex="-1" aria-labelledby="editFamilyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editFamilyModalLabel">Edit Family Information</h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <form action="{{ route('biodata.update.family', $biodata->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Father's Name</label>
                                    <input type="text" name="father_name" class="form-control" value="{{ $biodata->father_name }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Is your father alive?</label>
                                    <select name="father_alive" class="form-select">
                                        <option value="Yes" {{ $biodata->father_alive == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ $biodata->father_alive == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Description of father's profession</label>
                                    <input type="text" name="father_profession" class="form-control" value="{{ $biodata->father_profession }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Mother's Name</label>
                                    <input type="text" name="mother_name" class="form-control" value="{{ $biodata->mother_name }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Is your mother alive?</label>
                                    <select name="mother_alive" class="form-select">
                                        <option value="Yes" {{ $biodata->mother_alive == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ $biodata->mother_alive == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Description of mother's profession</label>
                                    <input type="text" name="mother_profession" class="form-control" value="{{ $biodata->mother_profession }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Number of brothers</label>
                                    <input type="number" name="brothers" class="form-control" value="{{ $biodata->brothers }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Number of sisters</label>
                                    <input type="number" name="sisters" class="form-control" value="{{ $biodata->sisters }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Description of uncle's profession</label>
                                    <input type="text" name="uncle_profession" class="form-control" value="{{ $biodata->uncle_profession }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Family financial status</label>
                                    <input type="text" name="family_financial_status" class="form-control" value="{{ $biodata->family_financial_status }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Description of family's financial situation</label>
                                    <input type="text" name="family_details" class="form-control" value="{{ $biodata->family_details }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Family's religious condition</label>
                                    <input type="text" name="family_religious_condition" class="form-control" value="{{ $biodata->family_religious_condition }}">
                                </div>
                            </div>

                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Occupational Info Card -->
        <div class="card info-card border-0 shadow mt-4">
            <div class="card-body p-3">
                <!-- Heading with Edit Button -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">
                        <i class="bi bi-briefcase-fill me-2"></i> Occupational Information
                    </h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editOccupationModal">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                </div>

                <!-- Table -->
                <table class="table table-bordered align-middle mb-0">
                    <tbody>
                        <tr>
                            <th>Occupation</th>
                            <td>{{ $biodata->occupation ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Description of Profession</th>
                            <td>{{ $biodata->profession_details ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Monthly Income</th>
                            <td>{{ $biodata->monthly_income ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editOccupationModal" tabindex="-1" aria-labelledby="editOccupationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('biodata.update.occupation', $biodata->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editOccupationModalLabel">Edit Occupational Information</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Occupation</label>
                                <input type="text" name="occupation" class="form-control" value="{{ $biodata->occupation }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description of Profession</label>
                                <input type="text" name="profession_details" class="form-control" value="{{ $biodata->profession_details }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Monthly Income</label>
                                <input type="text" name="monthly_income" class="form-control" value="{{ $biodata->monthly_income }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Information</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <!-- Marriage & Future Plans -->
        <div class="card info-card border-0 shadow mt-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">
                        <i class="bi bi-people-fill me-2"></i> Marriage & Future Plans
                    </h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editMarriageModal">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                </div>

                <table class="table table-bordered align-middle mb-0">
                    <tbody>
                        <tr>
                            <th>Do your guardians agree to your marriage?</th>
                            <td>{{ $biodata->guardian_agree ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Will you keep your wife in the veil after marriage?</th>
                            <td>{{ $biodata->wife_in_veil ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Allow your wife to study after marriage?</th>
                            <td>{{ $biodata->wife_study_allowed ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Allow your wife to do any job after marriage?</th>
                            <td>{{ $biodata->wife_job_allowed ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Residence with wife after marriage</th>
                            <td>{{ $biodata->residence_after_marriage ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Expect any gift from bride's family?</th>
                            <td>{{ $biodata->expect_gift_from_bride ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Marriage Modal -->
        <div class="modal fade" id="editMarriageModal" tabindex="-1" aria-labelledby="editMarriageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-gradient text-white">
                        <h5 class="modal-title" id="editMarriageModalLabel">
                            <i class="bi bi-pencil-square me-2"></i> Edit Marriage & Future Plans
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('biodata.update.marriage', $biodata->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Do your guardians agree to your marriage?</label>
                                    <input type="text" name="guardian_agree" class="form-control" value="{{ old('guardian_agree', $biodata->guardian_agree) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Will you keep your wife in the veil after marriage?</label>
                                    <input type="text" name="wife_in_veil" class="form-control" value="{{ old('wife_in_veil', $biodata->wife_in_veil) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Allow your wife to study after marriage?</label>
                                    <input type="text" name="wife_study_allowed" class="form-control" value="{{ old('wife_study_allowed', $biodata->wife_study_allowed) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Allow your wife to do any job after marriage?</label>
                                    <input type="text" name="wife_job_allowed" class="form-control" value="{{ old('wife_job_allowed', $biodata->wife_job_allowed) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Residence with wife after marriage</label>
                                    <input type="text" name="residence_after_marriage" class="form-control" value="{{ old('residence_after_marriage', $biodata->residence_after_marriage) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Expect any gift from bride's family?</label>
                                    <input type="text" name="expect_gift_from_bride" class="form-control" value="{{ old('expect_gift_from_bride', $biodata->expect_gift_from_bride) }}">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Information</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <!-- Expected Life Partner -->
        <div class="card info-card border-0 shadow mt-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">
                        <i class="bi bi-gem me-2"></i> Expected Life Partner
                    </h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editPartnerModal">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                </div>

                <table class="table table-bordered align-middle mb-0">
                    <tbody>
                        <tr>
                            <th>Age</th>
                            <td>{{ $biodata->partner_age ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Complexion</th>
                            <td>{{ $biodata->partner_complexion ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Height</th>
                            <td>{{ $biodata->partner_height ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Educational Qualification</th>
                            <td>{{ $biodata->partner_education ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>District</th>
                            <td>{{ $biodata->partner_district ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Marital Status</th>
                            <td>{{ $biodata->partner_marital_status ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Profession</th>
                            <td>{{ $biodata->partner_profession ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Financial Condition</th>
                            <td>{{ $biodata->partner_financial_condition ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Expected Qualities / Attributes</th>
                            <td>{{ $biodata->partner_expectations ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Expected Partner Modal -->
        <div class="modal fade" id="editPartnerModal" tabindex="-1" aria-labelledby="editPartnerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-gradient text-white">
                        <h5 class="modal-title" id="editPartnerModalLabel">
                            <i class="bi bi-pencil-square me-2"></i> Edit Expected Life Partner Information
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('biodata.update.partner', $biodata->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Age</label>
                                    <input type="text" name="partner_age" class="form-control" value="{{ old('partner_age', $biodata->partner_age) }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Complexion</label>
                                    <input type="text" name="partner_complexion" class="form-control" value="{{ old('partner_complexion', $biodata->partner_complexion) }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Height</label>
                                    <input type="text" name="partner_height" class="form-control" value="{{ old('partner_height', $biodata->partner_height) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Educational Qualification</label>
                                    <input type="text" name="partner_education" class="form-control" value="{{ old('partner_education', $biodata->partner_education) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">District</label>
                                    <input type="text" name="partner_district" class="form-control" value="{{ old('partner_district', $biodata->partner_district) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Marital Status</label>
                                    <input type="text" name="partner_marital_status" class="form-control" value="{{ old('partner_marital_status', $biodata->partner_marital_status) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Profession</label>
                                    <input type="text" name="partner_profession" class="form-control" value="{{ old('partner_profession', $biodata->partner_profession) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Financial Condition</label>
                                    <input type="text" name="partner_financial_condition" class="form-control" value="{{ old('partner_financial_condition', $biodata->partner_financial_condition) }}">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Expected Qualities / Attributes</label>
                                    <textarea name="partner_expectations" class="form-control" rows="3">{{ old('partner_expectations', $biodata->partner_expectations) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Information</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <!-- Pledge -->
        <div class="card info-card border-0 shadow mt-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">
                        <i class="bi bi-journal-check me-2"></i> Pledge
                    </h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editPledgeModal">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                </div>

                <table class="table table-bordered align-middle mb-0">
                    <tbody>
                        <tr>
                            <th>Do your parents know that you are submitting biodata?</th>
                            <td>{{ $biodata->parents_know ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>By Allah, testify that all the information given is true</th>
                            <td>{{ $biodata->truth_testify ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>If false information is provided, OrdhekDeen.com is not responsible. Do you agree?</th>
                            <td>{{ $biodata->responsibility ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Pledge Modal -->
        <div class="modal fade" id="editPledgeModal" tabindex="-1" aria-labelledby="editPledgeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-gradient text-white">
                        <h5 class="modal-title" id="editPledgeModalLabel">
                            <i class="bi bi-pencil-square me-2"></i> Edit Pledge Information
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('biodata.update.pledge', $biodata->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Do your parents know that you are submitting biodata?</label>
                                    <select name="parents_know" class="form-select">
                                        <option value="">Select Option</option>
                                        <option value="Yes" {{ old('parents_know', $biodata->parents_know) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ old('parents_know', $biodata->parents_know) == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">By Allah, testify that all the information given is true</label>
                                    <select name="truth_testify" class="form-select">
                                        <option value="">Select Option</option>
                                        <option value="Yes" {{ old('truth_testify', $biodata->truth_testify) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ old('truth_testify', $biodata->truth_testify) == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">If false information is provided, OrdhekDeen.com is not responsible. Do you agree?</label>
                                    <select name="responsibility" class="form-select">
                                        <option value="">Select Option</option>
                                        <option value="Yes" {{ old('responsibility', $biodata->responsibility) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ old('responsibility', $biodata->responsibility) == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Pledge</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <!-- Contact -->
        <div class="card info-card border-0 shadow mt-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">
                        <i class="bi bi-telephone-fill me-2"></i> Contact
                    </h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editContactModal">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                </div>

                <table class="table table-bordered align-middle mb-0">
                    <tbody>
                        <tr>
                            <th>Groom's Name</th>
                            <td>{{ $biodata->groom_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Guardian's Mobile Number</th>
                            <td>{{ $biodata->guardian_mobile ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Relationship with Guardian</th>
                            <td>{{ $biodata->guardian_relationship ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>E-mail to Receive Biodata</th>
                            <td>{{ $biodata->guardian_email ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Contact Modal -->
        <div class="modal fade" id="editContactModal" tabindex="-1" aria-labelledby="editContactModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('biodata.update.contact', $biodata->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="editContactModalLabel">
                                <i class="bi bi-pencil-square me-2"></i> Edit Contact Information
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Groom's Name</label>
                                <input type="text" name="groom_name" class="form-control" value="{{ $biodata->groom_name }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Guardian's Mobile Number</label>
                                <input type="text" name="guardian_mobile" class="form-control" value="{{ $biodata->guardian_mobile }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Relationship with Guardian</label>
                                <input type="text" name="guardian_relationship" class="form-control" value="{{ $biodata->guardian_relationship }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">E-mail to Receive Biodata</label>
                                <input type="email" name="guardian_email" class="form-control" value="{{ $biodata->guardian_email }}">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>




    </div>
</div>

<!-- Image Zoom Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <img id="zoomImage" src="" alt="Zoomed Image" class="w-100 rounded">
            </div>
        </div>
    </div>
</div>

<script>
    // Zoom functionality
    document.querySelectorAll(".zoomable").forEach(img => {
        img.addEventListener("click", function() {
            document.getElementById("zoomImage").src = this.src;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        });
    });

    function confirmDownload(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to download the biodata PDF!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Download!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Start download
                const link = document.createElement('a');
                link.href = '/biodata/download/' + id;
                link.download = '';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Optional: show success toast
                Swal.fire({
                    title: 'Downloading...',
                    text: 'Your biodata is being downloaded!',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }
</script>

@endsection