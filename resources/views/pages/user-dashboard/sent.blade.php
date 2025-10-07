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

    /* Empty message section */
    .empty-section {
      margin-top: 20px;
      padding-top: 15px;
      border-top: 1px solid #ddd;
      text-align: center;
      color: #777;
      font-size: 0.9rem;
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

    </div>
  </div>
</div>



@endsection

@section('content')
    <div class="container-fluid">
  <div class="row">
    <div class="col-10 p-3">
     
      <!-- Multiple Message Cards -->
      <div class="message-card card shadow-sm mb-4 p-3">
        <div class="d-flex">
          <div class="profile-photo me-3">
            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Profile Photo">
          </div>
          <div class="flex-grow-1">
            <h5 class="card-title d-flex align-items-center mb-1">
              <input type="checkbox" class="form-check-input me-2">
              Sharmin Sultana <span class="text-muted fs-6 ms-1">(BGD3802360)</span>
            </h5>
            <p class="mb-2 small">
              Age: 27 Yrs • Height: 5 ft 4 in / 162 cm • Islam (Sunni) • Dhaka, Bangladesh • Bachelors • Student
            </p>
            <div class="mb-2 card-links">
              <a href="#">View Full Profile</a> | <a href="#">Communication History</a>
            </div>
            <div class="message-box">
              <p><strong>You have sent an interest on 2nd September 2025</strong></p>
              <p>I like your profile and I want to get in touch with you. Please accept if interested.</p>
            </div>
            <button class="btn btn-sm btn-reminder">Send Reminder</button>
          </div>
        </div>
      </div>

      <div class="message-card card shadow-sm mb-4 p-3">
        <div class="d-flex">
          <div class="profile-photo me-3">
            <img src="https://randomuser.me/api/portraits/men/36.jpg" alt="Profile Photo">
          </div>
          <div class="flex-grow-1">
            <h5 class="card-title d-flex align-items-center mb-1">
              <input type="checkbox" class="form-check-input me-2">
              Ahsan Habib <span class="text-muted fs-6 ms-1">(BGD3791925)</span>
            </h5>
            <p class="mb-2 small">
              Age: 30 Yrs • Height: 5 ft 9 in / 175 cm • Islam (Sunni) • Chittagong, Bangladesh • Masters • Engineer
            </p>
            <div class="mb-2 card-links">
              <a href="#">View Full Profile</a> | <a href="#">Communication History</a>
            </div>
            <div class="message-box">
              <p><strong>Interest received on 30th August 2025</strong></p>
              <p>I found your profile very interesting. Let’s connect for further discussions.</p>
            </div>
            <button class="btn btn-sm btn-reminder">Accept Interest</button>
          </div>
        </div>
      </div>

      <div class="message-card card shadow-sm mb-4 p-3">
        <div class="d-flex">
          <div class="profile-photo me-3">
            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Profile Photo">
          </div>
          <div class="flex-grow-1">
            <h5 class="card-title d-flex align-items-center mb-1">
              <input type="checkbox" class="form-check-input me-2">
              Nusrat Jahan <span class="text-muted fs-6 ms-1">(BGD3811450)</span>
            </h5>
            <p class="mb-2 small">
              Age: 25 Yrs • Height: 5 ft 3 in / 160 cm • Islam (Sunni) • Sylhet, Bangladesh • Bachelors • Teacher
            </p>
            <div class="mb-2 card-links">
              <a href="#">View Full Profile</a> | <a href="#">Communication History</a>
            </div>
            <div class="message-box">
              <p><strong>You sent interest on 1st September 2025</strong></p>
              <p>Looking forward to hearing from you. Kindly respond if interested.</p>
            </div>
            <button class="btn btn-sm btn-reminder">Send Reminder</button>
          </div>
        </div>
      </div>

      <!-- Bottom Controls -->
      <div class="subnavbar d-flex justify-content-between align-items-center">
        <div class="actions">
          <a href="#" class="action-link">Select All</a> | <a href="#">Delete All</a>
        </div>
        <small class="text-muted">Showing Page 1 of 3</small>
      </div>
    </div>
  </div>
</div>

<style>
/* Subnavbar styling */
.subnavbar {
  background: #f9fbfd;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 6px 12px;
  margin-bottom: 10px;
}
.btn-received, .btn-sent {
  border-radius: 20px;
  font-weight: 500;
  transition: 0.3s;
}
.btn-received {
  background-color: #0d6efd;
  color: white;
}
.btn-received:hover, .btn-received.active {
  background-color: #0b5ed7;
}
.btn-sent {
  background-color: #6c757d;
  color: white;
}
.btn-sent:hover, .btn-sent.active {
  background-color: #5c636a;
}
.actions .action-link {
  color: #0d6efd;
  font-weight: 500;
  text-decoration: none;
}
.actions .action-link:hover {
  text-decoration: underline;
}

/* Profile photo styling */
.profile-photo img {
  width: 90px;
  height: 90px;
  object-fit: cover;
  border: 3px solid #ddd;
  border-radius: 50%; /* circular photo */
}

/* Card hover effect */
.message-card {
  border-radius: 10px;
  transition: all 0.3s ease;
}
.message-card:hover {
  border-color: #0d6efd;
  box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
}

/* Card links styling */
.card-links a {
  color: #0d6efd;
  font-weight: 500;
  text-decoration: none;
}
.card-links a:hover {
  text-decoration: underline;
}

/* Message box styling */
.message-box {
  background-color: #e9f5ff;
  border-left: 4px solid #0d6efd;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 10px;
  font-size: 14px;
}

/* Reminder button styling */
.btn-reminder {
  background-color: #198754;
  color: white;
  border-radius: 20px;
  padding: 5px 15px;
  font-size: 13px;
  transition: background-color 0.3s;
}
.btn-reminder:hover {
  background-color: #157347;
}
</style>

@endsection