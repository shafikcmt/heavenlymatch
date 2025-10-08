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
</style>
<!-- resources/views/includes/navbar.blade.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top text-uppercase">
  <div class="container">
    <a class="navbar-brand" href="{{route('myhome')}}">Logo</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>  
    </button>

    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link active" href="{{route('myhome')}}">My Home</a></li>
        <li class="nav-item"><a class="nav-link" href="{{route('matches')}}">Matches <span class="badge bg-secondary">4957</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{route('inbox')}}">Inbox</a></li>
        <li class="nav-item"><a class="nav-link" href="{{route('search')}}">Search</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Daily Matches</a></li>
        <li class="nav-item"><a class="nav-link" href="{{route('upgrade')}}">Upgrade</a></li>
      </ul>
      <!-- <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-bell"></i></a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-person-circle"></i></a></li>
      </ul> -->
      <ul class="navbar-nav ms-auto d-flex align-items-center">
  <!-- Notification -->
  <li class="nav-item me-3">
    <a class="nav-link position-relative text-white" href="#">
      <i class="bi bi-bell"></i>
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        3
      </span>
    </a>
  </li>

  <!-- User Profile Dropdown -->
  <li class="nav-item dropdown">
    <a class="nav-link text-white dropdown-toggle" href="#" role="button">
      <i class="bi bi-person-circle"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow-lg rounded-3 animate-dropdown">
      
      <!-- Top Section with Image, Button, Status -->
      <li class="px-3 py-3 border-bottom text-center">
        <img height="50px" src="https://hips.hearstapps.com/hmg-prod/images/index3-3-1651581277.jpg?crop=0.5xw:1xh;center,top&resize=640:*" class="rounded-circle mb-3" alt="Avatar">
        
        @php
            $hasBiodata = auth()->user()->biodata()->exists();
        @endphp

        <div>
            @if ($hasBiodata)
                <a href="{{ route('biodata.create') }}" class="btn btn-success btn-sm mb-2">View Biodata</a>
            @else
                <a href="{{ route('biodata.create') }}" class="btn btn-primary btn-sm mb-2">Create Biodata</a>
            @endif
        </div>
        
        <div class="small text-white px-2 py-1 rounded mb-1" style="background-color: #ff6b6b;">
          Biodata Status
        </div>
        <div class="small text-white px-2 py-1 rounded" style="background-color: #ff6b6b;">
          Not Approved
        </div>
      </li>

      <!-- Dashboard Items -->
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
</ul>
  </li>
</ul>


    </div>
  </div>
</nav>

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
