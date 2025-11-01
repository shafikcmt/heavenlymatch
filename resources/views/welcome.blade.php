@extends('layouts.app')

@section('title', 'Home')

@push('styles')
<style>
.hero-section{
background: url(https://heavenlymatch.net/public/images/hero-bg.jpg) no-repeat center center fixed; 
                background-size: cover; height: 100vh;
}
</style>
@endpush

@section('content')
    <section class="hero-section text-white d-flex align-items-center w-100">

    <div class="container text-center bg-dark bg-opacity-50 p-5">
        <!-- Hero Text -->

          <h2 class="text-center fw-bold mb-4">Begin Your Search for an Ideal Match</h2>

        <form action="" method="GET" class="row g-3 justify-content-center">
            @csrf

            <!-- I'm looking for -->
            <div class="col-md-2">
                <label class="form-label">I'm looking for</label>
                <select name="looking_for" class="form-select" required>
                    <option value="">Select</option>
                    <option value="bride">Bride</option>
                    <option value="groom">Groom</option>
                </select>
            </div>

            <!-- Age From -->
            <div class="col-md-2">
                <label class="form-label">Age From</label>
                <select name="age_from" class="form-select" required>
                    <option value="">From</option>
                    @for($i = 18; $i <= 65; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <!-- Age To -->
            <div class="col-md-2">
                <label class="form-label">To</label>
                <select name="age_to" class="form-select" required>
                    <option value="">To</option>
                    @for($i = 18; $i <= 65; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <!-- Mother Tongue -->
            <div class="col-md-2">
                <label class="form-label">Mother Tongue</label>
                <select name="mother_tongue" class="form-select" required>
                    <option value="">Select</option>
                    <option value="bengali">Bengali</option>
                    <option value="hindi">Hindi</option>
                    <option value="urdu">Urdu</option>
                    <option value="english">English</option>
                    <!-- Add more as needed -->
                </select>
            </div>

            <!-- Caste / Sect -->
            <div class="col-md-2">
                <label class="form-label">Caste / Sect</label>
                <select name="caste" class="form-select" required>
                    <option value="">Select</option>
                    <option value="general">General</option>
                    <option value="sc">SC</option>
                    <option value="st">ST</option>
                    <option value="obc">OBC</option>
                    <!-- Add more as needed -->
                </select>
            </div>

            <!-- Search Button -->
            <div class="col-md-2 d-grid align-self-end">
                <button type="submit" class="btn btn-danger btn-lg">Search</button>
            </div>
        </form>
      
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container text-center">
        <h2 class="mb-5 fw-bold">Find Your Special Someone</h2>

        <div class="row g-4">
            <!-- Step 1: Sign Up -->
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-person-plus-fill fs-1 text-danger"></i> <!-- Icon -->
                        </div>
                        <h5 class="card-title fw-bold">Sign Up</h5>
                        <p class="card-text">Register for free & put up your Matrimony Profile.</p>
                    </div>
                </div>
            </div>

            <!-- Step 2: Connect -->
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-chat-dots-fill fs-1 text-danger"></i>
                        </div>
                        <h5 class="card-title fw-bold">Connect</h5>
                        <p class="card-text">Select & Connect with Matches you like.</p>
                    </div>
                </div>
            </div>

            <!-- Step 3: Interact -->
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-heart-fill fs-1 text-danger"></i>
                        </div>
                        <h5 class="card-title fw-bold">Interact</h5>
                        <p class="card-text">Become a Premium Member & Start a Conversation.</p>
                    </div>
                </div>
            </div>

            <!-- Step 4: Celebrate -->
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-gift-fill fs-1 text-danger"></i>
                        </div>
                        <h5 class="card-title fw-bold">Celebrate</h5>
                        <p class="card-text">Find your life partner & celebrate your journey together.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5 bg-primary text-white">
    <div class="container">
       <h1 class="display-6 fw-bold text-center">Find Your Perfect Match ❤️</h1>
        <p class="lead mb-4 text-center">Join our Matrimony website today and connect with your life partner easily.</p>

        <!-- Registration Form -->
        <form action="{{ route('register.show') }}" method="GET" class="row g-2 justify-content-center bg-dark bg-opacity-50 p-4 rounded">
            @csrf

            <!-- Looking For -->
            <div class="col-md-3">
                <select name="looking_for" class="form-select" required>
                    <option value="">Matrimony Profile For</option>
                    <option value="myself">Myself</option>
                    <option value="daughter">Daughter</option>
                    <option value="son">Son</option>
                    <option value="sister">Sister</option>
                    <option value="brother">Brother</option>
                    <option value="relative">Relative</option>
                    <option value="friend">Friend</option>
                </select>
            </div>


            <!-- Name -->
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="Your Name" required>
            </div>

            <!-- Mobile Number -->
            <div class="col-md-4">
                <input type="tel" name="mobile" class="form-control" placeholder="Mobile Number" required>
            </div>

            <!-- Register Button -->
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-danger">Register</button>
            </div>
        </form> 
    </div>
</section>

<section class="py-5" style="background-color: #f8f9fa;">
  <!-- Slider Title -->
  <h2 class="text-center mb-4">HeavenlyMatch
 Brides & Grooms</h2>

  <!-- Profile Slider -->
  <div id="profileCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">

      <!-- Slide 1 -->
      <div class="carousel-item active">
        <div class="row text-center justify-content-center">
          <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card shadow p-3">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s" 
                   class="rounded-circle mx-auto d-block mb-2" alt="Profile 1" style="width:120px; height:120px; object-fit:cover;">
              <h6>101</h6>
              <p>25 yrs</p>
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card shadow p-3">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s" 
                   class="rounded-circle mx-auto d-block mb-2" alt="Profile 2" style="width:120px; height:120px; object-fit:cover;">
              <h6>102</h6>
              <p>30 yrs</p>
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card shadow p-3">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s" 
                   class="rounded-circle mx-auto d-block mb-2" alt="Profile 3" style="width:120px; height:120px; object-fit:cover;">
              <h6>103</h6>
              <p>28 yrs</p>
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card shadow p-3">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s" 
                   class="rounded-circle mx-auto d-block mb-2" alt="Profile 4" style="width:120px; height:120px; object-fit:cover;">
              <h6>104</h6>
              <p>27 yrs</p>
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card shadow p-3">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s" 
                   class="rounded-circle mx-auto d-block mb-2" alt="Profile 5" style="width:120px; height:120px; object-fit:cover;">
              <h6>105</h6>
              <p>29 yrs</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="carousel-item">
        <div class="row text-center justify-content-center">
          <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card shadow p-3">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s" 
                   class="rounded-circle mx-auto d-block mb-2" alt="Profile 6" style="width:120px; height:120px; object-fit:cover;">
              <h6>106</h6>
              <p>26 yrs</p>
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card shadow p-3">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s" 
                   class="rounded-circle mx-auto d-block mb-2" alt="Profile 7" style="width:120px; height:120px; object-fit:cover;">
              <h6>107</h6>
              <p>31 yrs</p>
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card shadow p-3">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s" 
                   class="rounded-circle mx-auto d-block mb-2" alt="Profile 8" style="width:120px; height:120px; object-fit:cover;">
              <h6>108</h6>
              <p>24 yrs</p>
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card shadow p-3">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s" 
                   class="rounded-circle mx-auto d-block mb-2" alt="Profile 9" style="width:120px; height:120px; object-fit:cover;">
              <h6>109</h6>
              <p>28 yrs</p>
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card shadow p-3">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s" 
                   class="rounded-circle mx-auto d-block mb-2" alt="Profile 10" style="width:120px; height:120px; object-fit:cover;">
              <h6>110</h6>
              <p>30 yrs</p>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Carousel controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#profileCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#profileCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>

  <!-- Subtitle after slider -->
  <h4 class="text-center mt-4">Be a Prospective Match</h4>

  <!-- Register Button -->
  <div class="text-center mt-3">
    <a href="#" class="btn btn-primary btn-lg">Register</a>
  </div>
</section>



<section class="py-5" style="background-color: #F6EFEE;">
  <div class="container">
    <div class="row align-items-center">
      <!-- Text Section -->
      <div class="col-lg-6 text-center text-lg-start mb-4 mb-lg-0">
        <h2 class="mb-3">HeavenlyMatch
 Matrimony App</h2>
        <p class="lead mb-2"><strong>Over 1 lakh+ installs</strong></p>
        <ul class="list-unstyled fs-5">
          <li>✅ Always stay up to date with faster & easier matchmaking</li>
          <li>✅ Get 24/7 support and world class user experience</li>
        </ul>
        <!-- Google Play Store Button -->
        <a href="#" class="d-inline-block mt-3">
          <img src="{{ asset('images/google-play-badge.png') }}" 
               alt="Google Play Store" style="height:60px;">
        </a>
      </div>

      <!-- App Promo Image -->
      <div class="col-lg-6 text-center">
        <img src="{{ asset('images/app-promo.png') }}" 
             alt="App Promo" class="img-fluid shadow rounded" style="max-height:400px;">
      </div>
    </div>
  </div>
</section>



<section class="py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-4">HeavenlyMatch  Matrimony - The No.1 choice for finding your life partner</h2>
    <div class="row">
      
      <!-- Left: Image Slider -->
      <div class="col-md-6">
        <div id="successImageSlider" class="carousel slide mb-3" data-bs-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s"
                   class="d-block w-100 rounded shadow" style="height: 300px; object-fit: cover;" alt="Success Story 1">
            </div>
            <div class="carousel-item">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s"
                   class="d-block w-100 rounded shadow" style="height: 300px; object-fit: cover;" alt="Success Story 2">
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#successImageSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#successImageSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      </div>

      <!-- Right: History Cards with Images -->
      <div class="col-md-6">
        <div id="historySlider" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">

            <!-- Slide 1 -->
            <div class="carousel-item active">
              <div class="mb-3 p-3 border rounded d-flex align-items-start">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s"
                     class="rounded me-3" style="height:100px; width:100px; object-fit:cover;" alt="Couple">
                <div>
                  <h5>Riad & Anika</h5>
                  <p class="mb-2">Riad Afreen and Anika Islam found love with the help of their families in Bangladeshi Matrimony...</p>
                  <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
                </div>
              </div>

              <div class="p-3 border rounded d-flex align-items-start">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s"
                     class="rounded me-3" style="height:100px; width:100px; object-fit:cover;" alt="Couple">
                <div>
                  <h5>Hasan & Farzana</h5>
                  <p class="mb-2">Hasan met Farzana on Bangladeshi Matrimony, and together they began a beautiful journey of love...</p>
                  <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
                </div>
              </div>
            </div>

            <!-- Slide 2 -->
            <div class="carousel-item">
              <div class="mb-3 p-3 border rounded d-flex align-items-start">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s"
                     class="rounded me-3" style="height:100px; width:100px; object-fit:cover;" alt="Couple">
                <div>
                  <h5>Tanvir & Shaila</h5>
                  <p class="mb-2">Tanvir and Shaila’s story began here, proving love knows no distance...</p>
                  <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
                </div>
              </div>

              <div class="p-3 border rounded d-flex align-items-start">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwgu1A5zgPSvfE83nurkuzNEoXs9DMNr8Ww&s"
                     class="rounded me-3" style="height:100px; width:100px; object-fit:cover;" alt="Couple">
                <div>
                  <h5>Kamal & Naila</h5>
                  <p class="mb-2">Kamal and Naila’s families connected through Bangladeshi Matrimony...</p>
                  <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
                </div>
              </div>
            </div>

          </div>

          <!-- Controls -->
          <button class="carousel-control-prev" type="button" data-bs-target="#historySlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#historySlider" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      </div>

    </div>

    <!-- Simple Register Button -->
    <div class="text-center mt-4">
      <a href="#" class="btn btn-success">Register Now</a>
    </div>
  </div>
</section>



@endsection






