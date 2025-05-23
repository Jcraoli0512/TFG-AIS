<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\CalendarController;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

// Página de bienvenida (para usuarios no autenticados)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Rutas autenticadas
|--------------------------------------------------------------------------
*/

// Página principal para usuarios autenticados
Route::get('/home', function () {
    return view('home');
})->middleware('auth')->name('home');

// Redirección desde /dashboard a /home (por compatibilidad con Breeze/Jetstream)
Route::get('/dashboard', function () {
    return redirect('/home');
})->middleware('auth')->name('dashboard');

// Perfil de usuario
Route::get('/profile', function () {
    $user = Auth::user();
    return view('profile', compact('user'));
})->middleware('auth')->name('profile.show');

// (Opcional) Edición de perfil, si la necesitas
Route::get('/profile/edit', function () {
    $user = Auth::user();
    return view('profile-edit', compact('user'));
})->middleware('auth')->name('profile.edit');

// Galería de imágenes
Route::get('/gallery', function () {
    return view('gallery');
})->middleware(['auth'])->name('gallery');

// Calendario
Route::get('/calendar', [CalendarController::class, 'index'])->middleware(['auth'])->name('calendar');
Route::get('/api/calendar-events', [CalendarController::class, 'getEvents'])->middleware(['auth']);
Route::get('/api/gallery-images/{date}', [CalendarController::class, 'getGalleryImages'])->middleware(['auth']);

/*
|--------------------------------------------------------------------------
| Cierre de sesión
|--------------------------------------------------------------------------
*/

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas de autenticación (login, registro, etc.)
|--------------------------------------------------------------------------
| Si usas Breeze, Jetstream o Fortify, normalmente se incluyen aquí:
*/
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}
