<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\StationController;
use App\Http\Controllers\Api\V1\ChannelController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    // Public auth routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        // CRUD protegido (solo administradores, por ejemplo)
        Route::apiResource('stations', StationController::class)->except(['index', 'show']);
        Route::apiResource('channels', ChannelController::class)->except(['index', 'show']);
    });

    // Public (para Flutter)
    Route::get('stations', [StationController::class, 'index']);
    Route::get('stations/{slug}', [StationController::class, 'show']);
    Route::get('stations/{slug}/channels', [ChannelController::class, 'index']);
    Route::get('stations/{slug}/channels/{channelSlug}', [ChannelController::class, 'show']);
});