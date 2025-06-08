<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtworkDisplayDateController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\UserController;

// Rutas para las fechas de exhibición
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/artwork-display-dates', [ArtworkDisplayDateController::class, 'store']);
    Route::delete('/artwork-display-dates/{displayDate}', [ArtworkDisplayDateController::class, 'cancel']);
    Route::delete('/artwork-display-dates/cancel-all/{date}', [ArtworkDisplayDateController::class, 'cancelAll'])->where('date', '[0-9]{4}-[0-9]{2}-[0-9]{2}');
});

// Ruta para obtener obras de arte aleatorias (accesible para todos)
Route::get('/artworks/random', [ArtworkController::class, 'randomArtworks']);

// Ruta para obtener los datos de un artista específico (accesible para todos)
Route::get('/artists/{artist}', [UserController::class, 'showArtist']);

// Ruta de prueba para depuración
Route::get('/test-api-route', function () {
    return response()->json(['message' => 'Ruta de prueba API funciona!']);
}); 