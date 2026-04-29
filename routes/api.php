<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TournamentController;

// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Protected
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Tournament
    Route::get('/tournaments',      [TournamentController::class, 'index']);
    Route::get('/tournaments/{id}', [TournamentController::class, 'show']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/tournaments',        [TournamentController::class, 'store']);
        Route::put('/tournaments/{id}',    [TournamentController::class, 'update']);
        Route::delete('/tournaments/{id}', [TournamentController::class, 'destroy']);
    });

});