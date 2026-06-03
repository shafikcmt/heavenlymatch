<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PhoneVerificationController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\MatchController;
use App\Http\Controllers\Dashboard\SearchController;
use App\Http\Controllers\Dashboard\InterestController;
use App\Http\Controllers\Dashboard\InboxController;
use App\Http\Controllers\Dashboard\ShortlistController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Biodata\BiodataWizardController;
use App\Http\Controllers\ProfileViewController;
use App\Http\Controllers\Admin\AdminLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\VerificationController;

// ── Public marketing pages ────────────────────────────────────────────────────
Route::get('/',             [PublicPageController::class, 'home'])->name('home');
Route::get('/how-it-works', [PublicPageController::class, 'howItWorks'])->name('how-it-works');
Route::get('/pricing',      [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/about',        [PublicPageController::class, 'about'])->name('about');
Route::get('/contact',      [PublicPageController::class, 'contact'])->name('contact');
Route::get('/blog',         [PublicPageController::class, 'blog'])->name('blog.index');
Route::get('/blog/{slug}',  [PublicPageController::class, 'blogShow'])->name('blog.show');
Route::get('/terms',        [PublicPageController::class, 'terms'])->name('terms');
Route::get('/privacy',      [PublicPageController::class, 'privacy'])->name('privacy');

// ── Public biodata search (accessible to guests) ─────────────────────────────
Route::get('/profiles',                  [PublicProfileController::class, 'index'])->name('profiles.index');
Route::get('/profiles/{registrationId}', [PublicProfileController::class, 'show'])->name('profiles.show');

// ── SEO / crawlers ────────────────────────────────────────────────────────────
Route::get('/robots.txt',   [PublicPageController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml',  [PublicPageController::class, 'sitemap'])->name('sitemap');

// Public photo serving (HMAC-signed, privacy enforced)
Route::get('/photo/{registrationId}/{photoIndex?}', [\App\Http\Controllers\Api\PhotoController::class, 'serve'])
    ->where('photoIndex', '[0-9]+')
    ->name('api.photo.serve');

// ── Language switcher (guests + authenticated) ────────────────────────────────
Route::post('/language/{locale}', [PublicPageController::class, 'switchLocale'])
    ->middleware('web')
    ->name('language.switch');

// ── Admin auth (outside user auth middleware) ─────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',  [AdminLoginController::class, 'show'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'store'])->name('login.submit');
    Route::post('/logout', [AdminLoginController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');
});

// ── Guest-only auth ───────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class, 'show'])->name('login');
    Route::post('/login',   [LoginController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register',[RegisterController::class, 'store'])->name('register.store');

    // Registration phone OTP (JSON). Throttled to curb SMS abuse.
    Route::post('/register/phone/send-otp', [PhoneVerificationController::class, 'sendOtp'])
        ->middleware('throttle:6,10')
        ->name('register.phone.send-otp');
    Route::post('/register/phone/verify-otp', [PhoneVerificationController::class, 'verifyOtp'])
        ->middleware('throttle:10,10')
        ->name('register.phone.verify-otp');

    Route::get('/forgot-password',        [PasswordController::class, 'request'])->name('password.request');
    Route::post('/forgot-password',       [PasswordController::class, 'email'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password',        [PasswordController::class, 'update'])->name('password.update');
});

// Social OAuth — Google and Facebook (provider validated by where constraint + controller)
Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect'])
    ->name('auth.social.redirect')
    ->where('provider', 'google|facebook')
    ->middleware('throttle:10,1');
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback'])
    ->name('auth.social.callback')
    ->where('provider', 'google|facebook')
    ->middleware('throttle:10,1');

// Email verification
Route::get('/verify-email',                    [LoginController::class, 'verifyNotice'])->name('verification.notice')->middleware('auth');
Route::get('/verify-email/{id}/{hash}',        [LoginController::class, 'verifyEmail'])->name('verification.verify')->middleware(['auth', 'signed']);
Route::post('/verify-email/resend',            [LoginController::class, 'resendVerification'])->name('verification.send')->middleware(['auth', 'throttle:6,1']);

// ── Authenticated user routes ─────────────────────────────────────────────────
Route::middleware(['auth', 'verified.user'])->group(function () {

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // ── Biodata wizard (accessible before biodata is complete) ────────────────
    Route::prefix('biodata')->name('biodata.')->group(function () {
        Route::get('/wizard/{step?}', [BiodataWizardController::class, 'show'])
            ->where('step', '[1-9]')
            ->name('wizard');
        Route::post('/wizard/{step}', [BiodataWizardController::class, 'save'])
            ->where('step', '[1-9]')
            ->name('save');
    });

    // ── Dashboard routes (biodata must be completed) ───────────────────────────
    Route::middleware('check.biodata')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Matches & Search
        Route::get('/matches',   [MatchController::class, 'index'])->name('matches.index');
        Route::get('/search',    [SearchController::class, 'index'])->name('search.index');

        // Interests / connection requests
        Route::get('/interests/received',        [InterestController::class, 'received'])->name('interests.received');
        Route::get('/interests/sent',            [InterestController::class, 'sent'])->name('interests.sent');
        Route::post('/interests',                [InterestController::class, 'store'])->name('interests.store');
        Route::post('/interests/{id}/respond',   [InterestController::class, 'respond'])->name('interests.respond');
        Route::delete('/interests/{id}',         [InterestController::class, 'withdraw'])->name('interests.withdraw');

        // Inbox / messaging
        Route::get('/inbox',                             [InboxController::class, 'index'])->name('inbox.index');
        Route::get('/inbox/{conversationId}',            [InboxController::class, 'show'])->name('inbox.show');
        Route::post('/inbox/{conversationId}/send',      [InboxController::class, 'send'])->name('inbox.send');
        Route::get('/inbox/{conversationId}/poll/{afterId}', [InboxController::class, 'poll'])->name('inbox.poll');

        // Shortlist
        Route::get('/shortlist',   [ShortlistController::class, 'index'])->name('shortlist.index');
        Route::post('/shortlist',  [ShortlistController::class, 'toggle'])->name('shortlist.toggle');

        // Notifications
        Route::get('/notifications',           [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read',[NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

        // Profile pages
        Route::get('/dashboard/profile',        [ProfileViewController::class, 'myProfile'])->name('dashboard.profile');
        Route::get('/profile/{registrationId}', [ProfileViewController::class, 'show'])->name('profile.show');
        Route::get('/who-viewed',               [ProfileViewController::class, 'whoViewed'])->name('profile.who-viewed');

        // Upgrade / Payments
        Route::get('/upgrade',               [\App\Http\Controllers\Payment\PaymentController::class, 'plans'])->name('upgrade.plans');
        Route::post('/upgrade/checkout',     [\App\Http\Controllers\Payment\PaymentController::class, 'checkout'])->name('upgrade.checkout');
        Route::get('/upgrade/manual/{txn}',  [\App\Http\Controllers\Payment\PaymentController::class, 'manualForm'])->name('upgrade.manual');
        Route::post('/upgrade/manual/{txn}', [\App\Http\Controllers\Payment\PaymentController::class, 'manualSubmit'])->name('upgrade.manual.submit');
        Route::get('/upgrade/status',        [\App\Http\Controllers\Payment\PaymentController::class, 'status'])->name('upgrade.status');
        Route::get('/upgrade/callback',      [\App\Http\Controllers\Payment\PaymentController::class, 'callback'])->name('upgrade.callback');
        Route::get('/upgrade/success',       [\App\Http\Controllers\Payment\PaymentController::class, 'success'])->name('upgrade.success');

        // Photo management (own photos)
        Route::prefix('profile/photos')->name('profile.photos.')->group(function () {
            Route::get('/',                          [\App\Http\Controllers\Biodata\PhotoUploadController::class, 'index'])->name('index');
            Route::post('/',                         [\App\Http\Controllers\Biodata\PhotoUploadController::class, 'store'])->name('store');
            Route::put('/visibility',                [\App\Http\Controllers\Biodata\PhotoUploadController::class, 'updateVisibility'])->name('visibility');
            Route::delete('/{index}',                [\App\Http\Controllers\Biodata\PhotoUploadController::class, 'destroy'])->name('destroy')->whereNumber('index');
            Route::put('/{index}/primary',           [\App\Http\Controllers\Biodata\PhotoUploadController::class, 'setPrimary'])->name('primary')->whereNumber('index');
            Route::post('/requests/{requestId}/respond', [\App\Http\Controllers\Biodata\PhotoUploadController::class, 'respondRequest'])->name('requests.respond')->whereNumber('requestId');
        });

        // Settings
        Route::get('/settings',             [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings/profile',     [SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/password',    [SettingsController::class, 'updatePassword'])->name('settings.password');
        Route::delete('/settings/account',  [SettingsController::class, 'deleteAccount'])->name('settings.delete');

        // Photo access (Islamic mode)
        Route::post('/photo/request-access/{registrationId}', [\App\Http\Controllers\Api\PhotoController::class, 'requestAccess'])->name('photo.request-access');
        Route::post('/photo/respond-access/{requestId}',      [\App\Http\Controllers\Api\PhotoController::class, 'respondAccess'])->name('photo.respond-access');

        // Identity verification
        Route::get('/verify/identity', [VerificationController::class, 'identity'])->name('verify.identity');

        // Reports
        Route::post('/report/{registrationId}', [\App\Http\Controllers\ReportController::class, 'store'])->name('report.store');
    });
});

// ── Admin panel (auth + admin middleware, no verified.user required) ───────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',           [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users',      [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/ban',      [\App\Http\Controllers\Admin\AdminUserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{id}/unban',    [\App\Http\Controllers\Admin\AdminUserController::class, 'unban'])->name('users.unban');
    Route::post('/users/{id}/suspend',  [\App\Http\Controllers\Admin\AdminUserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{id}/activate', [\App\Http\Controllers\Admin\AdminUserController::class, 'activate'])->name('users.activate');
    Route::post('/users/{id}/verify',   [\App\Http\Controllers\Admin\AdminUserController::class, 'verify'])->name('users.verify');
    Route::get('/biodatas',              [\App\Http\Controllers\Admin\AdminBiodataController::class, 'index'])->name('biodatas.index');
    Route::get('/biodatas/{id}',         [\App\Http\Controllers\Admin\AdminBiodataController::class, 'show'])->name('biodatas.show');
    Route::post('/biodatas/{id}/approve',[\App\Http\Controllers\Admin\AdminBiodataController::class, 'approve'])->name('biodatas.approve');
    Route::post('/biodatas/{id}/reject', [\App\Http\Controllers\Admin\AdminBiodataController::class, 'reject'])->name('biodatas.reject');
    Route::get('/payments',                    [\App\Http\Controllers\Admin\AdminPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{id}/approve',      [\App\Http\Controllers\Admin\AdminPaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{id}/reject',       [\App\Http\Controllers\Admin\AdminPaymentController::class, 'reject'])->name('payments.reject');
    Route::get('/payments/screenshot/{id}',    [\App\Http\Controllers\Admin\AdminPaymentController::class, 'serveScreenshot'])->name('payments.screenshot');
    Route::get('/reports',                [\App\Http\Controllers\Admin\AdminReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/{id}/resolve',  [\App\Http\Controllers\Admin\AdminReportController::class, 'resolve'])->name('reports.resolve');
    Route::post('/reports/{id}/dismiss',  [\App\Http\Controllers\Admin\AdminReportController::class, 'dismiss'])->name('reports.dismiss');
    Route::get('/settings',                     [\App\Http\Controllers\Admin\AdminSettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings',                     [\App\Http\Controllers\Admin\AdminSettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/media/{key}',        [\App\Http\Controllers\Admin\AdminSettingsController::class, 'uploadMedia'])->name('settings.media.upload');
    Route::delete('/settings/media/{key}',      [\App\Http\Controllers\Admin\AdminSettingsController::class, 'removeMedia'])->name('settings.media.remove');
});
