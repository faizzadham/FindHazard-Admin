<?php

use App\Http\Controllers\TraineeResultController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — FindHazard VR Supervisor Dashboard
|--------------------------------------------------------------------------
|
| Handles multi-page navigation across the supervisor operations center:
| - Supervisor Login
| - Overview Command Center (KPIs & Recent Runs)
| - Live VR Session Monitor (Meta Quest Developer Hub Cast & Telemetry)
| - Trainee Directory & Leaderboard (Search, 10-item Pagination, Date Filter)
| - Sector Analytics (6 Warehouse Zones, Blindspots & Hazards)
| - Trainee Evaluation Protocol Guide (Scoring Logic & Tier Benchmarks)
| - Individual Trainee Dossier & Performance Statement
|
*/

// Authentication (Supervisor Login)
Route::get('/', [TraineeResultController::class, 'login'])->name('login');
Route::get('/login', [TraineeResultController::class, 'login']);

// Supervisor Operations Sections
Route::get('/overview', [TraineeResultController::class, 'index'])->name('dashboard.overview');
Route::get('/dashboard', [TraineeResultController::class, 'index']);
Route::get('/monitor', [TraineeResultController::class, 'monitor'])->name('dashboard.monitor');
Route::get('/directory', [TraineeResultController::class, 'directory'])->name('dashboard.directory');
Route::get('/analytics', [TraineeResultController::class, 'analytics'])->name('dashboard.analytics');
Route::get('/guide', [TraineeResultController::class, 'guide'])->name('dashboard.guide');

// Individual Trainee Dossier & Performance Statement
Route::get('/trainee/{id}', [TraineeResultController::class, 'show'])->name('dashboard.trainee');
