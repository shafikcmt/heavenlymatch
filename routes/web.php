<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\SocialLoginController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BiodataController;


// Auth::routes(); 


// Forgot password form
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');

// Send reset link
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Reset form
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// Update password
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Step-wise form navigation
Route::get('/biodata/create/{step?}', [BiodataController::class, 'create'])->name('biodata.create');
Route::post('/biodata/store/{step}', [BiodataController::class, 'store'])->name('biodata.store');


Route::get('/profiledetail', function () {
    return view('pages.user-dashboard.profiledetail');
})->name('profiledetail');


// Home / Welcome page
Route::get('/', function () {
    return view('welcome');
})->name('welcome');


Route::get('/myhome', function () {
    return view('pages.user-dashboard.myhome');
})->name('myhome');

Route::get('/demo', function () {
    return view('pages.user-dashboard.demo');
})->name('demo');


Route::get('/upgrade', function () {
    return view('pages.user-dashboard.upgrade');
})->name('upgrade');

Route::get('/matches', function () {
    return view('pages.user-dashboard.matches');
})->name('matches');


Route::get('/inbox', function () {
    return view('pages.user-dashboard.inbox');
})->name('inbox');



Route::get('/sent', function () {
    return view('pages.user-dashboard.sent');
})->name('sent');

Route::get('/search', function () {
    return view('pages.user-dashboard.search');
})->name('search');


// Profile page
Route::get('/profile', function () {
    return view('profile');
})->name('profile');

// Settings page
Route::get('/settings', function () {
    return view('settings');
})->name('settings');

// About page
Route::get('/about', function () {
    return view('pages.frontend.about');
})->name('about');

// Search page
Route::get('/guide', function () {
    return view('pages.frontend.guide');
})->name('guide');

// Faq page
Route::get('/faq', function () {
    return view('pages.frontend.faq');
})->name('faq');



// Register page
Route::get('/register', function () {
    return view('auth.register');  // now looks inside /resources/views/auth/
})->name('register');


Route::get('/registration', function () {
    return view('auth.registration');
})->name('registration');

Route::post('/registration', [RegistrationController::class, 'store'])->name('registration.store');
// Contact page
Route::get('/contact', function () {
    return view('pages.frontend.contact');
})->name('contact');

Route::get('/login', [CustomLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CustomLoginController::class, 'login']);
Route::post('/logout', [CustomLoginController::class, 'logout'])->name('logout');

Route::get('/login/google', [SocialLoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);

// Dashboard page
Route::get('/dashboard', function () {
    return view('dashboard.dashboard'); 
})->name('dashboard');


// Route::get('/dashboard', function () {
//     return view('dashboard.dashboard'); 
// })->name('dashboard')->middleware('auth:registration');


