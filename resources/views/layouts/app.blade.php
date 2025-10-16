<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="https://heavenlymatch.net" />
    <title>@yield('title', 'Heavenly Match')</title>

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

    @if(session('success'))
<div id="successAlert" class="success-popup">
  <div class="success-icon">
    <svg viewBox="0 0 24 24" width="60" height="60">
      <circle cx="12" cy="12" r="10" fill="none" stroke="#4CAF50" stroke-width="2"/>
      <path d="M8 12l3 3 5-6" fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
  <div class="success-text">🎉 {{ session('success') }}</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const alert = document.getElementById('successAlert');
    alert.classList.add('show');
    setTimeout(() => alert.classList.remove('show'), 4000);
});
</script>

<style>
.success-popup {
  position: fixed;
  top: 30px;
  right: 30px;
  background: #fff;
  border-left: 6px solid #4CAF50;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  opacity: 0;
  transform: translateY(-20px);
  transition: all 0.5s ease;
  z-index: 9999;
}
.success-popup.show {
  opacity: 1;
  transform: translateY(0);
}
.success-icon {
  animation: pop 0.4s ease;
}
.success-text {
  font-size: 16px;
  font-weight: 600;
  color: #2e7d32;
}
@keyframes pop {
  0% { transform: scale(0); }
  80% { transform: scale(1.2); }
  100% { transform: scale(1); }
}
</style>
@endif

        @yield('content')
    </div>

      <!-- Include Footer -->
    @include('components.footer')

    {{-- Bootstrap JS --}}
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Custom JS --}}
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
