<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HospitalController;

// ── Homepage ──────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ── Hospital routes ───────────────────────────────────────────
Route::prefix('hospital')->name('hospital.')->group(function () {

    // Register
    Route::get('/register',  [HospitalController::class, 'showRegister'])->name('register');
    Route::post('/register', [HospitalController::class, 'register'])->name('register.submit');

    // Login
    Route::get('/login',     [HospitalController::class, 'showLogin'])->name('login');
    Route::post('/login',    [HospitalController::class, 'login'])->name('login.submit');

    // Dashboard (protected)
    Route::get('/dashboard', [HospitalController::class, 'dashboard'])->name('dashboard');

    // Request Actions
    Route::post('/request/{id}/approve',  [HospitalController::class, 'approveRequest'])->name('request.approve');
    Route::post('/request/{id}/reject',   [HospitalController::class, 'rejectRequest'])->name('request.reject');
    Route::post('/request/{id}/complete', [HospitalController::class, 'completeRequest'])->name('request.complete');

    // Donation Actions
    Route::post('/donation/schedule',     [HospitalController::class, 'scheduleDonation'])->name('donation.schedule');
    Route::post('/donation/{id}/complete', [HospitalController::class, 'completeDonation'])->name('donation.complete');
    Route::post('/donation/{id}/cancel',  [HospitalController::class, 'cancelDonation'])->name('donation.cancel');

    // Logout
    Route::post('/logout',   [HospitalController::class, 'logout'])->name('logout');
});