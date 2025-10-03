<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Laravel App')</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  
    
    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
     @stack('styles')
</head>
<body>
    
    @include('components.navbar')

    {{-- Main Content --}}
    <div class="container-fluid">
        @yield('content')
    </div>

      <!-- Include Footer -->
    @include('components.footer')

    {{-- Bootstrap JS --}}
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    {{-- Custom JS --}}
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
