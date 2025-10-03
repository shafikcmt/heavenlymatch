@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <section class="py-5 text-center bg-light">
  <div class="container">
    <h3 class="mb-4">How do you want to create account?</h3>
    
    <div class="d-grid gap-3 col-md-6 mx-auto">
      <!-- Google Account Button -->
      <a href="#" class="btn btn-danger btn-lg">
        <i class="bi bi-google"></i> Create Account with Google
      </a>

      <!-- Email Account Button -->
      <a href="{{route('registration')}}" class="btn btn-primary btn-lg">
        <i class="bi bi-envelope"></i> Create Account with Email
      </a>

      <!-- YouTube Link Button -->
      <a href="https://www.youtube.com/watch?v=your-video-id" target="_blank" class="btn btn-dark btn-lg">
        <i class="bi bi-youtube"></i> How to Create Biodata (YouTube)
      </a>
    </div>
  </div>
</section>
@endsection