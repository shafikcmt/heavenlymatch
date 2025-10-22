@extends('layouts.user-dashboard-app')

@section('title', 'MY HOME')

@section('content')

@push('styles')
<style>
    body {
        background-color: #f8f9fa;
    }

    .profile-card {
        border-radius: 15px;
        overflow: hidden;
        position: relative;
    }

    .carousel-container {
        width: 220px;
    }

    .carousel-item img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        border-radius: 10px;
    }

    .top-right-controls {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .icon-group i {
        font-size: 1.2rem;
        cursor: pointer;
    }

    .icon-group div {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .view-profile-button {
        display: flex;
        justify-content: left;
        margin-top: 5px;
    }

    .id-text {
        text-align: center;
        font-weight: 600;
        font-size: 10px;
    }

    /* LEFT AND RIGHT ICONS AT BOTTOM */
    .bottom-icons {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
    }

    .icon-group-horizontal {
        display: flex;
        gap: 15px;
        text-align: center;
    }

    .carousel-indicators [data-bs-target] {
        height: 5px !important;
        width: 5px !important;
        border-radius: 100%;
    }

    .eye-btn {
        border: none;
        /* Remove border */
        background: none;
        /* Remove background */
        font-size: 1.4rem;
        /* Make icon slightly larger */
        color: #555;
        /* Icon color */
        transition: transform 0.2s, color 0.2s;
    }

    .eye-btn:hover {
        color: #0d6efd;
        /* Hover color (Bootstrap blue) */
        transform: scale(1.1);
        /* Slight zoom on hover */
    }

    .profile-section {
        /* border: 1px solid #dee2e6; */
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 10px;
        background-color: #fafafa;
    }

    .profile-section h6 {
        background: #0d6efd;
        color: #fff;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 0.95rem;
        margin-bottom: 12px;
    }

    .profile-section p {
        margin-bottom: 6px;
        font-size: 0.9rem;
    }

    .profile-section strong {
        color: #0d6efd;
    }


    .top-right-controls {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .status-pill {
        background-color: #e7f1ff;
        /* same bg for both */
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-text {
        font-size: 0.85rem;
    }

    #eyeIcon {
        font-size: 1rem;
        color: #0d6efd;
        cursor: pointer;
    }



.carousel-container {
  position: relative;
  overflow: hidden;
  border-radius: 10px;
}

.carousel-inner img {
  width: 100%;
  height: 201px; /* Set same height for all images */
  object-fit: cover;
  display: block;
}


/* ID text on image */
.id-text {
    position: absolute;
    bottom: 0px;
    left: 0px;
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
    padding: 6px 14px;
    /* border-radius: 6px; */
    font-weight: 500;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.5);
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    width: 100%;
    display: block;
}
</style>
@endpush
{{-- Main Dashboard Content --}}
<div class="container my-4">
    <form class="d-flex flex-wrap align-items-center gap-3 filter-form">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="showPhoto">
            <label class="form-check-label" for="showPhoto">Show profiles with photo</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="dontContacted">
            <label class="form-check-label" for="dontContacted">Don't show already contacted</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="dontViewed">
            <label class="form-check-label" for="dontViewed">Don't show already viewed</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="showAll">
            <label class="form-check-label" for="showAll">Show all</label>
        </div>

        <button type="submit" class="btn btn-primary">Submit »</button>
    </form>
</div>

<div class="container my-4">
    <div class="row">
        <div class="col-lg-9 col-md-12">
            <div class="card shadow-sm p-3 profile-card mb-3">
                <!-- TOP RIGHT CONTROLS -->
                <div class="top-right-controls d-flex flex-column gap-2 align-items-end">
                    <!-- ONLINE STATUS -->
                    <div class="status-pill d-flex align-items-center gap-1">
                        <span class="status-dot bg-success"></span>
                        <span class="status-text">Online</span>
                    </div>

                    <!-- VIEWED / NEW -->
                    <div class="status-pill d-flex align-items-center gap-1">
                        <i class="bi bi-eye-fill" id="eyeIcon"></i>
                        <span class="status-text" id="eyeText">Viewed</span>
                    </div>
                </div>


                <div class="d-flex align-items-start gap-3">
                    <!-- LEFT CAROUSEL -->
                    <div class="carousel-container position-relative">
  <div id="profileCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner rounded overflow-hidden">
      <div class="carousel-item active">
        <img src="https://i.pinimg.com/564x/02/e7/f5/02e7f5464591ce514fffbdf03b287eed.jpg" alt="Profile Image 1" />
      </div>
      <div class="carousel-item">
        <img src="https://i.pinimg.com/736x/7d/9e/4b/7d9e4b710a28f342febffb0be5d88519.jpg" alt="Profile Image 2" />
      </div>
      <div class="carousel-item">
        <img src="https://i.pinimg.com/736x/b6/86/b8/b686b8e70200aacc92ad78b70b866416.jpg" alt="Profile Image 3" />
      </div>
    </div>

 

    <!-- Carousel Indicators -->
    <div class="carousel-indicators position-absolute bottom-0 end-0 me-2 mb-1" style="justify-content: end">
      <button type="button" data-bs-target="#profileCarousel" data-bs-slide-to="0" class="active"></button>
      <button type="button" data-bs-target="#profileCarousel" data-bs-slide-to="1"></button>
      <button type="button" data-bs-target="#profileCarousel" data-bs-slide-to="2"></button>
    </div>
  </div>

  <!-- ID OVERLAY TEXT -->
  <div class="id-text position-absolute">
  ID:<span>HM12345</span>
  </div>
</div>


                    <!-- RIGHT DETAILS -->
                    <div class="flex-grow-1 position-relative">
                        <h5 class="fw-bold mb-1">Md Shafiqul Islam</h5>
                        <p class="text-muted mb-1">
                            <i class="bi bi-geo-alt-fill"></i> Khulna, Bangladesh
                        </p>
                        <p class="mb-1">
                            <strong>Age:</strong> 27 | <strong> Status:</strong> Married
                        </p>
                        <p class="mb-1">
                            <strong>Occupation:</strong> Lorem ipsum dolor sit amet
                        </p>
                        <p class="mb-1">
                            <strong>Blood:</strong> B+
                            |
                            <strong>Complexion:</strong> Dark
                        </p>
                        <p class="mb-1">
                            <strong>Height:</strong> 5.4 | <strong>Weight:</strong> 63 kg
                        </p>

                        <!-- VIEW FULL PROFILE BUTTON BELOW INFO -->
                        <div class="view-profile-button">
                            <button class="btn btn-outline-primary btn-sm btn-toggle" data-bs-toggle="collapse" data-bs-target="#fullProfile" aria-expanded="false" id="toggleButton">
                                <i class="bi bi-person-lines-fill me-1"></i>
                                <span>View Full Profile</span>
                                <i class="bi bi-chevron-down ms-1" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM ICONS LEFT & RIGHT CORNERS -->
                <div class="bottom-icons d-flex justify-content-between mt-3">
                    <!-- LEFT ICONS -->
                    <div class="d-flex gap-4">
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-bookmark-check-fill text-primary"></i>
                            <span class="small">Shortlist</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-heart-fill text-danger"></i>
                            <span class="small">Favorite</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-share-fill text-secondary"></i>
                            <span class="small">Share</span>
                        </div>
                    </div>

                    <!-- RIGHT ICONS -->
                    <div class="d-flex gap-4">
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-telephone-fill text-primary"></i>
                            <span class="small">Call</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-envelope-fill text-primary"></i>
                            <span class="small">Email</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- COLLAPSIBLE FULL PROFILE -->
            <div class="collapse" id="fullProfile">
                <div class="card card-body shadow-sm border-0">


                    <!-- GENERAL INFO -->
                    <div class="profile-section">
                        <h6>General Info</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Biodata Type:</strong> Male</p>
                                <p><strong>Marital Status:</strong> Married</p>
                                <p><strong>Birth Date:</strong> 1998-01-01</p>
                                <p><strong>Height:</strong> 5.4</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Complexion:</strong> Dark</p>
                                <p><strong>Weight:</strong> 63</p>
                                <p><strong>Blood Group:</strong> B+</p>
                                <p><strong>Nationality:</strong> Bangladeshi</p>
                            </div>
                        </div>
                    </div>

                    <!-- ADDRESS -->
                    <div class="profile-section">
                        <h6>Address</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Present Address:</strong> Bangladesh, Chauhali, Sirajganj, Khulna</p>
                                <p><strong>Village Area:</strong> Nagrapara</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Permanent Address:</strong> Bangladesh, Sadarsouth, Comilla, Chattagram</p>
                                <p><strong>Grew Up:</strong> Khulna</p>
                            </div>
                        </div>
                    </div>

                    <!-- EDUCATIONAL QUALIFICATIONS -->
                    <div class="profile-section">
                        <h6>Educational Qualifications</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Education Method:</strong> General</p>
                                <p><strong>Highest Qualification:</strong> SSC</p>
                                <p><strong>Other Education:</strong> Hafiz</p>
                                <p><strong>SSC Passing Year:</strong> 2014</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Diploma Subject:</strong> Computer Science and Engineering</p>
                                <p><strong>Diploma Institute:</strong> Barguna Polytechnic Institute</p>
                                <p><strong>Diploma Passing Year:</strong> 2018</p>
                                <p><strong>Group (SSC):</strong> Science</p>
                            </div>
                        </div>
                    </div>

                    <!-- PERSONAL INFORMATION -->
                    <div class="profile-section">
                        <h6>Personal Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Clothing Style:</strong> Simple</p>
                                <p><strong>Beard Info:</strong> Yes, last 2 years</p>
                                <p><strong>Qaza (Missed Prayers):</strong> Yes, 3–4 per week</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Fiqh Followed:</strong> Hanafi</p>
                                <p><strong>Quran Recitation:</strong> Yes</p>
                                <p><strong>Mental or Physical Diseases:</strong> Yes</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAMILY INFORMATION -->
                    <div class="profile-section">
                        <h6>Family Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Father's Name:</strong> Abdul Haque</p>
                                <p><strong>Mother's Name:</strong> Maksuda Begum</p>
                                <p><strong>Brothers:</strong> 2</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Sisters:</strong> 0</p>
                                <p><strong>Financial Status:</strong> Lower class</p>
                                <p><strong>Religious Condition:</strong> Practicing</p>
                            </div>
                        </div>
                    </div>

                    <!-- OCCUPATIONAL INFORMATION -->
                    <div class="profile-section">
                        <h6>Occupational Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Occupation:</strong> Lorem ipsum dolor sit amet</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Monthly Income:</strong> 12454</p>
                            </div>
                        </div>
                    </div>

                    <!-- MARRIAGE & FUTURE PLANS -->
                    <div class="profile-section">
                        <h6>Marriage & Future Plans</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Guardians Agree:</strong> Yes</p>
                                <p><strong>Wife in Veil:</strong> Yes</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Allow Wife to Study:</strong> Yes</p>
                                <p><strong>Allow Wife to Work:</strong> Yes</p>
                            </div>
                        </div>
                    </div>

                    <!-- EXPECTED LIFE PARTNER -->
                    <div class="profile-section">
                        <h6>Expected Life Partner</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Age:</strong> 18–28</p>
                                <p><strong>Complexion:</strong> Dark</p>
                                <p><strong>Height:</strong> 5.2–5.6</p>
                                <p><strong>Qualification:</strong> HSC</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>District:</strong> Khagrachhari</p>
                                <p><strong>Marital Status:</strong> Widow</p>
                                <p><strong>Profession:</strong> Doctor</p>
                                <p><strong>Financial Condition:</strong> Average</p>
                            </div>
                        </div>
                    </div>

                    <!-- CONTACT -->
                    <div class="profile-section">
                        <h6>Contact</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Groom's Name:</strong> Shafiqul Islam</p>
                                <p><strong>Guardian Mobile:</strong> 01761817602</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Email:</strong> mdshafiqulislam822@gmail.com</p>
                                <p><strong>Relationship with Guardian:</strong> Sister</p>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const toggleIcon = document.getElementById("toggleIcon");
    const fullProfile = document.getElementById("fullProfile");
    const toggleText = document.querySelector("#toggleButton span");

    // When profile opens
    fullProfile.addEventListener("show.bs.collapse", () => {
        toggleIcon.classList.replace("bi-chevron-down", "bi-chevron-up");
        toggleText.textContent = "Hide Full Profile";

        // Auto hide if content exceeds screen height
        const observer = new ResizeObserver(() => {
            const rect = fullProfile.getBoundingClientRect();
            const viewportHeight = window.innerHeight;

            if (rect.bottom > viewportHeight) {
                const bsCollapse = bootstrap.Collapse.getInstance(fullProfile);
                if (bsCollapse) bsCollapse.hide();
                observer.disconnect();
            }
        });

        observer.observe(fullProfile);
    });

    // When profile hides
    fullProfile.addEventListener("hide.bs.collapse", () => {
        toggleIcon.classList.replace("bi-chevron-up", "bi-chevron-down");
        toggleText.textContent = "View Full Profile";
    });


    const eyeIcon = document.getElementById('eyeIcon');
    const eyeText = document.getElementById('eyeText');

    eyeIcon.addEventListener('click', () => {
        if (eyeIcon.classList.contains('bi-eye-fill')) {
            eyeIcon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            eyeText.textContent = 'New';
        } else {
            eyeIcon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            eyeText.textContent = 'Viewed';
        }
    });
</script>

@endsection