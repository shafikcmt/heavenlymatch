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
      }

      /* LEFT IMAGE SLIDER */
      .carousel-container {
        width: 220px; /* fixed small width */
      }
      .carousel-item img {
        width: 100%;
        height: 160px; /* smaller height */
        object-fit: cover;
        border-radius: 10px;
      }

      .icon-bar i {
        font-size: 1.2rem;
        color: #0d6efd;
        cursor: pointer;
      }
      .icon-bar i:hover {
        color: #084298;
      }

      .carousel-indicators [data-bs-target] {
        background-color: #0d6efd;
        width: 8px;
        height: 8px;
        border-radius: 50%;
      }

      .btn-toggle {
        white-space: nowrap;
      }

      .bottom-toggle {
        text-align: center;
        margin-top: 20px;
      }

      /* Table alignment */
      table.table th {
        width: 35%; /* fixed width for headers */
        text-align: left; /* align headers right */
        vertical-align: middle;
      }
      table.table td {
        text-align: left; /* align data left */
        vertical-align: middle;
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
    <div class="col-lg-10 col-md-12">
     <!-- PROFILE CARD -->
      <div class="card shadow-sm p-3 profile-card mb-3">
        <div class="d-flex align-items-start gap-3">
          <!-- LEFT: IMAGE SLIDER -->
          <div class="carousel-container">
            <div
              id="profileCarousel"
              class="carousel slide"
              data-bs-ride="carousel"
            >
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img
                    src="https://i.pinimg.com/564x/02/e7/f5/02e7f5464591ce514fffbdf03b287eed.jpg"
                    alt="Profile Image 1"
                  />
                </div>
                <div class="carousel-item">
                  <img
                    src="https://i.pinimg.com/736x/7d/9e/4b/7d9e4b710a28f342febffb0be5d88519.jpg"
                    alt="Profile Image 2"
                  />
                </div>
                <div class="carousel-item">
                  <img
                    src="https://i.pinimg.com/736x/b6/86/b8/b686b8e70200aacc92ad78b70b866416.jpg"
                    alt="Profile Image 3"
                  />
                </div>
              </div>
              <div class="carousel-indicators position-relative mt-2">
                <button
                  type="button"
                  data-bs-target="#profileCarousel"
                  data-bs-slide-to="0"
                  class="active"
                ></button>
                <button
                  type="button"
                  data-bs-target="#profileCarousel"
                  data-bs-slide-to="1"
                ></button>
                <button
                  type="button"
                  data-bs-target="#profileCarousel"
                  data-bs-slide-to="2"
                ></button>
              </div>
            </div>
          </div>

          <!-- RIGHT: SHORT PROFILE INFO -->
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h5 class="fw-bold mb-1">Md Shafiqul Islam</h5>
                <p class="text-muted mb-1">
                  <i class="bi bi-geo-alt-fill"></i> Khulna, Bangladesh
                </p>
                <p class="mb-1">
                  <strong>Age:</strong> 27 | <strong>Height:</strong> 5.4
                </p>
                <p class="mb-0">
                  <strong>Occupation:</strong> Lorem ipsum dolor sit amet
                </p>
              </div>

              <div>
                <button
                  class="btn btn-outline-primary btn-sm btn-toggle"
                  data-bs-toggle="collapse"
                  data-bs-target="#fullProfile"
                  aria-expanded="false"
                  id="toggleButton"
                >
                  <i class="bi bi-person-lines-fill me-1"></i>
                  <span>View Full Profile</span>
                  <i class="bi bi-chevron-down ms-1" id="toggleIcon"></i>
                </button>
              </div>
            </div>

            <!-- ICON BAR MOVED TO TOP -->
            <div class="icon-bar d-flex justify-content-start gap-3 mt-2">
              <i class="bi bi-telephone-fill" title="Call"></i>
              <i class="bi bi-envelope-fill" title="Email"></i>
              <i class="bi bi-heart-fill" title="Favorite"></i>
              <i class="bi bi-share-fill" title="Share"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- COLLAPSIBLE FULL PROFILE -->
      <div class="collapse" id="fullProfile">
        <div class="card card-body shadow-sm">
          <!-- GENERAL INFO -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-info-circle-fill me-1"></i> General Info
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Biodata Type</th>
                <td>Male</td>
              </tr>
              <tr>
                <th>Marital Status</th>
                <td>Married</td>
              </tr>
              <tr>
                <th>Birth Date</th>
                <td>1998-01-01</td>
              </tr>
              <tr>
                <th>Height</th>
                <td>5.4</td>
              </tr>
              <tr>
                <th>Complexion</th>
                <td>Dark</td>
              </tr>
              <tr>
                <th>Weight</th>
                <td>63</td>
              </tr>
              <tr>
                <th>Blood Group</th>
                <td>B+</td>
              </tr>
              <tr>
                <th>Nationality</th>
                <td>Bangladeshi</td>
              </tr>
            </tbody>
          </table>

          <!-- ADDRESS -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-geo-alt-fill me-1"></i> Address
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Present Address</th>
                <td>Bangladesh, Chauhali, Sirajganj, Khulna</td>
              </tr>
              <tr>
                <th>Village Area</th>
                <td>Nagrapara</td>
              </tr>
              <tr>
                <th>Permanent Address</th>
                <td>Bangladesh, Sadarsouth, Comilla, Chattagram</td>
              </tr>
              <tr>
                <th>Grew Up</th>
                <td>Khulna</td>
              </tr>
            </tbody>
          </table>

          <!-- EDUCATION -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-book-fill me-1"></i> Educational Qualifications
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Education Method</th>
                <td>General</td>
              </tr>
              <tr>
                <th>Highest Qualification</th>
                <td>SSC</td>
              </tr>
              <tr>
                <th>Diploma Subject</th>
                <td>Computer Science and Engineering</td>
              </tr>
              <tr>
                <th>Institution</th>
                <td>Barguna Polytechnic Institute</td>
              </tr>
              <tr>
                <th>Passing Year</th>
                <td>2018</td>
              </tr>
            </tbody>
          </table>

          <!-- PERSONAL INFO -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-person-fill me-1"></i> Personal Information
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Beard Info</th>
                <td>Yes, last 2 years</td>
              </tr>
              <tr>
                <th>Quran Recitation</th>
                <td>Yes</td>
              </tr>
              <tr>
                <th>Fiqh Followed</th>
                <td>Hanafi</td>
              </tr>
              <tr>
                <th>Books Read</th>
                <td>Bukhari, Al Quran</td>
              </tr>
            </tbody>
          </table>

          <!-- FAMILY INFO -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-people-fill me-1"></i> Family Information
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Father's Name</th>
                <td>Abdul Haque</td>
              </tr>
              <tr>
                <th>Mother's Name</th>
                <td>Maksuda Begum</td>
              </tr>
              <tr>
                <th>Brothers</th>
                <td>2</td>
              </tr>
              <tr>
                <th>Sisters</th>
                <td>0</td>
              </tr>
            </tbody>
          </table>

          <!-- CONTACT -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-telephone-fill me-1"></i> Contact
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Mobile</th>
                <td>01768987710</td>
              </tr>
              <tr>
                <th>Email</th>
                <td>mdshafiqulislam822@gmail.com</td>
              </tr>
              <tr>
                <th>Guardian Mobile</th>
                <td>01761817602</td>
              </tr>
              <tr>
                <th>Relationship</th>
                <td>Sister</td>
              </tr>
            </tbody>
          </table>

          <!-- BOTTOM HIDE BUTTON -->
          <div class="bottom-toggle">
            <button
              class="btn btn-outline-danger btn-sm mt-3"
              data-bs-toggle="collapse"
              data-bs-target="#fullProfile"
            >
              <i class="bi bi-chevron-up me-1"></i> Hide Full Profile
            </button>
          </div>
        </div>
      </div>
       <!-- PROFILE CARD -->
      <div class="card shadow-sm p-3 profile-card mb-3">
        <div class="d-flex align-items-start gap-3">
          <!-- LEFT: IMAGE SLIDER -->
          <div class="carousel-container">
            <div
              id="profileCarousel"
              class="carousel slide"
              data-bs-ride="carousel"
            >
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img
                    src="https://i.pinimg.com/564x/02/e7/f5/02e7f5464591ce514fffbdf03b287eed.jpg"
                    alt="Profile Image 1"
                  />
                </div>
                <div class="carousel-item">
                  <img
                    src="https://i.pinimg.com/736x/7d/9e/4b/7d9e4b710a28f342febffb0be5d88519.jpg"
                    alt="Profile Image 2"
                  />
                </div>
                <div class="carousel-item">
                  <img
                    src="https://i.pinimg.com/736x/b6/86/b8/b686b8e70200aacc92ad78b70b866416.jpg"
                    alt="Profile Image 3"
                  />
                </div>
              </div>
              <div class="carousel-indicators position-relative mt-2">
                <button
                  type="button"
                  data-bs-target="#profileCarousel"
                  data-bs-slide-to="0"
                  class="active"
                ></button>
                <button
                  type="button"
                  data-bs-target="#profileCarousel"
                  data-bs-slide-to="1"
                ></button>
                <button
                  type="button"
                  data-bs-target="#profileCarousel"
                  data-bs-slide-to="2"
                ></button>
              </div>
            </div>
          </div>

          <!-- RIGHT: SHORT PROFILE INFO -->
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h5 class="fw-bold mb-1">Md Shafiqul Islam</h5>
                <p class="text-muted mb-1">
                  <i class="bi bi-geo-alt-fill"></i> Khulna, Bangladesh
                </p>
                <p class="mb-1">
                  <strong>Age:</strong> 27 | <strong>Height:</strong> 5.4
                </p>
                <p class="mb-0">
                  <strong>Occupation:</strong> Lorem ipsum dolor sit amet
                </p>
              </div>

              <div>
                <button
                  class="btn btn-outline-primary btn-sm btn-toggle"
                  data-bs-toggle="collapse"
                  data-bs-target="#fullProfile"
                  aria-expanded="false"
                  id="toggleButton"
                >
                  <i class="bi bi-person-lines-fill me-1"></i>
                  <span>View Full Profile</span>
                  <i class="bi bi-chevron-down ms-1" id="toggleIcon"></i>
                </button>
              </div>
            </div>

            <!-- ICON BAR MOVED TO TOP -->
            <div class="icon-bar d-flex justify-content-start gap-3 mt-2">
              <i class="bi bi-telephone-fill" title="Call"></i>
              <i class="bi bi-envelope-fill" title="Email"></i>
              <i class="bi bi-heart-fill" title="Favorite"></i>
              <i class="bi bi-share-fill" title="Share"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- COLLAPSIBLE FULL PROFILE -->
      <div class="collapse" id="fullProfile">
        <div class="card card-body shadow-sm">
          <!-- GENERAL INFO -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-info-circle-fill me-1"></i> General Info
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Biodata Type</th>
                <td>Male</td>
              </tr>
              <tr>
                <th>Marital Status</th>
                <td>Married</td>
              </tr>
              <tr>
                <th>Birth Date</th>
                <td>1998-01-01</td>
              </tr>
              <tr>
                <th>Height</th>
                <td>5.4</td>
              </tr>
              <tr>
                <th>Complexion</th>
                <td>Dark</td>
              </tr>
              <tr>
                <th>Weight</th>
                <td>63</td>
              </tr>
              <tr>
                <th>Blood Group</th>
                <td>B+</td>
              </tr>
              <tr>
                <th>Nationality</th>
                <td>Bangladeshi</td>
              </tr>
            </tbody>
          </table>

          <!-- ADDRESS -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-geo-alt-fill me-1"></i> Address
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Present Address</th>
                <td>Bangladesh, Chauhali, Sirajganj, Khulna</td>
              </tr>
              <tr>
                <th>Village Area</th>
                <td>Nagrapara</td>
              </tr>
              <tr>
                <th>Permanent Address</th>
                <td>Bangladesh, Sadarsouth, Comilla, Chattagram</td>
              </tr>
              <tr>
                <th>Grew Up</th>
                <td>Khulna</td>
              </tr>
            </tbody>
          </table>

          <!-- EDUCATION -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-book-fill me-1"></i> Educational Qualifications
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Education Method</th>
                <td>General</td>
              </tr>
              <tr>
                <th>Highest Qualification</th>
                <td>SSC</td>
              </tr>
              <tr>
                <th>Diploma Subject</th>
                <td>Computer Science and Engineering</td>
              </tr>
              <tr>
                <th>Institution</th>
                <td>Barguna Polytechnic Institute</td>
              </tr>
              <tr>
                <th>Passing Year</th>
                <td>2018</td>
              </tr>
            </tbody>
          </table>

          <!-- PERSONAL INFO -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-person-fill me-1"></i> Personal Information
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Beard Info</th>
                <td>Yes, last 2 years</td>
              </tr>
              <tr>
                <th>Quran Recitation</th>
                <td>Yes</td>
              </tr>
              <tr>
                <th>Fiqh Followed</th>
                <td>Hanafi</td>
              </tr>
              <tr>
                <th>Books Read</th>
                <td>Bukhari, Al Quran</td>
              </tr>
            </tbody>
          </table>

          <!-- FAMILY INFO -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-people-fill me-1"></i> Family Information
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Father's Name</th>
                <td>Abdul Haque</td>
              </tr>
              <tr>
                <th>Mother's Name</th>
                <td>Maksuda Begum</td>
              </tr>
              <tr>
                <th>Brothers</th>
                <td>2</td>
              </tr>
              <tr>
                <th>Sisters</th>
                <td>0</td>
              </tr>
            </tbody>
          </table>

          <!-- CONTACT -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-telephone-fill me-1"></i> Contact
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Mobile</th>
                <td>01768987710</td>
              </tr>
              <tr>
                <th>Email</th>
                <td>mdshafiqulislam822@gmail.com</td>
              </tr>
              <tr>
                <th>Guardian Mobile</th>
                <td>01761817602</td>
              </tr>
              <tr>
                <th>Relationship</th>
                <td>Sister</td>
              </tr>
            </tbody>
          </table>

          <!-- BOTTOM HIDE BUTTON -->
          <div class="bottom-toggle">
            <button
              class="btn btn-outline-danger btn-sm mt-3"
              data-bs-toggle="collapse"
              data-bs-target="#fullProfile"
            >
              <i class="bi bi-chevron-up me-1"></i> Hide Full Profile
            </button>
          </div>
        </div>
      </div>
       <!-- PROFILE CARD -->
      <div class="card shadow-sm p-3 profile-card mb-3">
        <div class="d-flex align-items-start gap-3">
          <!-- LEFT: IMAGE SLIDER -->
          <div class="carousel-container">
            <div
              id="profileCarousel"
              class="carousel slide"
              data-bs-ride="carousel"
            >
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img
                    src="https://i.pinimg.com/564x/02/e7/f5/02e7f5464591ce514fffbdf03b287eed.jpg"
                    alt="Profile Image 1"
                  />
                </div>
                <div class="carousel-item">
                  <img
                    src="https://i.pinimg.com/736x/7d/9e/4b/7d9e4b710a28f342febffb0be5d88519.jpg"
                    alt="Profile Image 2"
                  />
                </div>
                <div class="carousel-item">
                  <img
                    src="https://i.pinimg.com/736x/b6/86/b8/b686b8e70200aacc92ad78b70b866416.jpg"
                    alt="Profile Image 3"
                  />
                </div>
              </div>
              <div class="carousel-indicators position-relative mt-2">
                <button
                  type="button"
                  data-bs-target="#profileCarousel"
                  data-bs-slide-to="0"
                  class="active"
                ></button>
                <button
                  type="button"
                  data-bs-target="#profileCarousel"
                  data-bs-slide-to="1"
                ></button>
                <button
                  type="button"
                  data-bs-target="#profileCarousel"
                  data-bs-slide-to="2"
                ></button>
              </div>
            </div>
          </div>

          <!-- RIGHT: SHORT PROFILE INFO -->
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h5 class="fw-bold mb-1">Md Shafiqul Islam</h5>
                <p class="text-muted mb-1">
                  <i class="bi bi-geo-alt-fill"></i> Khulna, Bangladesh
                </p>
                <p class="mb-1">
                  <strong>Age:</strong> 27 | <strong>Height:</strong> 5.4
                </p>
                <p class="mb-0">
                  <strong>Occupation:</strong> Lorem ipsum dolor sit amet
                </p>
              </div>

              <div>
                <button
                  class="btn btn-outline-primary btn-sm btn-toggle"
                  data-bs-toggle="collapse"
                  data-bs-target="#fullProfile"
                  aria-expanded="false"
                  id="toggleButton"
                >
                  <i class="bi bi-person-lines-fill me-1"></i>
                  <span>View Full Profile</span>
                  <i class="bi bi-chevron-down ms-1" id="toggleIcon"></i>
                </button>
              </div>
            </div>

            <!-- ICON BAR MOVED TO TOP -->
            <div class="icon-bar d-flex justify-content-start gap-3 mt-2">
              <i class="bi bi-telephone-fill" title="Call"></i>
              <i class="bi bi-envelope-fill" title="Email"></i>
              <i class="bi bi-heart-fill" title="Favorite"></i>
              <i class="bi bi-share-fill" title="Share"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- COLLAPSIBLE FULL PROFILE -->
      <div class="collapse" id="fullProfile">
        <div class="card card-body shadow-sm">
          <!-- GENERAL INFO -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-info-circle-fill me-1"></i> General Info
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Biodata Type</th>
                <td>Male</td>
              </tr>
              <tr>
                <th>Marital Status</th>
                <td>Married</td>
              </tr>
              <tr>
                <th>Birth Date</th>
                <td>1998-01-01</td>
              </tr>
              <tr>
                <th>Height</th>
                <td>5.4</td>
              </tr>
              <tr>
                <th>Complexion</th>
                <td>Dark</td>
              </tr>
              <tr>
                <th>Weight</th>
                <td>63</td>
              </tr>
              <tr>
                <th>Blood Group</th>
                <td>B+</td>
              </tr>
              <tr>
                <th>Nationality</th>
                <td>Bangladeshi</td>
              </tr>
            </tbody>
          </table>

          <!-- ADDRESS -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-geo-alt-fill me-1"></i> Address
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Present Address</th>
                <td>Bangladesh, Chauhali, Sirajganj, Khulna</td>
              </tr>
              <tr>
                <th>Village Area</th>
                <td>Nagrapara</td>
              </tr>
              <tr>
                <th>Permanent Address</th>
                <td>Bangladesh, Sadarsouth, Comilla, Chattagram</td>
              </tr>
              <tr>
                <th>Grew Up</th>
                <td>Khulna</td>
              </tr>
            </tbody>
          </table>

          <!-- EDUCATION -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-book-fill me-1"></i> Educational Qualifications
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Education Method</th>
                <td>General</td>
              </tr>
              <tr>
                <th>Highest Qualification</th>
                <td>SSC</td>
              </tr>
              <tr>
                <th>Diploma Subject</th>
                <td>Computer Science and Engineering</td>
              </tr>
              <tr>
                <th>Institution</th>
                <td>Barguna Polytechnic Institute</td>
              </tr>
              <tr>
                <th>Passing Year</th>
                <td>2018</td>
              </tr>
            </tbody>
          </table>

          <!-- PERSONAL INFO -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-person-fill me-1"></i> Personal Information
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Beard Info</th>
                <td>Yes, last 2 years</td>
              </tr>
              <tr>
                <th>Quran Recitation</th>
                <td>Yes</td>
              </tr>
              <tr>
                <th>Fiqh Followed</th>
                <td>Hanafi</td>
              </tr>
              <tr>
                <th>Books Read</th>
                <td>Bukhari, Al Quran</td>
              </tr>
            </tbody>
          </table>

          <!-- FAMILY INFO -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-people-fill me-1"></i> Family Information
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Father's Name</th>
                <td>Abdul Haque</td>
              </tr>
              <tr>
                <th>Mother's Name</th>
                <td>Maksuda Begum</td>
              </tr>
              <tr>
                <th>Brothers</th>
                <td>2</td>
              </tr>
              <tr>
                <th>Sisters</th>
                <td>0</td>
              </tr>
            </tbody>
          </table>

          <!-- CONTACT -->
          <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">
            <i class="bi bi-telephone-fill me-1"></i> Contact
          </h6>
          <table class="table table-sm table-bordered mb-3">
            <tbody>
              <tr>
                <th>Mobile</th>
                <td>01768987710</td>
              </tr>
              <tr>
                <th>Email</th>
                <td>mdshafiqulislam822@gmail.com</td>
              </tr>
              <tr>
                <th>Guardian Mobile</th>
                <td>01761817602</td>
              </tr>
              <tr>
                <th>Relationship</th>
                <td>Sister</td>
              </tr>
            </tbody>
          </table>

          <!-- BOTTOM HIDE BUTTON -->
          <div class="bottom-toggle">
            <button
              class="btn btn-outline-danger btn-sm mt-3"
              data-bs-toggle="collapse"
              data-bs-target="#fullProfile"
            >
              <i class="bi bi-chevron-up me-1"></i> Hide Full Profile
            </button>
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

      fullProfile.addEventListener("show.bs.collapse", () => {
        toggleIcon.classList.replace("bi-chevron-down", "bi-chevron-up");
        toggleText.textContent = "Hide Full Profile";
      });

      fullProfile.addEventListener("hide.bs.collapse", () => {
        toggleIcon.classList.replace("bi-chevron-up", "bi-chevron-down");
        toggleText.textContent = "View Full Profile";
      });
    </script>

@endsection
