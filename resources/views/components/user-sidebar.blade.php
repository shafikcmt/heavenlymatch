<style>
  .sidebar {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
    max-width: 300px;
    margin: auto;
  }

  .sidebar:hover {
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
  }

  .sidebar .profile-section img {
    height: 80px;
    width: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e9ecef;
  }

  .sidebar .username {
    font-weight: 700;
    font-size: 15px;
    margin-top: 8px;
    color: #333;
  }

  .sidebar .biodata-btn {
    margin-top: 10px;
  }

  .sidebar .status-badge {
    font-size: 12px;
    color: #fff;
    background-color: #ff6b6b;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-block;
    margin-top: 8px;
  }

  .sidebar .menu {
    text-align: left;
    margin-top: 20px;
  }

  .sidebar .menu-item {
    display: flex;
    align-items: center;
    padding: 10px 12px;
    border-radius: 10px;
    color: #333;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.25s ease;
    text-decoration: none;
  }

  .sidebar .menu-item i {
    margin-right: 10px;
    font-size: 16px;
    color: #6c757d;
  }

  .sidebar .menu-item:hover {
    background-color: #f8f9fa;
    color: #007bff;
  }

  .sidebar .menu-item:hover i {
    color: #007bff;
  }

  .sidebar .logout {
    margin-top: 15px;
    padding-top: 10px;
    border-top: 1px solid #eee;
  }

  .sidebar .logout a {
    color: #dc3545;
    font-weight: 600;
    text-decoration: none;
  }

  .sidebar .logout a:hover {
    text-decoration: underline;
  }

  /* -----------------------------
     ✅ RESPONSIVE DESIGN STYLES
     ----------------------------- */
  @media (max-width: 992px) {
    .sidebar {
      max-width: 90%;
      margin: 20px auto;
      padding: 15px;
    }
  }

  @media (max-width: 768px) {
    .sidebar {
      text-align: left;
      border-radius: 0;
      box-shadow: none;
      border-top: 2px solid #f1f1f1;
      padding: 15px 10px;
    }

    .sidebar .profile-section {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 10px;
    }

    .sidebar .profile-section img {
      height: 60px;
      width: 60px;
    }

    .sidebar .username {
      font-size: 14px;
      margin-top: 0;
    }

    .sidebar .biodata-btn {
      margin-top: 5px;
    }

    .sidebar .menu-item {
      padding: 8px;
      font-size: 13px;
    }

    .sidebar .menu-item i {
      font-size: 15px;
    }

    .sidebar .status-badge {
      margin-top: 4px;
    }
  }

  @media (max-width: 576px) {
    .sidebar {
      padding: 10px 5px;
      max-width: 100%;
    }

    .sidebar .menu-item {
      justify-content: flex-start;
      padding: 6px 8px;
    }

    .sidebar .logout {
      text-align: center;
    }
  }
</style>

<div class="sidebar">
  <!-- Profile Section -->
  <div class="profile-section">
    <img src="https://hips.hearstapps.com/hmg-prod/images/index3-3-1651581277.jpg?crop=0.5xw:1xh;center,top&resize=640:*" alt="Avatar">
    <div>
      <div class="username">{{ auth()->user()->name ?? 'User Name' }}</div>

      <div class="biodata-btn">
        @if (auth()->user()->biodata()->exists())
          <a href="{{ route('profiledetail') }}" class="btn btn-success btn-sm">View Biodata</a>
        @else
          <a href="{{ route('biodata.create') }}" class="btn btn-primary btn-sm">Create Biodata</a>
        @endif
      </div>

      <div class="status-badge">Not Approved</div>
    </div>
  </div>

  <!-- Sidebar Menu -->
  <div class="menu mt-3">
    <a href="#" class="menu-item"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="#" class="menu-item"><i class="bi bi-pencil-square"></i> Edit Biodata</a>
    <a href="#" class="menu-item"><i class="bi bi-heart"></i> Shortlist</a>
    <a href="#" class="menu-item"><i class="bi bi-x-circle"></i> Ignore List</a>
    <a href="#" class="menu-item"><i class="bi bi-cart3"></i> My Purchased</a>
    <a href="#" class="menu-item"><i class="bi bi-question-circle"></i> Support & Report</a>
    <a href="#" class="menu-item"><i class="bi bi-gear"></i> Settings</a>
  </div>

  <!-- Logout -->
  <div class="logout">
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
      @csrf
    </form>
  </div>
</div>
