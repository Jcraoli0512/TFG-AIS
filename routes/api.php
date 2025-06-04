<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtworkDisplayDateController;

// Rutas para las fechas de exhibición
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/artwork-display-dates', [ArtworkDisplayDateController::class, 'store']);
    Route::delete('/artwork-display-dates/{displayDate}', [ArtworkDisplayDateController::class, 'cancel']);
    Route::delete('/artwork-display-dates/cancel-all/{date}', [ArtworkDisplayDateController::class, 'cancelAll'])->where('date', '[0-9]{4}-[0-9]{2}-[0-9]{2}');
}); 