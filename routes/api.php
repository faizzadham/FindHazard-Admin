<?php

use App\Http\Controllers\TraineeResultController;
use Illuminate\Support\Facades\Route;

Route::post('/trainee-results', [TraineeResultController::class, 'store']);
Route::post('/active-trainee', [TraineeResultController::class, 'setActiveTrainee']);
Route::post('/active-trainee/clear', [TraineeResultController::class, 'clearActiveTrainee']);
Route::get('/active-trainee', [TraineeResultController::class, 'liveMonitorStatus']);
Route::get('/live-monitor/status', [TraineeResultController::class, 'liveMonitorStatus']);


