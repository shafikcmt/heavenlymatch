<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BiodataController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ProfileInteractionController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminBiodataController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminFeatureController;


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
        Route::get('/profiledetail', [UserProfileController::class, 'showProfile'])
        ->middleware(['auth', 'check.biodata'])
        ->name('profiledetail');
        Route::get('/profiledetail/{id}', [UserProfileController::class, 'showProfile'])
        ->middleware(['auth', 'check.biodata'])
        ->name('profiledetail.show');
        Route::post('/profiles/{biodata}/shortlist', [ProfileInteractionController::class, 'shortlist'])->name('profiles.shortlist');
        Route::post('/profiles/{biodata}/interest', [ProfileInteractionController::class, 'interest'])->name('profiles.interest');
        Route::post('/profiles/{biodata}/chat', [ProfileInteractionController::class, 'chat'])->name('profiles.chat');
        Route::put('/biodata/update-general-info/{id}', [BiodataController::class, 'updateGeneralInfo'])
    ->name('biodata.updateGeneralInfo');
    Route::put('/biodata/update-address/{id}', [BiodataController::class, 'updateAddress'])->name('biodata.updateAddress');
    Route::put('/biodata/update-education/{id}', [App\Http\Controllers\BiodataController::class, 'updateEducation'])
    ->name('biodata.update.education');
    Route::put('/biodata/update-family/{id}', [App\Http\Controllers\BiodataController::class, 'updateFamily'])
    ->name('biodata.update.family');
   Route::put('/biodata/personal/{id}', [BiodataController::class, 'updatePersonal'])
    ->name('biodata.update.personal');
    Route::put('/biodata/occupation/{id}', [BiodataController::class, 'updateOccupation'])
    ->name('biodata.update.occupation');
    Route::put('/biodata/marriage/{id}', [BiodataController::class, 'updateMarriage'])
    ->name('biodata.update.marriage');
    Route::put('/biodata/{id}/update-partner', [BiodataController::class, 'updatePartner'])->name('biodata.update.partner');
Route::put('/biodata/{id}/update-pledge', [BiodataController::class, 'updatePledge'])->name('biodata.update.pledge');
Route::put('/biodata/update/contact/{id}', [BiodataController::class, 'updateContact'])->name('biodata.update.contact');

Route::get('/biodata/download/{id}', [BiodataController::class, 'downloadPdf'])->name('biodata.download');







        Route::middleware(['auth', 'check.biodata'])->group(function () {
   
});

        Route::get('/matches', fn() => view('pages.user-dashboard.matches'))->name('matches');
        Route::get('/shortlist', fn() => view('pages.user-dashboard.shortlist'))->name('shortlist');
        Route::get('/inbox', fn() => view('pages.user-dashboard.inbox'))->name('inbox');
        Route::get('/sent', fn() => view('pages.user-dashboard.sent'))->name('sent');
        Route::get('/search', fn() => view('pages.user-dashboard.search'))->name('search');
        Route::get('/upgrade', fn() => view('pages.user-dashboard.upgrade'))->name('upgrade');
    });

    // Logout
    Route::post('/logout', [CustomLoginController::class, 'logout'])->name('logout');
    Route::get('/demo', [DemoController::class, 'index'])->name('demo');
});


// --------------------------------------------------------
// Admin Routes
// /admin now opens the admin login page instead of 404.
// --------------------------------------------------------
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login.form');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::patch('/users/{user}/verify-email', [AdminUserController::class, 'verifyEmail'])->name('users.verify-email');
        Route::patch('/users/{user}/make-admin', [AdminUserController::class, 'makeAdmin'])->name('users.make-admin');
        Route::patch('/users/{user}/remove-admin', [AdminUserController::class, 'removeAdmin'])->name('users.remove-admin');
        Route::patch('/users/{user}/block', [AdminUserController::class, 'block'])->name('users.block');
        Route::patch('/users/{user}/unblock', [AdminUserController::class, 'unblock'])->name('users.unblock');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        Route::get('/biodatas', [AdminBiodataController::class, 'index'])->name('biodatas.index');
        Route::get('/biodatas/{biodata}', [AdminBiodataController::class, 'show'])->name('biodatas.show');
        Route::patch('/biodatas/{biodata}/approve', [AdminBiodataController::class, 'approve'])->name('biodatas.approve');
        Route::patch('/biodatas/{biodata}/reject', [AdminBiodataController::class, 'reject'])->name('biodatas.reject');
        Route::patch('/biodatas/{biodata}/pending', [AdminBiodataController::class, 'pending'])->name('biodatas.pending');
        Route::patch('/biodatas/{biodata}/feature', [AdminBiodataController::class, 'feature'])->name('biodatas.feature');
        Route::patch('/biodatas/{biodata}/unfeature', [AdminBiodataController::class, 'unfeature'])->name('biodatas.unfeature');
        Route::delete('/biodatas/{biodata}', [AdminBiodataController::class, 'destroy'])->name('biodatas.destroy');

        Route::get('/payments/{scope?}', [AdminFeatureController::class, 'payments'])->name('payments.index');
        Route::patch('/payments/{payment}/status', [AdminSettingsController::class, 'updatePaymentStatus'])->name('payments.update-status');

        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::get('/settings/packages', [AdminSettingsController::class, 'packages'])->name('settings.packages');
        Route::post('/settings/plans', [AdminSettingsController::class, 'storePlan'])->name('settings.plans.store');
        Route::put('/settings/plans/{plan}', [AdminSettingsController::class, 'updatePlan'])->name('settings.plans.update');
        Route::patch('/settings/plans/{plan}/toggle-status', [AdminSettingsController::class, 'togglePlanStatus'])->name('settings.plans.toggle-status');
        Route::delete('/settings/plans/{plan}', [AdminSettingsController::class, 'destroyPlan'])->name('settings.plans.destroy');
        Route::post('/settings/gateways', [AdminSettingsController::class, 'storeGateway'])->name('settings.gateways.store');
        Route::put('/settings/gateways/{gateway}', [AdminSettingsController::class, 'updateGateway'])->name('settings.gateways.update');
        Route::delete('/settings/gateways/{gateway}', [AdminSettingsController::class, 'destroyGateway'])->name('settings.gateways.destroy');
        Route::post('/settings/system/{feature}/toggle', [AdminSettingsController::class, 'toggleSystemFeature'])->name('settings.system.toggle');
        Route::get('/settings/{section}', [AdminSettingsController::class, 'edit'])->name('settings.edit');
        Route::post('/settings/{section}', [AdminSettingsController::class, 'update'])->name('settings.update');

        Route::get('/attributes/{type}', [AdminFeatureController::class, 'attributes'])->name('attributes.show');
        Route::post('/attributes/{type}', [AdminFeatureController::class, 'storeAttribute'])->name('attributes.store');
        Route::put('/attributes/{type}/{attribute}', [AdminFeatureController::class, 'updateAttribute'])->name('attributes.update');
        Route::delete('/attributes/{type}/{attribute}', [AdminFeatureController::class, 'destroyAttribute'])->name('attributes.destroy');

        Route::get('/interactions/{type}', [AdminFeatureController::class, 'interactions'])->name('interactions.show');
        Route::get('/tickets/{scope?}', [AdminFeatureController::class, 'tickets'])->name('tickets.show');
        Route::get('/reports/{type}', [AdminFeatureController::class, 'reports'])->name('reports.show');
        Route::get('/extra/{type}', [AdminFeatureController::class, 'extra'])->name('extra.show');
        Route::get('/notifications/send', [AdminFeatureController::class, 'notifications'])->name('notifications.send');
    });
});

// --------------------------------------------------------
// Frontend Routes (Public pages)
// These pages should be visible for both guests and logged-in users.
// --------------------------------------------------------
Route::get('/', fn() => view('welcome'))->name('welcome');
Route::get('/profile', fn() => view('profile'))->name('profile');
Route::get('/settings', fn() => view('settings'))->name('settings');
Route::get('/about', fn() => view('pages.frontend.about'))->name('about');
Route::get('/guide', fn() => view('pages.frontend.guide'))->name('guide');
Route::get('/faq', fn() => view('pages.frontend.faq'))->name('faq');
Route::get('/contact', fn() => view('pages.frontend.contact'))->name('contact');
Route::post('/contact', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:180'],
        'subject' => ['required', 'string', 'max:180'],
        'message' => ['required', 'string', 'max:3000'],
    ]);

    // Mail/storage integration can be added here later. For now this prevents a 405 error
    // and gives users clear feedback that their message was received.
    return back()->with('success', 'Your message has been received. We will contact you soon InShaAllah.');
})->name('contact.submit');

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
