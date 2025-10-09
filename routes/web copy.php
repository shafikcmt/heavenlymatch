<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\SocialLoginController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BiodataController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PhoneVerificationController;


Route::middleware('guest')->group(function () {

    // ✅ Registration
    Route::get('/register', [RegistrationController::class, 'showForm'])->name('register.show');
    Route::post('/register', [RegistrationController::class, 'store'])->name('register.store');

    // ✅ Email Verification Notice Page
    Route::get('/verify-email', [EmailVerificationController::class, 'showVerifyForm'])->name('email.verify.notice');

    // ✅ Verify by code
    Route::post('/verify-email-code', [EmailVerificationController::class, 'verifyCode'])->name('email.verify.code');

    // ✅ Send or Resend verification code
    Route::post('/send-verification-code', [EmailVerificationController::class, 'sendCode'])->name('email.send.code');

    // ✅ Verify by clickable link
    Route::get('/verify-email/{token}', [EmailVerificationController::class, 'verifyLink'])->name('email.verify.link');
});




// Protected dashboard routes
Route::middleware(['auth', 'verified.user', 'check.biodata'])->group(function () {
    Route::get('/biodata/create/{step?}', [BiodataController::class, 'create'])->name('biodata.create');
    Route::post('/biodata/store/{step}', [BiodataController::class, 'store'])->name('biodata.store');

    Route::get('/myhome', fn() => view('pages.user-dashboard.myhome'))->name('myhome');
    Route::get('/demo', fn() => view('pages.user-dashboard.demo'))->name('demo');
    Route::get('/profiledetail', fn() => view('pages.user-dashboard.profiledetail'))->name('profiledetail');
    Route::get('/matches', fn() => view('pages.user-dashboard.matches'))->name('matches');
    Route::get('/inbox', fn() => view('pages.user-dashboard.inbox'))->name('inbox');
    Route::get('/sent', fn() => view('pages.user-dashboard.sent'))->name('sent');
    Route::get('/search', fn() => view('pages.user-dashboard.search'))->name('search');
    Route::get('/upgrade', fn() => view('pages.user-dashboard.upgrade'))->name('upgrade');
});


// ----------------------------------
// Frontend Pages (only visible to guests)
// ----------------------------------
Route::middleware('guest.frontend')->group(function () {
    Route::get('/', fn() => view('welcome'))->name('welcome');
    Route::get('/profile', fn() => view('profile'))->name('profile');
    Route::get('/settings', fn() => view('settings'))->name('settings');
    Route::get('/about', fn() => view('pages.frontend.about'))->name('about');
    Route::get('/guide', fn() => view('pages.frontend.guide'))->name('guide');
    Route::get('/faq', fn() => view('pages.frontend.faq'))->name('faq');
    Route::get('/contact', fn() => view('pages.frontend.contact'))->name('contact');
});


Route::prefix('auth')->group(function () {
    Route::get('/login', [CustomLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CustomLoginController::class, 'login']);
    Route::post('/logout', [CustomLoginController::class, 'logout'])->name('logout');

    Route::get('/login/google', [SocialLoginController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/login/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);
});


// Dashboard page
Route::get('/dashboard', function () {
    return view('dashboard.dashboard'); 
})->name('dashboard');



