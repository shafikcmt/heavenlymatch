<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'My Laravel App')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
   <!-- Page-specific CSS -->
    @stack('styles')
</head>
<body>

  @include('components.user-nav')

<div class="container-fluid">
<div class="row"> 
  <div class="col-auto">
      @hasSection('sidebar')
          @yield('sidebar')
      @else
          @include('components.user-sidebar')
      @endif
  </div>

      <!-- Main Content -->
      <main class="col">
        
        <!-- Subnavbar -->
            @hasSection('subnavbar')
                @yield('subnavbar')  <!-- If page defines a sidebar, show it -->
            @else
               @include('components.usernav-inner')  <!-- Default sidebar -->
            @endif

      
        <!-- Page Content -->
        <div>
          @yield('content')
        </div>

      </main>
    </div>
  </div>

  <!-- Footer Full Width -->
  <footer>
    @include('components.user-footer')
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
