@extends('layouts.user-dashboard-app')

@section('title', 'MATCHES')

@push('styles')
  <style>
 .profile-card {
    border-radius: 15px;
    overflow: hidden;
    background: #fff;
    margin-bottom: 25px;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  }
  .profile-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  }

  /* Image Border Style */
  .carousel-inner img {
    width: 100%;
    height: 230px;
    object-fit: cover;
    border: 4px solid #f1f1f1;
    border-radius: 12px;
    transition: transform 0.3s ease;
  }
  .carousel-inner img:hover {
    transform: scale(1.03);
  }

  /* Recently Joined Badge */
  .recently-joined {
    background: linear-gradient(45deg, #28a745, #218838);
    color: #fff;
    padding: 6px;
    text-align: center;
    font-size: 13px;
    font-weight: bold;
    border-radius: 0 0 12px 12px;
    margin-top: 6px;
  }

  /* Custom Carousel Controls at Bottom */
  .carousel-controls {
    text-align: center;
    margin-top: 8px;
  }
  .carousel-controls button {
    border: none;
    background: #007bff;
    color: #fff;
    padding: 6px 14px;
    border-radius: 50px;
    margin: 0 5px;
    transition: all 0.3s ease;
  }
  .carousel-controls button:hover {
    background: #0056b3;
    transform: scale(1.1);
  }

  /* Profile Actions */
  .profile-actions i {
    font-size: 20px;
    margin-left: 12px;
    cursor: pointer;
    transition: color 0.3s, transform 0.2s;
  }
  .profile-actions i:hover {
    color: #007bff;
    transform: scale(1.2);
  }

  /* About Text */
  .about-text {
    font-size: 14px;
    color: #444;
  }

  /* Button Spacing */
  .btn-space {
    margin-right: 10px;
    margin-top: 8px;
  }

  /* Filter Form */
  .filter-form {
    background: #fff;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
  }

  .profile-header {
        margin: 20px 0;
        font-weight: 500;
    }
    .profile-count {
        color: #007bff;
        font-weight: bold;
    }
    .tabs-container {
        margin-top: 10px;
    }
    .tabs-container .nav-tabs .nav-link.active {
        background-color: #007bff;
        color: #fff;
        border-radius: 5px 5px 0 0;
    }
    .tabs-container .nav-tabs .nav-link {
        border: 1px solid #dee2e6;
        margin-right: 5px;
        border-radius: 5px 5px 0 0;
    }
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        margin-bottom: 10px;
    }
    .action-bar .btn {
        margin-right: 5px;
    }

 
    .sidebar {
      width: 250px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 20px;
      font-size: 14px;
    }
    .sidebar h6 {
      font-weight: 700;
      margin-bottom: 12px;
      text-transform: uppercase;
      font-size: 13px;
      color: #444;
      border-bottom: 2px solid #0d6efd;
      padding-bottom: 5px;
    }
    .sort-option {
      margin-bottom: 8px;
    }
    .sort-option input {
      accent-color: #0d6efd;
    }
    .accordion-button {
      font-size: 14px;
      padding: 10px;
      background: #f8f9fa;
      border-radius: 6px !important;
      margin-bottom: 6px;
      box-shadow: none;
      transition: all 0.2s ease-in-out;
    }
    .accordion-button::after {
      font-family: "bootstrap-icons";
      content: "\f282"; /* plus icon */
      font-size: 14px;
      transform: rotate(0deg);
    }
    .accordion-button:not(.collapsed)::after {
      content: "\f286"; /* dash icon */
      transform: rotate(180deg);
    }
    .accordion-button:hover {
      background: #e9f2ff;
      color: #0d6efd;
    }
    .accordion-body {
      padding: 8px 15px;
    }
    .filter-list {
      list-style: none;
      padding-left: 0;
      margin-bottom: 0;
    }
    .filter-list li {
      margin-bottom: 6px;
    }
    .filter-list a {
      text-decoration: none;
      color: #333;
      font-size: 13px;
      transition: color 0.2s;
    }
    .filter-list a:hover {
      color: #0d6efd;
      text-decoration: underline;
    }
  </style>
@endpush

@section('sidebar')



<div class="col-lg-2 sidebar">
  
  <!-- Sort Section -->
  <h6><i class="bi bi-filter-circle me-1"></i> Sort</h6>
  <div class="mb-3">
    <div class="sort-option"><input type="radio" name="sort" checked> Relevance (Recommended)</div>
    <div class="sort-option"><input type="radio" name="sort"> Recently Active</div>
    <div class="sort-option"><input type="radio" name="sort"> Newest Profiles</div>
    <div class="sort-option"><input type="radio" name="sort"> Oldest Profiles</div>
  </div>
  
  <!-- Refine Search -->
  <h6><i class="bi bi-search me-1"></i> Refine Search</h6>
  <div class="accordion" id="filterAccordion">

    <!-- Profiles Created -->
    <div class="accordion-item border-0">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#created">
          Show Profiles Created
        </button>
      </h2>
      <div id="created" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
        <div class="accordion-body">
          <ul class="filter-list">
            <li><a href="#">Within a day (6)</a></li>
            <li><a href="#">Within a week (171)</a></li>
            <li><a href="#">Within a month (709)</a></li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Active -->
    <div class="accordion-item border-0">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#active">
          Active
        </button>
      </h2>
      <div id="active" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
        <div class="accordion-body">
          <ul class="filter-list">
            <li><a href="#">Online now (881)</a></li>
            <li><a href="#">One week ago (1299)</a></li>
            <li><a href="#">One month ago (2145)</a></li>
            <li><a href="#">One month+ (4930)</a></li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Profile Type -->
    <div class="accordion-item border-0">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ptype">
          Profile Type
        </button>
      </h2>
      <div id="ptype" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
        <div class="accordion-body">
          <ul class="filter-list">
            <li><a href="#">With Photo (1911)</a></li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Religion -->
    <div class="accordion-item border-0">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#religion">
          Religion
        </button>
      </h2>
      <div id="religion" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
        <div class="accordion-body">
          <ul class="filter-list">
            <li><a href="#">Hindu</a></li>
            <li><a href="#">Muslim</a></li>
            <li><a href="#">Christian</a></li>
          </ul>
        </div>
      </div>
    </div>

    <!-- More sections can be added like: Mother Tongue, Occupation, Income, etc. -->
    
  </div>
</div>

@endsection



@section('subnavbar')
<section>

<div class="col-md-10">
    <!-- Header -->
    <div class="profile-header">
        Yet to be viewed profiles 
        <span class="profile-count">(4930)</span>
    </div>

    <!-- Tabs -->
    <div class="tabs-container">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#">Preferred Profiles</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Matchwatch Profiles (4930)</a>
            </li>
        </ul>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div>
            <button class="btn btn-outline-primary btn-sm">Send Interest to All</button>
            <button class="btn btn-outline-secondary btn-sm">Shortlist</button>
        </div>
        <div>
            View: 
            <button class="btn btn-outline-secondary btn-sm">List</button>
            <button class="btn btn-outline-secondary btn-sm">Grid</button>
        </div>
    </div>

    
</div>

</section>
@endsection
@section('content')
<div class="container">
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="profile-card p-3">
        <div class="row g-3">
          
          <!-- Left Side (Image Slider with Border & Bottom Controls) -->
          <div class="col-md-4">
            <div id="profileCarousel" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="https://i.pinimg.com/564x/02/e7/f5/02e7f5464591ce514fffbdf03b287eed.jpg" class="d-block w-100" alt="Profile Image 1">
                </div>
                <div class="carousel-item">
                  <img src="https://i.pinimg.com/736x/7d/9e/4b/7d9e4b710a28f342febffb0be5d88519.jpg" class="d-block w-100" alt="Profile Image 2">
                </div>
                <div class="carousel-item">
                  <img src="https://i.pinimg.com/736x/b6/86/b8/b686b8e70200aacc92ad78b70b866416.jpg" class="d-block w-100" alt="Profile Image 3">
                </div>
                <div class="carousel-item">
                  <img src="https://i.pinimg.com/236x/e0/a5/06/e0a506828ba00d377d8fc5872612d84a.jpg" class="d-block w-100" alt="Profile Image 4">
                </div>
              </div>
              <!-- Custom Bottom Controls -->
              <div class="carousel-controls">
                <button type="button" data-bs-target="#profileCarousel" data-bs-slide="prev">
                  ‹ Prev
                </button>
                <button type="button" data-bs-target="#profileCarousel" data-bs-slide="next">
                  Next ›
                </button>
              </div>
            </div>
            <div class="recently-joined">✨ Recently Joined</div>
          </div>
          
          <!-- Right Side (Profile Info) -->
          <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
              <div>
                <h5 class="mb-1 fw-bold">Fariha Ferdous</h5>
                <p class="mb-1 text-muted"><i class="bi bi-geo-alt"></i> Dhaka [Dacca], Dhaka, Bangladesh</p>
              </div>
              <div class="profile-actions text-primary">
                <i class="bi bi-envelope"></i>
                <i class="bi bi-telephone"></i>
                <i class="bi bi-chat"></i>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-sm-6">
                <p><strong>Age, Height:</strong> 22 yrs, 5 ft 2 in / 157 cm</p>
                <p><strong>Religion:</strong> Islam</p>
                <p><strong>Sect:</strong> Sunni (Caste No Bar)</p>
              </div>
              <div class="col-sm-6">
                <p><strong>Education:</strong> Bachelors</p>
                <p><strong>Occupation:</strong> Student</p>
              </div>
            </div>

            <div class="d-flex align-items-center mt-3 flex-wrap">
              <span class="me-2 fw-semibold">Interested in her?</span>
              <button class="btn btn-warning btn-sm btn-space">Yes</button>
              <button class="btn btn-outline-secondary btn-sm btn-space">No</button>
            </div>

            <hr>
            <p class="about-text mb-0">
              My name is Fariha Ferdous. I’m doing my bachelor’s right now... 
              <a href="#" class="text-decoration-none fw-semibold">View Full Profile</a>
            </p>
          </div>
        </div>
      </div>

      <div class="profile-card p-3">
        <div class="row g-3">
          
          <!-- Left Side (Image Slider with Border & Bottom Controls) -->
          <div class="col-md-4">
            <div id="profileCarousel" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="https://i.pinimg.com/564x/02/e7/f5/02e7f5464591ce514fffbdf03b287eed.jpg" class="d-block w-100" alt="Profile Image 1">
                </div>
                <div class="carousel-item">
                  <img src="https://i.pinimg.com/736x/7d/9e/4b/7d9e4b710a28f342febffb0be5d88519.jpg" class="d-block w-100" alt="Profile Image 2">
                </div>
                <div class="carousel-item">
                  <img src="https://i.pinimg.com/736x/b6/86/b8/b686b8e70200aacc92ad78b70b866416.jpg" class="d-block w-100" alt="Profile Image 3">
                </div>
                <div class="carousel-item">
                  <img src="https://i.pinimg.com/236x/e0/a5/06/e0a506828ba00d377d8fc5872612d84a.jpg" class="d-block w-100" alt="Profile Image 4">
                </div>
              </div>
              <!-- Custom Bottom Controls -->
              <div class="carousel-controls">
                <button type="button" data-bs-target="#profileCarousel" data-bs-slide="prev">
                  ‹ Prev
                </button>
                <button type="button" data-bs-target="#profileCarousel" data-bs-slide="next">
                  Next ›
                </button>
              </div>
            </div>
            <div class="recently-joined">✨ Recently Joined</div>
          </div>
          
          <!-- Right Side (Profile Info) -->
          <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
              <div>
                <h5 class="mb-1 fw-bold">Fariha Ferdous</h5>
                <p class="mb-1 text-muted"><i class="bi bi-geo-alt"></i> Dhaka [Dacca], Dhaka, Bangladesh</p>
              </div>
              <div class="profile-actions text-primary">
                <i class="bi bi-envelope"></i>
                <i class="bi bi-telephone"></i>
                <i class="bi bi-chat"></i>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-sm-6">
                <p><strong>Age, Height:</strong> 22 yrs, 5 ft 2 in / 157 cm</p>
                <p><strong>Religion:</strong> Islam</p>
                <p><strong>Sect:</strong> Sunni (Caste No Bar)</p>
              </div>
              <div class="col-sm-6">
                <p><strong>Education:</strong> Bachelors</p>
                <p><strong>Occupation:</strong> Student</p>
              </div>
            </div>

            <div class="d-flex align-items-center mt-3 flex-wrap">
              <span class="me-2 fw-semibold">Interested in her?</span>
              <button class="btn btn-warning btn-sm btn-space">Yes</button>
              <button class="btn btn-outline-secondary btn-sm btn-space">No</button>
            </div>

            <hr>
            <p class="about-text mb-0">
              My name is Fariha Ferdous. I’m doing my bachelor’s right now... 
              <a href="#" class="text-decoration-none fw-semibold">View Full Profile</a>
            </p>
          </div>
        </div>
      </div>

      <div class="profile-card p-3">
        <div class="row g-3">
          
          <!-- Left Side (Image Slider with Border & Bottom Controls) -->
          <div class="col-md-4">
            <div id="profileCarousel" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="https://i.pinimg.com/564x/02/e7/f5/02e7f5464591ce514fffbdf03b287eed.jpg" class="d-block w-100" alt="Profile Image 1">
                </div>
                <div class="carousel-item">
                  <img src="https://i.pinimg.com/736x/7d/9e/4b/7d9e4b710a28f342febffb0be5d88519.jpg" class="d-block w-100" alt="Profile Image 2">
                </div>
                <div class="carousel-item">
                  <img src="https://i.pinimg.com/736x/b6/86/b8/b686b8e70200aacc92ad78b70b866416.jpg" class="d-block w-100" alt="Profile Image 3">
                </div>
                <div class="carousel-item">
                  <img src="https://i.pinimg.com/236x/e0/a5/06/e0a506828ba00d377d8fc5872612d84a.jpg" class="d-block w-100" alt="Profile Image 4">
                </div>
              </div>
              <!-- Custom Bottom Controls -->
              <div class="carousel-controls">
                <button type="button" data-bs-target="#profileCarousel" data-bs-slide="prev">
                  ‹ Prev
                </button>
                <button type="button" data-bs-target="#profileCarousel" data-bs-slide="next">
                  Next ›
                </button>
              </div>
            </div>
            <div class="recently-joined">✨ Recently Joined</div>
          </div>
          
          <!-- Right Side (Profile Info) -->
          <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
              <div>
                <h5 class="mb-1 fw-bold">Fariha Ferdous</h5>
                <p class="mb-1 text-muted"><i class="bi bi-geo-alt"></i> Dhaka [Dacca], Dhaka, Bangladesh</p>
              </div>
              <div class="profile-actions text-primary">
                <i class="bi bi-envelope"></i>
                <i class="bi bi-telephone"></i>
                <i class="bi bi-chat"></i>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-sm-6">
                <p><strong>Age, Height:</strong> 22 yrs, 5 ft 2 in / 157 cm</p>
                <p><strong>Religion:</strong> Islam</p>
                <p><strong>Sect:</strong> Sunni (Caste No Bar)</p>
              </div>
              <div class="col-sm-6">
                <p><strong>Education:</strong> Bachelors</p>
                <p><strong>Occupation:</strong> Student</p>
              </div>
            </div>

            <div class="d-flex align-items-center mt-3 flex-wrap">
              <span class="me-2 fw-semibold">Interested in her?</span>
              <button class="btn btn-warning btn-sm btn-space">Yes</button>
              <button class="btn btn-outline-secondary btn-sm btn-space">No</button>
            </div>

            <hr>
            <p class="about-text mb-0">
              My name is Fariha Ferdous. I’m doing my bachelor’s right now... 
              <a href="#" class="text-decoration-none fw-semibold">View Full Profile</a>
            </p>
          </div>
        </div>
      </div>

      <div class="profile-card p-3">
        <div class="row g-3">
          
          <!-- Left Side (Image Slider with Border & Bottom Controls) -->
          <div class="col-md-4">
            <div id="profileCarousel" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="https://i.pinimg.com/564x/02/e7/f5/02e7f5464591ce514fffbdf03b287eed.jpg" class="d-block w-100" alt="Profile Image 1">
                </div>
                <div class="carousel-item">
                  <img src="https://i.pinimg.com/736x/7d/9e/4b/7d9e4b710a28f342febffb0be5d88519.jpg" class="d-block w-100" alt="Profile Image 2">
                </div>
                <div class="carousel-item">
                  <img src="https://i.pinimg.com/736x/b6/86/b8/b686b8e70200aacc92ad78b70b866416.jpg" class="d-block w-100" alt="Profile Image 3">
                </div>
                <div class="carousel-item">
                  <img src="https://i.pinimg.com/236x/e0/a5/06/e0a506828ba00d377d8fc5872612d84a.jpg" class="d-block w-100" alt="Profile Image 4">
                </div>
              </div>
              <!-- Custom Bottom Controls -->
              <div class="carousel-controls">
                <button type="button" data-bs-target="#profileCarousel" data-bs-slide="prev">
                  ‹ Prev
                </button>
                <button type="button" data-bs-target="#profileCarousel" data-bs-slide="next">
                  Next ›
                </button>
              </div>
            </div>
            <div class="recently-joined">✨ Recently Joined</div>
          </div>
          
          <!-- Right Side (Profile Info) -->
          <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
              <div>
                <h5 class="mb-1 fw-bold">Fariha Ferdous</h5>
                <p class="mb-1 text-muted"><i class="bi bi-geo-alt"></i> Dhaka [Dacca], Dhaka, Bangladesh</p>
              </div>
              <div class="profile-actions text-primary">
                <i class="bi bi-envelope"></i>
                <i class="bi bi-telephone"></i>
                <i class="bi bi-chat"></i>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-sm-6">
                <p><strong>Age, Height:</strong> 22 yrs, 5 ft 2 in / 157 cm</p>
                <p><strong>Religion:</strong> Islam</p>
                <p><strong>Sect:</strong> Sunni (Caste No Bar)</p>
              </div>
              <div class="col-sm-6">
                <p><strong>Education:</strong> Bachelors</p>
                <p><strong>Occupation:</strong> Student</p>
              </div>
            </div>

            <div class="d-flex align-items-center mt-3 flex-wrap">
              <span class="me-2 fw-semibold">Interested in her?</span>
              <button class="btn btn-warning btn-sm btn-space">Yes</button>
              <button class="btn btn-outline-secondary btn-sm btn-space">No</button>
            </div>

            <hr>
            <p class="about-text mb-0">
              My name is Fariha Ferdous. I’m doing my bachelor’s right now... 
              <a href="#" class="text-decoration-none fw-semibold">View Full Profile</a>
            </p>
          </div>
        </div>
      </div>  
    </div>
  </div>
</div>
@endsection