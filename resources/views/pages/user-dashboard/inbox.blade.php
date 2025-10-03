@extends('layouts.user-dashboard-app')

@section('title', 'MATCHES')

@push('styles')
    <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
    }
    .sidebar {
      width: 260px;
      min-height: 100vh;
      background: #fff;
      border-right: 1px solid #ddd;
      padding: 20px 10px;
    }
    .accordion-button {
      font-size: 0.95rem;
      font-weight: 600;
      color: #444;
      background: #f8f9fa;
      border: none;
      box-shadow: none !important;
    }
    .accordion-button:not(.collapsed) {
      color: #007bff;
      background: #eef5ff;
    }
    .filter-item {
      display: block;
      padding: 8px 15px;
      margin: 3px 0;
      border-radius: 6px;
      font-size: 0.9rem;
      color: #333;
      text-decoration: none;
      transition: all 0.2s;
    }
    .filter-item:hover,
    .filter-item.active {
      background: #f1f1f1;
      color: #007bff;
      font-weight: 600;
    }


     /* Subnavbar */
    .subnavbar {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 10px 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    /* Route buttons */
    .route-switch {
      display: flex;
      border: 1px solid #ddd;
      border-radius: 25px;
      overflow: hidden;
      background: #f8f9fa;
    }
    .route-switch a {
      flex: 1;
      text-align: center;
      padding: 8px 20px;
      font-size: 0.95rem;
      font-weight: 600;
      color: #555;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    .route-switch a.active {
      background: #007bff;
      color: #fff;
    }
    .route-switch a:hover {
      background: #e9ecef;
      color: #007bff;
    }
    .subnavbar-right .btn {
      margin-left: 8px;
    }

    /* Filtered messages section */
    .filter-section {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px 20px;
      margin-top: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }
    .filter-section label {
      font-size: 0.95rem;
      font-weight: 500;
      color: #444;
    }
    .edit-preference {
      border: 1px solid #007bff;
      border-radius: 20px;
      padding: 6px 15px;
      font-size: 0.85rem;
      font-weight: 600;
      color: #007bff;
      text-decoration: none;
      transition: 0.3s;
    }
    .edit-preference:hover {
      background: #007bff;
      color: #fff;
      text-decoration: none;
    }
 /* Section Wrapper */
    section.inbox-section {
      background: #f9fafb;
      padding: 25px;
      border-radius: 16px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
   
      max-width: 845px;  /* fixed width, smaller */
    }

    /* Profile Card */
    .profile-card {
      display: flex;
      align-items: center;
      gap: 20px;
      padding: 18px;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      background: #fff;
      margin-bottom: 16px;
      box-shadow: 0px 3px 8px rgba(0,0,0,0.07);
      transition: 0.3s;
    }
    .profile-card:hover {
      box-shadow: 0px 5px 14px rgba(0,0,0,0.12);
    }

    /* Profile Image */
    .profile-img {
      width: 85px;
      height: 85px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #ddd;
    }

    /* Info Section */
    .profile-info {
      flex: 1;
      font-size: 14px;
    }
    .profile-info h6 {
      margin: 0 0 6px;
      font-size: 17px;
      font-weight: 600;
      color: #222;
    }
    .meta {
      font-size: 13px;
      color: #555;
      margin-bottom: 4px;
    }
    .status {
      font-size: 13px;
      color: #16a34a; /* green */
      font-weight: 500;
    }

    /* Actions */
    .profile-actions {
      text-align: right;
      min-width: 120px;
    }
    .profile-actions button {
      margin: 4px 0;
      padding: 5px 12px;
      font-size: 13px;
      border-radius: 8px;
      display: block;
      width: 100%;
    }

    /* Footer Control */
    .inbox-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 10px;
      border-top: 1px solid #e5e7eb;
      margin-top: 12px;
    }
    .inbox-footer label {
      font-size: 14px;
      color: #333;
    }

  </style>
@endpush

@section('sidebar')
    <div class="sidebar">
    <div class="accordion" id="inboxFilters">

      <!-- Pending -->
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pending">
            Pending
          </button>
        </h2>
        <div id="pending" class="accordion-collapse collapse" data-bs-parent="#inboxFilters">
          <div class="accordion-body p-2">
            <a href="#" class="filter-item active">All</a>
            <a href="#" class="filter-item">Interests</a>
            <a href="#" class="filter-item">Messages</a>
          </div>
        </div>
      </div>

      <!-- Accepted -->
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accepted">
            Accepted
          </button>
        </h2>
        <div id="accepted" class="accordion-collapse collapse" data-bs-parent="#inboxFilters">
          <div class="accordion-body p-2">
            <a href="#" class="filter-item">Interests</a>
          </div>
        </div>
      </div>

      <!-- Declined -->
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#declined">
            Declined
          </button>
        </h2>
        <div id="declined" class="accordion-collapse collapse" data-bs-parent="#inboxFilters">
          <div class="accordion-body p-2">
            <a href="#" class="filter-item">All</a>
            <a href="#" class="filter-item">Interests</a>
            <a href="#" class="filter-item">Messages</a>
          </div>
        </div>
      </div>

      <!-- Replied -->
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#replied">
            Replied
          </button>
        </h2>
        <div id="replied" class="accordion-collapse collapse" data-bs-parent="#inboxFilters">
          <div class="accordion-body p-2">
            <a href="#" class="filter-item">Messages</a>
          </div>
        </div>
      </div>

      <!-- Requests -->
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#requests">
            Requests
          </button>
        </h2>
        <div id="requests" class="accordion-collapse collapse" data-bs-parent="#inboxFilters">
          <div class="accordion-body p-2">
            <a href="#" class="filter-item">Chat History</a>
          </div>
        </div>
      </div>

    </div>
  </div>
@endsection

@section('subnavbar')
  <div class="container-fluid">
  <div class="row">
    <!-- Right Content Only (col-9) -->
    <div class="col-lg-10 p-3">

      <!-- Subnavbar -->
      <div class="subnavbar">
        <div class="route-switch">
          <a href="{{route('inbox')}}" class="active">Received</a>
          <a href="{{route('sent')}}">Sent</a>
        </div>
        <div class="subnavbar-right">
          <button class="btn btn-outline-secondary btn-sm">Select All</button>
          <button class="btn btn-outline-danger btn-sm">Delete All</button>
        </div>
      </div>

      <!-- Filtered Messages Section -->
      <div class="filter-section">
        <div class="d-flex align-items-center">
          <input type="checkbox" id="filtered" class="form-check-input me-2">
          <label for="filtered">Show filtered messages</label>
        </div>
        <a href="#" class="edit-preference">Edit Partner Preference</a>
      </div>

      

    </div>
  </div>
</div>



@endsection

@section('content')
<div class="container-fluid my-4">
  <section class="inbox-section">

    <!-- Card 1 -->
    <div class="profile-card">
      <img src="https://images.pexels.com/photos/1130626/pexels-photo-1130626.jpeg" alt="Profile" class="profile-img">
      <div class="profile-info">
        <h6>Aliya (BGD3790806)</h6>
        <p class="meta">Age: 27 | Height: 5 ft | Religion: Islam | Education: Masters | Occupation: Manager</p>
        <p class="status">📩 Member's Interest received today!</p>
      </div>
      <div class="profile-actions">
        <button class="btn btn-sm btn-success">Accept</button>
        <button class="btn btn-sm btn-outline-danger">Decline</button>
      </div>
    </div>

    <!-- Card 2 -->
    <div class="profile-card">
      <img src="https://images.pexels.com/photos/1130626/pexels-photo-1130626.jpeg" alt="Profile" class="profile-img">
      <div class="profile-info">
        <h6>Sharmin (BGD3802360)</h6>
        <p class="meta">Age: 25 | Height: 5.2 ft | Religion: Islam | Education: Bachelors | Occupation: Student</p>
        <p class="status">📩 Interest received on 3rd Sep 2025</p>
      </div>
      <div class="profile-actions">
        <button class="btn btn-sm btn-success">Accept</button>
        <button class="btn btn-sm btn-outline-danger">Decline</button>
      </div>
    </div>

    <!-- Card 3 -->
    <div class="profile-card">
      <img src="https://images.pexels.com/photos/1130626/pexels-photo-1130626.jpeg" alt="Profile" class="profile-img">
      <div class="profile-info">
        <h6>Tania (BGD3787831)</h6>
        <p class="meta">Age: 23 | Height: 5.2 ft | Religion: Islam | Education: Bachelors | Occupation: Student</p>
        <p class="status">📩 Chat request received 2nd Sep 2025</p>
      </div>
      <div class="profile-actions">
        <button class="btn btn-sm btn-success">Accept</button>
        <button class="btn btn-sm btn-outline-danger">Decline</button>
      </div>
    </div>

    <!-- Footer -->
    <div class="inbox-footer">
      <label><input type="checkbox"> Select All</label>
      <button class="btn btn-sm btn-danger">Delete All</button>
    </div>

  </section>
</div>

@endsection