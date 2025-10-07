@extends('layouts.app')

@section('title', 'Login')

@push('styles')
<style>
  body {
    background: #f0f2f5;
  }
  .login-card {
    max-width: 400px;
    margin: auto;
    margin-top: 80px;
    padding: 30px;
    border-radius: 15px;
    background: #fff;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    animation: fadeIn 0.5s ease-in-out;
  }
  .login-card h3 {
    text-align: center;
    margin-bottom: 20px;
    font-weight: bold;
  }
  .login-card .btn-google {
    background-color: #DB4437;
    color: #fff;
  }
  .login-card .text-center a {
    text-decoration: none;
    color: #0d6efd;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .btn-primary {
    transition: all 0.3s ease;
  }
  .btn-primary:hover {
    background-color: #0b5ed7;
    transform: scale(1.05);
  }
</style>
@endpush

@section('content')
<div class="login-card">
    <h3>Log In to Your Account</h3>

    @if (session('success'))
      <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger text-center">{{ $errors->first() }}</div>
    @endif

    <!-- Login with Google -->
    <a href="{{ route('login.google') }}" class="btn btn-danger w-100 mb-3">
        Login with Google
    </a>

    <hr>

    <!-- Login with Email -->
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   name="password" required>
            @error('password')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">Remember Me</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-2">Login</button>

        <div class="text-center">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot Your Password?</a>
            @endif
        </div>

        <div class="text-center mt-2">
            Don’t have an account? <a href="{{ route('register.show') }}">Create an Account</a>
        </div>
    </form>
</div>
@endsection
