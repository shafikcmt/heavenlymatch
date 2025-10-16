@extends('layouts.user-dashboard-app')

@section('title', 'All Profiles')

@push('styles')
<style>
  body { background: #f8f9fa; }
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
    margin-bottom: 30px; 
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
  }
  .section-heading { 
    background: #f1f3f5; 
    font-weight: 600; 
    font-size: 16px; 
    padding: 10px 12px; 
    border-left: 4px solid #007bff; 
    margin: 20px 0 10px 0; 
  }
  .info-list li { margin-bottom: 8px; font-size: 15px; }
</style>
@endpush

@section('content')

<div class="container py-4">

  @foreach($data as $biodata)
  <!-- Profile Header -->
  <div class="profile-header">
    <div class="profile-carousel">
      <img src="{{ asset('storage/' . ($biodata->groom_photo ?? 'groom_photos/default.png')) }}" 
     class="d-block zoomable" alt="Profile Photo">
    </div>

    <h3>{{ $biodata->name ?? $biodata->reg_name ?? 'Not Set' }}</h3>
    <p>{{ $biodata->height ?? 'N/A' }} | {{ $biodata->present_address ?? 'N/A' }}</p>
    <button class="btn btn-light btn-sm"><i class="bi bi-pencil-square"></i> Edit Profile</button>
  </div>

  <!-- Profile Box -->
  <div class="profile-box">

    <div class="section-heading"><i class="bi bi-person-fill"></i> General Info</div>
    <ul class="list-unstyled info-list">
      <li><b>Full Name:</b> {{ $biodata->name ?? $biodata->reg_name ?? 'N/A' }}</li>
      <li><b>Gender:</b> {{ $biodata->gender ?? 'N/A' }}</li>
      <li><b>Marital Status:</b> {{ ucfirst($biodata->marital_status ?? 'N/A') }}</li>
      <li><b>Nationality:</b> {{ $biodata->nationality ?? 'N/A' }}</li>
    </ul>

    <div class="section-heading"><i class="bi bi-geo-alt-fill"></i> Address</div>
    <p>{{ $biodata->present_address ?? 'N/A' }}</p>

    <div class="section-heading"><i class="bi bi-mortarboard-fill"></i> Education</div>
    <p>{{ $biodata->highest_qualification ?? 'N/A' }}</p>

    <div class="section-heading"><i class="bi bi-people-fill"></i> Family Info</div>
    <ul class="list-unstyled info-list">
      <li><b>Father:</b> {{ $biodata->father_name ?? 'N/A' }} ({{ $biodata->father_profession ?? 'N/A' }})</li>
      <li><b>Mother:</b> {{ $biodata->mother_name ?? 'N/A' }} ({{ $biodata->mother_profession ?? 'N/A' }})</li>
      <li><b>Siblings:</b> {{ $biodata->brothers ?? 0 }} Brother(s), {{ $biodata->sisters ?? 0 }} Sister(s)</li>
    </ul>

    <div class="section-heading"><i class="bi bi-briefcase-fill"></i> Occupation</div>
    <ul class="list-unstyled info-list">
      <li><b>Occupation:</b> {{ $biodata->occupation ?? 'N/A' }}</li>
      <li><b>Company:</b> {{ $biodata->profession_details ?? 'N/A' }}</li>
      <li><b>Monthly Income:</b> {{ $biodata->monthly_income ?? 'N/A' }}</li>
    </ul>

    <div class="section-heading"><i class="bi bi-envelope-fill"></i> Registration Info</div>
    <ul class="list-unstyled info-list">
      <li><b>Registered Name:</b> {{ $biodata->name ?? $biodata->reg_name ?? 'N/A' }}</li>
      <li><b>Email:</b> {{ $biodata->email ?? 'N/A' }}</li>
      <li><b>Gender (from registration):</b> {{ $biodata->gender ?? 'N/A' }}</li>
    </ul>

    <div class="section-heading"><i class="bi bi-telephone-fill"></i> Contact</div>
    <ul class="list-unstyled info-list">
      <li><b>Guardian Mobile:</b> {{ $biodata->guardian_mobile ?? 'N/A' }}</li>
      <li><b>Guardian Email:</b> {{ $biodata->guardian_email ?? 'N/A' }}</li>
    </ul>

  </div>
  @endforeach

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
  document.querySelectorAll(".zoomable").forEach(img => {
    img.addEventListener("click", function() {
      const modalImg = document.getElementById("zoomImage");
      modalImg.src = this.src;
      new bootstrap.Modal(document.getElementById('imageModal')).show();
    });
  });
</script>

@endsection
