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
</style>
@endpush

@section('content')

<div class="container py-4">

  <!-- Profile Header with Carousel -->
  <div class="profile-header">
    <div id="profileCarousel" class="carousel slide profile-carousel" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="https://cdn.pixabay.com/photo/2024/05/26/10/15/bird-8788491_1280.jpg" class="d-block zoomable" alt="Profile Photo 1" data-bs-toggle="modal" data-bs-target="#imageModal">
        </div>
        <div class="carousel-item">
          <img src="https://imgv3.fotor.com/images/slider-image/A-clear-close-up-photo-of-a-woman.jpg" class="d-block zoomable" alt="Profile Photo 2" data-bs-toggle="modal" data-bs-target="#imageModal">
        </div>
        <div class="carousel-item">
          <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTE-U-_rFFQnMES2iy_acaFEO4xsAMwA6loUxoW5QrS-DjE82s260icoMiD63F0MV0yQwc&usqp=CAU" class="d-block zoomable" alt="Profile Photo 3" data-bs-toggle="modal" data-bs-target="#imageModal">
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#profileCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#profileCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
    <h3>Rahul Sharma</h3>
    <p>29 yrs, 5 ft 8 in / 173 cm | Mumbai, India</p>
    <button class="btn btn-light btn-sm"><i class="bi bi-pencil-square"></i> Edit Profile</button>
  </div>

  <!-- Profile Box -->
  <div class="profile-box">

    <div class="section-heading"><i class="bi bi-person-fill"></i> General Info</div>
    <ul class="list-unstyled info-list">
      <li><b>Full Name:</b> Rahul Sharma</li>
      <li><b>Age:</b> 29 yrs</li>
      <li><b>Gender:</b> Male</li>
      <li><b>Mother Tongue:</b> Hindi</li>
    </ul>

    <div class="section-heading"><i class="bi bi-geo-alt-fill"></i> Address</div>
    <p>Mumbai, Maharashtra, India</p>

    <div class="section-heading"><i class="bi bi-mortarboard-fill"></i> Educational Qualifications</div>
    <p>MBA in Finance from XYZ University</p>

    <div class="section-heading"><i class="bi bi-people-fill"></i> Family Information</div>
    <ul class="list-unstyled info-list">
      <li><b>Father:</b> Businessman</li>
      <li><b>Mother:</b> Homemaker</li>
      <li><b>Siblings:</b> 1 Brother, 1 Sister</li>
    </ul>

    <div class="section-heading"><i class="bi bi-heart-fill"></i> Personal Information</div>
    <ul class="list-unstyled info-list">
      <li><b>Marital Status:</b> Unmarried</li>
      <li><b>Religion:</b> Hindu</li>
      <li><b>Height:</b> 5 ft 8 in</li>
      <li><b>Weight:</b> 70 kg</li>
    </ul>

    <div class="section-heading"><i class="bi bi-briefcase-fill"></i> Occupational Information</div>
    <ul class="list-unstyled info-list">
      <li><b>Occupation:</b> Software Engineer</li>
      <li><b>Company:</b> ABC Pvt Ltd</li>
      <li><b>Annual Income:</b> ₹12,00,000</li>
    </ul>

    <div class="section-heading"><i class="bi bi-flower1"></i> Marriage Related Information</div>
    <p>Looking for a life partner who values family, culture, and career growth.</p>

    <div class="section-heading"><i class="bi bi-gem"></i> Expected Life Partner</div>
    <ul class="list-unstyled info-list">
      <li><b>Age:</b> 24 to 28 yrs</li>
      <li><b>Height:</b> 5 ft 0 in to 5 ft 8 in</li>
      <li><b>Education:</b> Graduate & Above</li>
      <li><b>Marital Status:</b> Unmarried</li>
    </ul>

    <div class="section-heading"><i class="bi bi-journal-check"></i> Pledge</div>
    <p>I pledge to respect, support, and care for my partner through all phases of life.</p>

    <div class="section-heading"><i class="bi bi-telephone-fill"></i> Contact</div>
    <ul class="list-unstyled info-list">
      <li><b>Phone:</b> +91-9876543210</li>
      <li><b>Email:</b> rahul.sharma@example.com</li>
    </ul>
    <button class="btn btn-success btn-sm"><i class="bi bi-whatsapp"></i> WhatsApp</button>
    <button class="btn btn-primary btn-sm"><i class="bi bi-chat-dots"></i> Chat Now</button>

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
    });
  });
</script>

@endsection
