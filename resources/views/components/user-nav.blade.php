<style>
  /* Dropdown show on hover */
  .dropdown:hover .dropdown-menu {
    display: block;
    opacity: 1;
    transform: translateY(0);
  }

  /* Dropdown animation */
  .animate-dropdown {
    display: none;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease;
  }

  .dropdown-menu {
    background-color: #fff;
    min-width: 220px;
  }

  .dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #333;
    font-weight: 500;
    transition: all 0.2s ease;
  }

  .dropdown-item:hover {
    background-color: #f1f1f1;
    color: #000;
  }

  .nav-link i {
    font-size: 1.3rem;
  }

  /* Top section */
  .dropdown-menu li.px-3 {
    cursor: default;
  }

  .dropdown-menu li.px-3 a.btn {
    font-size: 0.85rem;
  }
  /* Main navbar */
.navbar-main {
    z-index: 1050; /* higher than second navbar */
}

/* Second Navbar Stylish Look */
.navbar-secondary {
    background: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); /* soft shadow */
    position: sticky;
    top: 56px; /* adjust based on main navbar height */
    z-index: 1040;
    padding: 0.6rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.navbar-secondary .nav-link {
    color: #333;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    transition: all 0.3s ease;
}

.navbar-secondary .nav-link:hover {
    color: #007bff;
    transform: translateY(-2px);
}
  .dropdown-menu {
    overflow: visible !important;
}
.navbar .dropdown-menu {
    position: absolute; /* ensures it floats over all elements */
}
.navbar {
    font-size: 12px !important;
}


  /* container li to mimic navbar spacing */
    .nav-item { list-style: none; }

    /* Upgrade button: flexible, prevents .nav-link defaults from breaking it */
    .upgrade-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 3px 14px;
      border-radius: 8px;
      font-weight: 700;
      color: #000;
      text-decoration: none;
      background: #fff;
      min-width: 85px;
      box-shadow: 0 6px 18px rgba(0,0,0,.15);
      overflow: hidden;
      position: relative;
      line-height: 1;
      font-size: 10px;
      text-align: center;
    }

    /* optional icon style */
    .upgrade-btn .icon {
      display:inline-block;
      font-size: 18px;
      opacity: .95;
    }

    /* the text-rotator viewport */
    .upgrade-text {
      display: inline-block;
      height: 20px;        /* controls visible height */
      line-height: 20px;   /* vertically center */
      overflow: hidden;
      position: relative;
      vertical-align: middle;
      flex: 1 1 auto;      /* allow text to take remaining width */
      min-width: 0;        /* allow text truncation if necessary */
    }

    /* each line occupies same space and uses animation */
    .upgrade-text span {
      display: block;
      width: 100%;
      position: absolute;
      left: 0;
      top: 0;
      transform: translateY(100%);
      opacity: 0;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      padding-right: 6px;
      animation: slideText 4s infinite;
    }

    /* timing offsets for each message */
    .upgrade-text .text1 { animation-delay: 0s; }
    .upgrade-text .text2 { animation-delay: 2s; }

    @keyframes slideText {
      0%   { transform: translateY(100%); opacity: 0; }
      10%  { transform: translateY(0%);   opacity: 1; }
      45%  { transform: translateY(0%);   opacity: 1; }
      55%  { transform: translateY(-100%);opacity: 0; }
      100% { transform: translateY(-100%);opacity: 0; }
    }

    /* small responsive tweak */
    @media (max-width:420px){
      .upgrade-btn { min-width: 120px; padding:6px 10px; gap:6px; }
      .upgrade-text { height: 18px; line-height:18px; }
    }
</style>
<!-- Main Navbar (unchanged) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top navbar-main ">
  <div class="container">
    <a class="navbar-brand" href="{{route('myhome')}}">❤️ HeavenlyMatch</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>  
    </button>

    <div class="collapse navbar-collapse justify-content-center text-uppercase" id="navbarNav">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link active" href="{{route('myhome')}}">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="{{route('matches')}}">Islamic Profile <span class="badge bg-secondary">4957</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{route('inbox')}}">General Profile</a></li>
      <li class="nav-item">
          <a class="nav-link" href="{{ route('demo') }}">
            Prefarence Match
          </a>
        </li>

        <li class="nav-item dropdown me-3">
  <a class="nav-link dropdown-toggle text-white" href="#" id="profileDrop" role="button" 
     data-bs-toggle="dropdown" aria-expanded="false">
     Profile List
  </a>

  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDrop">

    <li>
      <a class="dropdown-item d-flex align-items-center" href="#">
        <i class=" bi bi-star me-2 text-danger"></i> Shortlist
      </a>
    </li>

    <li>
      <a class="dropdown-item d-flex align-items-center" href="#">
        <i class="bi bi-heart me-2 text-warning"></i> Favorite list
      </a>
    </li>

    <li>
      <a class="dropdown-item d-flex align-items-center" href="#">
        <i class="bi bi-person-check me-2 text-success"></i> Shortlisted me
      </a>
    </li>

    <li>
      <a class="dropdown-item d-flex align-items-center" href="#">
        <i class="bi bi-telephone me-2 text-primary"></i> Contact List
      </a>
    </li>

  </ul>
</li>

        <!-- <li class="nav-item"><a class="nav-link" href="{{route('upgrade')}}">Upgrade</a></li> -->
      </ul>

      @php
          $hasBiodata = auth()->check() && auth()->user()->biodata()->exists();
      @endphp

      <ul class="navbar-nav ms-auto d-flex align-items-center">

      
        <!-- User Profile Dropdown -->
      
<li class="nav-item dropdown">
  <a class="nav-link text-white dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
    <i class="bi bi-person-circle"></i>
  </a>

  <ul class="dropdown-menu dropdown-menu-end shadow-lg rounded-3 animate-dropdown">
    <li class="px-3 py-3 border-bottom text-center">
      <img height="50px"
        src="https://hips.hearstapps.com/hmg-prod/images/index3-3-1651581277.jpg?crop=0.5xw:1xh;center,top&resize=640:*"
        class="rounded-circle mb-3" alt="Avatar">

      <div>
        @if ($hasBiodata)
          <a href="{{ route('profiledetail') }}" class="btn btn-success btn-sm mb-2">View Biodata</a>
        @else
          <a href="{{ route('biodata.create') }}" class="btn btn-primary btn-sm mb-2">Create Biodata</a>
        @endif
      </div>

      <div class="small text-white px-2 py-1 rounded mb-1" style="background-color: #ff6b6b;">
        Biodata Status
      </div>
      <div class="small text-white px-2 py-1 rounded" style="background-color: #ff6b6b;">
       Share Profile
      </div>
    </li>

    <li><a class="dropdown-item" href="#"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
    <li><a class="dropdown-item" href="#"><i class="bi bi-pencil-square me-2"></i>Edit Biodata</a></li>
    <li><a class="dropdown-item" href="#"><i class="bi bi-heart me-2"></i>Shortlist</a></li>
    <li><a class="dropdown-item" href="#"><i class="bi bi-x-circle me-2"></i>Ignore List</a></li>
    <li><a class="dropdown-item" href="#"><i class="bi bi-cart3 me-2"></i>My Purchased</a></li>
    <li><a class="dropdown-item" href="#"><i class="bi bi-question-circle me-2"></i>Support & Report</a></li>
    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>

    <li><hr class="dropdown-divider"></li>

    <li>
      <a class="dropdown-item text-danger" href="#"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="bi bi-box-arrow-right me-2"></i>Logout
      </a>

      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
      </form>
    </li>
  </ul>
</li>

        <!-- Notification -->
      <li class="nav-item me-3">
        <a class="upgrade-btn" href="#" role="button" aria-label="Upgrade">
      
          <span class="upgrade-text" aria-hidden="false">
            <span class="text1">Upgrade</span>
            <span class="text2"> 60% Off</span>
          </span>
        </a>
      </li>
         <li class="nav-item dropdown me-3">
  <a class="nav-link dropdown-toggle text-white" href="#" id="languageDropdown" role="button" 
     data-bs-toggle="dropdown" aria-expanded="false">
    EN/BN
  </a>
  
  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
    <li>
      <a class="dropdown-item" href="?lang=en">English</a>
    </li>
    <li>
      <a class="dropdown-item" href="?lang=bn">বাংলা</a>
    </li>
  </ul>
</li>


         <li class="nav-item me-3">
          <a class="nav-link position-relative text-white" href="#">
            <i class="bi bi-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              3
            </span>
          </a>
        </li>

<!-- Add this CSS -->
<style>
/* Keep dropdown open on hover */
.nav-item.dropdown:hover .dropdown-menu {
  display: block;
  margin-top: 0; /* No gap */
}

/* Prevent dropdown from hiding when mouse moves */
.nav-item.dropdown .dropdown-menu {
  right: 0;
  left: auto;
  top: 100%;
  transform: translateX(0);
  min-width: 220px;
  z-index: 1050;
  animation: fadeIn 0.3s ease-in-out;
}

/* Nice animation */
@keyframes fadeIn {
  from {opacity: 0; transform: translateY(10px);}
  to {opacity: 1; transform: translateY(0);}
}

/* Avoid overflow on right edge */
.dropdown-menu-end {
  right: 0;
  left: auto;
  overflow: visible;
}
</style>

      </ul>
    </div>
  </div>
</nav>



<!-- Responsive Secondary Navbar -->
<nav class="navbar navbar-expand-lg custom-subnav shadow-sm">
  <div class="container">
    <button
      class="navbar-toggler ms-auto"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#secondaryNavbar"
      aria-controls="secondaryNavbar"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <i class="bi bi-list fs-3 text-white"></i>
    </button>

    <div class="collapse navbar-collapse justify-content-center" id="secondaryNavbar">
      <ul class="navbar-nav align-items-center gap-lg-3">
        
        <li class="nav-item">
          <a class="nav-link" href="{{ route('demo') }}">
            Preferred Profession
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('matches') }}">
             Preferred Education
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('search') }}">
             Preferred Location
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('upgrade') }}">
            Nearby Matches
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('upgrade') }}">
            <i class="bi bi-search me-1"></i>  Search
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
  /* Custom Navbar Styling */
  .custom-subnav {
    background: linear-gradient(90deg, #007bff, #6610f2);
    padding: 0.4rem 1rem;
  }

  .custom-subnav .nav-link {
    color: #fff !important;
    font-weight: 500;
    font-size: 15px;
    padding: 8px 14px;
    border-radius: 8px;
    transition: all 0.3s ease;
  }

  .custom-subnav .nav-link i {
    font-size: 16px;
  }

  .custom-subnav .nav-link:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #fff !important;
  }

  .custom-subnav .navbar-toggler {
    border: none;
    outline: none;
  }

  @media (max-width: 992px) {
    .custom-subnav .navbar-collapse {
      background: rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      margin-top: 10px;
      padding: 10px;
    }

    .custom-subnav .nav-link {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 10px;
    }

    .custom-subnav .nav-item {
      width: 100%;
      text-align: center;
    }
  }
</style>



<script>
  // Hover effect for user dropdown
  const userItem = document.querySelector('.nav-item');
  const dropdown = userItem.querySelector('.dropdown-menu');

  userItem.addEventListener('mouseenter', () => {
    dropdown.style.display = 'block';
  });

  userItem.addEventListener('mouseleave', () => {
    dropdown.style.display = 'none';
  });

  // Optional: hover effect for menu items
  dropdown.querySelectorAll('a').forEach(item => {
    item.addEventListener('mouseenter', () => {
      item.style.background = '#f0f0f0';
    });
    item.addEventListener('mouseleave', () => {
      item.style.background = 'transparent';
    });
  });
</script>
