<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\Api\TournamentController;

// Member & admin
Route::get('/tournaments',      [TournamentController::class, 'index']);
Route::get('/tournaments/{id}', [TournamentController::class, 'show']);

// Admin only
Route::middleware('role:admin')->group(function () {
    Route::post('/tournaments',          [TournamentController::class, 'store']);
    Route::put('/tournaments/{id}',      [TournamentController::class, 'update']);
    Route::delete('/tournaments/{id}',   [TournamentController::class, 'destroy']);
});