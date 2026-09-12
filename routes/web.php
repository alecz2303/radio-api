<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StationController;
use App\Http\Controllers\Admin\ChannelController;
use App\Http\Controllers\Admin\SongRequestController;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

Route::get('/dashboard', function () {
    return view('dashboard');
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
    });
