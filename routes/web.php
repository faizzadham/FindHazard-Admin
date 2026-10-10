<?php

use App\Http\Controllers\TraineeResultController;
use Illuminate\Support\Facades\Route;

// Authentication Routes (Starts with Login page)
Route::get('/', [TraineeResultController::class, 'login'])->name('login');
Route::get('/login', [TraineeResultController::class, 'login']);

// Section Routes (Multi-Page Navigation)
Route::get('/overview', [TraineeResultController::class, 'index'])->name('dashboard.overview');
Route::get('/dashboard', [TraineeResultController::class, 'index']);
Route::get('/monitor', [TraineeResultController::class, 'monitor'])->name('dashboard.monitor');
Route::get('/directory', [TraineeResultController::class, 'directory'])->name('dashboard.directory');
Route::get('/analytics', [TraineeResultController::class, 'analytics'])->name('dashboard.analytics');
Route::get('/guide', [TraineeResultController::class, 'guide'])->name('dashboard.guide');
Route::get('/trainee/{id}', [TraineeResultController::class, 'show'])->name('dashboard.trainee');




