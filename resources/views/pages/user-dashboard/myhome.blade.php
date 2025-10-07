@extends('layouts.user-dashboard-app')

@section('title', 'MY HOME')

@section('content')

<style>
  /* Profile Card */
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


  
</style>

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
