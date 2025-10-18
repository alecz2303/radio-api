<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StationController;
use App\Http\Controllers\Admin\ChannelController;

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


/*
|--------------------------------------------------------------------------
| Panel administrativo
|--------------------------------------------------------------------------
|
| Todas las rutas del panel se agrupan bajo el prefijo /admin
| y usan nombres únicos con el prefijo "admin." para evitar conflicto
| con las rutas API (como /api/v1/stations).
|
| Ejemplo:
| - GET  /admin/stations            →  admin.stations.index
| - GET  /admin/stations/create     →  admin.stations.create
| - POST /admin/stations            →  admin.stations.store
| - GET  /admin/stations/{id}/edit  →  admin.stations.edit
| - PUT  /admin/stations/{id}       →  admin.stations.update
| - DELETE /admin/stations/{id}     →  admin.stations.destroy
|
*/

Route::middleware(['auth'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        // CRUD de estaciones
        Route::resource('stations', StationController::class);
        Route::get('stations/{station}/channels', [StationController::class, 'channels'])
            ->name('stations.channels');

        // CRUD de canales (emisoras)
        Route::resource('channels', ChannelController::class);
    });