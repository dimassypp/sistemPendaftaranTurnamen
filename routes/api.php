<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeamController;

Route::get('/test', function () {
    return "API OK";
});

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/teams', [TeamController::class,'store']);
    Route::get('/teams/my', [TeamController::class,'my']);
    Route::get('/teams', [TeamController::class,'index']);
    Route::put('/teams/{id}', [TeamController::class,'update']);
    Route::delete('/teams/{id}', [TeamController::class,'destroy']);

});