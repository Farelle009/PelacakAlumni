<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('alumni', AlumniController::class);
Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/tracking/run/{alumni}', [TrackingController::class, 'run'])->name('tracking.run');
Route::get('/tracking/result/{alumni}', [TrackingController::class, 'result'])->name('tracking.result');
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
