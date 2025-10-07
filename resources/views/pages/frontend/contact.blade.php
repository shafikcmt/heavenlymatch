@extends('layouts.app')



@section('title', 'Contact')

<style>
.card {
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
}
</style>
@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Contact Us</h2>
            <p>For any query you may have, please fill out the form below and send it to us.<br>
               We will contact you soon InShaAllah.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow rounded p-4">
                    <form action="" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label fw-bold">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter subject" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label fw-bold">Description</label>
                            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Enter your message" required></textarea>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-5">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection


