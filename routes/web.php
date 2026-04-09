<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TrackingController;
use App\Http\Middleware\AdminAuthenticated;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
// Guest-only auth routes — redirect to dashboard if already logged in
// ─────────────────────────────────────────────────────────────────────────────

Route::prefix('admin')->name('admin.')->middleware('guest:admin')->group(function () {
    Route::get('/login',     [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AdminAuthController::class, 'login'])->name('login.post');
    Route::get('/register',  [AdminAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AdminAuthController::class, 'register'])->name('register.post');
});

// Logout sits outside the guest group (needs active session)
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])
    ->name('admin.logout')
    ->middleware(AdminAuthenticated::class);

// ─────────────────────────────────────────────────────────────────────────────
// Protected routes — all require admin login
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(AdminAuthenticated::class)->group(function () {

    // Dashboard (replaces the welcome view)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Alumni resource
    Route::resource('alumni', AlumniController::class)->parameters([
        'alumni' => 'alumni',
    ]);

    // Tracking
    // Note: status-check must be declared BEFORE /{alumni} wildcard routes
    // so Laravel does not treat "status-check" as an alumni ID.
    Route::prefix('tracking')->name('tracking.')->group(function () {
        Route::get('/',                [TrackingController::class, 'index'])->name('index');
        Route::get('/status-check',    [TrackingController::class, 'statusCheck'])->name('status-check');
        Route::post('/run/{alumni}',   [TrackingController::class, 'run'])->name('run');
        Route::get('/result/{alumni}', [TrackingController::class, 'result'])->name('result');
    });

    // Admin profile
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/profile',          [AdminAuthController::class, 'showProfile'])->name('profile');
        Route::put('/profile',          [AdminAuthController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [AdminAuthController::class, 'updatePassword'])->name('profile.password');
    });
});

use App\Http\Controllers\PddiktiController;

Route::prefix('pddikti')->name('pddikti.')->group(function () {
    Route::get('/', [PddiktiController::class, 'index'])->name('index');
    Route::post('/search', [PddiktiController::class, 'search'])->name('search');
});