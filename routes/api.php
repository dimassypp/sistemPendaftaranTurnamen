<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\Api\AnnouncementController;

// Semua user
Route::get('/announcements',      [AnnouncementController::class, 'index']);
Route::get('/announcements/{id}', [AnnouncementController::class, 'show']);

// Admin only
Route::middleware('role:admin')->group(function () {
    Route::post('/announcements',          [AnnouncementController::class, 'store']);
    Route::put('/announcements/{id}',      [AnnouncementController::class, 'update']);
    Route::delete('/announcements/{id}',   [AnnouncementController::class, 'destroy']);
});