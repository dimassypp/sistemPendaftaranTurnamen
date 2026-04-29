<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TournamentController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\AnnouncementController;

// =======================
// Public
// =======================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::get('/test', function () {
    return "API OK";
});

// =======================
// Protected
// =======================
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

        // Announcement (admin)
        Route::post('/announcements',        [AnnouncementController::class, 'store']);
        Route::put('/announcements/{id}',    [AnnouncementController::class, 'update']);
        Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);
        
        // Team (Admin)
        Route::get('/teams',         [TeamController::class, 'index']);
        Route::put('/teams/{id}',    [TeamController::class, 'update']);
        Route::delete('/teams/{id}', [TeamController::class, 'destroy']);
    });

    // Announcement (public after login)
    Route::get('/announcements',      [AnnouncementController::class, 'index']);
    Route::get('/announcements/{id}', [AnnouncementController::class, 'show']);

    // Team
    Route::post('/teams',        [TeamController::class, 'store']);
    Route::get('/teams/my',      [TeamController::class, 'my']);

});