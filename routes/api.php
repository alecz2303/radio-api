<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\StationController;
use App\Http\Controllers\Api\V1\ChannelController;
use App\Http\Controllers\Api\V1\SongRequestController;
use App\Http\Controllers\Api\V1\PushDeviceController;
use App\Http\Controllers\Api\V1\AdCampaignController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::apiResource('stations', StationController::class)->except(['index', 'show']);
        Route::apiResource('channels', ChannelController::class)->except(['index', 'show']);
    });

    Route::get('stations', [StationController::class, 'index']);
    Route::get('stations/{slug}', [StationController::class, 'show']);
    Route::get('stations/{slug}/channels', [ChannelController::class, 'index']);
    Route::get('stations/{slug}/channels/{channelSlug}', [ChannelController::class, 'show']);
    Route::post('song-requests', [SongRequestController::class, 'store']);
    Route::post('push-devices', [PushDeviceController::class, 'store'])->middleware('throttle:30,1');

    Route::get('ads', [AdCampaignController::class, 'index']);
    Route::post('ads/{adCampaign}/impression', [AdCampaignController::class, 'impression'])->middleware('throttle:120,1');
    Route::post('ads/{adCampaign}/click', [AdCampaignController::class, 'click'])->middleware('throttle:60,1');
});
