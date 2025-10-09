<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BiodataController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PhoneVerificationController;

// --------------------------------------------------------
// Guest Routes (Accessible only if not logged in)
// --------------------------------------------------------
Route::middleware('guest')->group(function () {

    // Registration
    Route::get('/register', [RegistrationController::class, 'showForm'])->name('register.show');
    Route::post('/register', [RegistrationController::class, 'store'])->name('register.store');

    // Email Verification
    Route::get('/verify-email', [EmailVerificationController::class, 'showVerifyForm'])->name('email.verify.notice');
    Route::post('/verify-email-code', [EmailVerificationController::class, 'verifyCode'])->name('email.verify.code');
    Route::post('/send-verification-code', [EmailVerificationController::class, 'sendCode'])->name('email.send.code');
    Route::get('/verify-email/{token}', [EmailVerificationController::class, 'verifyLink'])->name('email.verify.link');

    // Login
    Route::get('/login', [CustomLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CustomLoginController::class, 'login']);
});

// --------------------------------------------------------
// Authenticated Routes (Logged-in users)
// --------------------------------------------------------
Route::middleware(['auth', 'verified.user'])->group(function () {

    // ------------------------
    // Biodata Routes (Always accessible to logged-in users)
    // ------------------------
    Route::prefix('biodata')->group(function () {
        Route::get('/create/{step?}', [BiodataController::class, 'create'])->name('biodata.create');
        Route::post('/store/{step}', [BiodataController::class, 'store'])->name('biodata.store');
    });

    // ------------------------
    // Dashboard Routes (Accessible only if biodata completed)
    // ------------------------
    Route::middleware('check.biodata')->group(function () {
        Route::get('/myhome', fn() => view('pages.user-dashboard.myhome'))->name('myhome');
        Route::get('/demo', fn() => view('pages.user-dashboard.demo'))->name('demo');
        Route::get('/profiledetail', fn() => view('pages.user-dashboard.profiledetail'))->name('profiledetail');
        Route::get('/matches', fn() => view('pages.user-dashboard.matches'))->name('matches');
        Route::get('/inbox', fn() => view('pages.user-dashboard.inbox'))->name('inbox');
        Route::get('/sent', fn() => view('pages.user-dashboard.sent'))->name('sent');
        Route::get('/search', fn() => view('pages.user-dashboard.search'))->name('search');
        Route::get('/upgrade', fn() => view('pages.user-dashboard.upgrade'))->name('upgrade');
    });

    // Logout
    Route::post('/logout', [CustomLoginController::class, 'logout'])->name('logout');
});

// --------------------------------------------------------
// Frontend Routes (Accessible to guests for viewing pages)
// --------------------------------------------------------
Route::middleware('guest.frontend')->group(function () {
    Route::get('/', fn() => view('welcome'))->name('welcome');
    Route::get('/profile', fn() => view('profile'))->name('profile');
    Route::get('/settings', fn() => view('settings'))->name('settings');
    Route::get('/about', fn() => view('pages.frontend.about'))->name('about');
    Route::get('/guide', fn() => view('pages.frontend.guide'))->name('guide');
    Route::get('/faq', fn() => view('pages.frontend.faq'))->name('faq');
    Route::get('/contact', fn() => view('pages.frontend.contact'))->name('contact');
});

// --------------------------------------------------------
// Social Login Routes
// --------------------------------------------------------
Route::prefix('auth')->group(function () {
    Route::get('/login/google', [SocialLoginController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/login/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);
});

// --------------------------------------------------------
// Dashboard page (Admin or main dashboard, if needed)
// --------------------------------------------------------
Route::get('/dashboard', fn() => view('dashboard.dashboard'))->name('dashboard');
