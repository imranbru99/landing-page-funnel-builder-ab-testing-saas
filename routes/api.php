<?php

use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/public/experiments/{experiment}/assign', [ApiController::class, 'assignExperiment']);
    Route::post('/public/experiments/{experiment}/events', [ApiController::class, 'experimentEvent']);
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/pages', [ApiController::class, 'pages']);
    Route::post('/pages/{page}/publish', [ApiController::class, 'publishPage']);
    Route::get('/contacts', [ApiController::class, 'contacts']);
    Route::get('/experiments/{experiment}/summary', [ApiController::class, 'experimentSummary']);
});
