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

    // Registration
    Route::get('/register', [RegistrationController::class, 'showForm'])->name('register.show');
    Route::post('/register', [RegistrationController::class, 'store'])->name('register.store');

    Route::get('/verify-email', [EmailVerificationController::class, 'showVerifyForm'])->name('email.verify.notice');
    Route::post('/send-verification-code', [EmailVerificationController::class, 'sendCode'])->name('email.send.code');
    Route::post('/verify-email-code', [EmailVerificationController::class, 'verifyCode'])->name('email.verify.code');
});


// Protected dashboard routes
Route::middleware(['auth', 'verified.user'])->group(function () {
    Route::get('/biodata/create/{step?}', [BiodataController::class, 'create'])->name('biodata.create');
    Route::post('/biodata/store/{step}', [BiodataController::class, 'store'])->name('biodata.store');

    Route::get('/myhome', function () { return view('pages.user-dashboard.myhome'); })->name('myhome');
    Route::get('/demo', function () { return view('pages.user-dashboard.demo'); })->name('demo');
    Route::get('/profiledetail', function () { return view('pages.user-dashboard.profiledetail'); })->name('profiledetail');
    Route::get('/matches', function () { return view('pages.user-dashboard.matches'); })->name('matches');
    Route::get('/inbox', function () { return view('pages.user-dashboard.inbox'); })->name('inbox');
    Route::get('/sent', function () { return view('pages.user-dashboard.sent'); })->name('sent');
    Route::get('/search', function () { return view('pages.user-dashboard.search'); })->name('search');
    Route::get('/upgrade', function () { return view('pages.user-dashboard.upgrade'); })->name('upgrade');
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



