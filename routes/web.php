<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StationController;
use App\Http\Controllers\Admin\ChannelController;
use App\Http\Controllers\Admin\SongRequestController;
use App\Http\Controllers\Admin\PushNotificationController;
use App\Http\Controllers\Admin\AdCampaignController;
use App\Models\Station;
use App\Models\Channel;
use App\Models\SongRequest;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

require __DIR__.'/auth.php';

Route::get('/dashboard', function () {
    $stats = [
        'stations' => Station::count(),
        'activeStations' => Station::where('is_active', true)->count(),
        'channels' => Channel::count(),
        'activeChannels' => Channel::where('is_active', true)->count(),
        'newRequests' => SongRequest::where('status', SongRequest::STATUS_NEW)->count(),
        'requestsToday' => SongRequest::whereDate('created_at', now()->toDateString())->count(),
    ];

    $recentRequests = SongRequest::query()
        ->with(['station', 'channel'])
        ->latest()
        ->take(6)
        ->get();

    $channels = Channel::query()
        ->with('station')
        ->orderByDesc('is_active')
        ->orderBy('order')
        ->take(6)
        ->get();

    return view('dashboard', compact('stats', 'recentRequests', 'channels'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::resource('stations', StationController::class);
        Route::get('stations/{station}/channels', [StationController::class, 'channels'])
            ->name('stations.channels');

        Route::resource('channels', ChannelController::class);

        Route::get('song-requests', [SongRequestController::class, 'index'])
            ->name('song-requests.index');
        Route::patch('song-requests/{songRequest}', [SongRequestController::class, 'update'])
            ->name('song-requests.update');

        Route::get('push-notifications', [PushNotificationController::class, 'index'])
            ->name('push-notifications.index');
        Route::post('push-notifications', [PushNotificationController::class, 'store'])
            ->name('push-notifications.store');

        Route::get('ads', [AdCampaignController::class, 'index'])->name('ads.index');
        Route::post('ads', [AdCampaignController::class, 'store'])->name('ads.store');
        Route::put('ads/{adCampaign}', [AdCampaignController::class, 'update'])->name('ads.update');
        Route::delete('ads/{adCampaign}', [AdCampaignController::class, 'destroy'])->name('ads.destroy');
    });
