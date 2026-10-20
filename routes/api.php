<?php

use App\Http\Controllers\TraineeResultController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| VR Headset & XR Interaction Simulation API Routes
|--------------------------------------------------------------------------
|
| These endpoints receive real-time telemetry from Unity (Meta Quest 3 Headset
| or Unity XR Interaction Simulator) and communicate with the live dashboard.
|
*/

// Session Ingestion: Unity sends completed drill payload (Score, Hazards, Wrong Clicks, Time)
Route::post('/trainee-results', [TraineeResultController::class, 'store']);

// Active Trainee Telemetry: Set or clear current VR operator during live drill
Route::post('/active-trainee', [TraineeResultController::class, 'setActiveTrainee']);
Route::post('/active-trainee/clear', [TraineeResultController::class, 'clearActiveTrainee']);

// Live Monitor Status Polling: Web dashboard polls active trainee stats
Route::get('/active-trainee', [TraineeResultController::class, 'liveMonitorStatus']);
Route::get('/live-monitor/status', [TraineeResultController::class, 'liveMonitorStatus']);
