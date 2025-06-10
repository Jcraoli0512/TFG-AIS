<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ArtworkDisplayDateController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

// Página de bienvenida (para usuarios no autenticados)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Página Nosotros (accesible sin autenticación)
Route::get('/nosotros', function () {
    return view('about');
})->name('about');

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

// Rutas de perfil
Route::middleware(['auth'])->group(function () {
    // Ruta base de perfil
    Route::get('/profile', function () {
        return redirect()->route('profile.show', ['user' => Auth::id()]);
    })->name('profile');

    // Edición de perfil
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/panoramic', [ProfileController::class, 'updatePanoramic'])->name('profile.update.panoramic');

    // Ver perfil de usuario específico
    Route::get('/profile/{user}', function (App\Models\User $user) {
        return view('profile', compact('user'));
    })->name('profile.show');
});

// Galería de imágenes (accesible para todos, carga directa desde el controlador)
Route::get('/gallery', [ArtworkController::class, 'indexGallery'])->name('gallery');

// Página de Artistas (accesible para todos, con búsqueda)
Route::get('/artists', [UserController::class, 'indexArtists'])->name('artists.index');
Route::post('/artists', [UserController::class, 'indexArtists'])->name('artists.search');

// Calendario
Route::get('/calendar', [CalendarController::class, 'index'])->middleware(['auth'])->name('calendar');
Route::get('/api/calendar-events', [CalendarController::class, 'getEvents'])->middleware(['auth']);
Route::get('/api/gallery-images/{date}', [CalendarController::class, 'getGalleryImages'])->middleware(['auth']);

// Rutas para la gestión de fechas de exhibición de obras
Route::middleware(['auth'])->group(function () {
    Route::post('/api/artwork-display-dates', [ArtworkDisplayDateController::class, 'store'])->name('artwork-display-dates.store');
    Route::delete('/api/artwork-display-dates/{displayDate}', [ArtworkDisplayDateController::class, 'destroy'])->name('artwork-display-dates.destroy');
    Route::post('/api/artwork-display-dates/{displayDate}/approve', [ArtworkDisplayDateController::class, 'approve'])
        ->middleware(['admin'])
        ->name('artwork-display-dates.approve');
    Route::delete('/api/artwork-display-dates/{displayDate}/cancel', [ArtworkDisplayDateController::class, 'cancel'])->name('artwork-display-dates.cancel');
    Route::delete('/api/artwork-display-dates/cancel-all/{date}', [ArtworkDisplayDateController::class, 'cancelAll'])
        ->where('date', '[0-9]{4}-[0-9]{2}-[0-9]{2}')
        ->name('artwork-display-dates.cancel-all');
});

// Nueva ruta para obtener la vista parcial de selección de obras
Route::get('/artworks/selection-partial', [ArtworkController::class, 'getArtworkSelectionPartial'])->middleware(['auth'])->name('artworks.selection-partial');

// Ruta específica para el formulario de creación de obras
Route::get('/artworks/create/form', [ArtworkController::class, 'create'])->middleware(['auth'])->name('artworks.create.form');

// Ruta pública para ver detalles de una obra (sin autenticación)
Route::get('/artworks/{artwork}', [ArtworkController::class, 'show'])->name('artworks.show.public');

// Ruta para verificar si una obra está siendo exhibida
Route::get('/artworks/{artwork}/check-exhibitions', [ArtworkController::class, 'checkExhibitions'])->name('artworks.check-exhibitions');

// Rutas para la gestión de obras (CRUD) - requiere autenticación
Route::resource('artworks', ArtworkController::class)->middleware(['auth'])->except(['show']);

// Exhibición 3D
Route::get('/exhibicion', function () {
    return view('exhibicion');
})->middleware(['auth'])->name('exhibicion');

/*
|--------------------------------------------------------------------------
| Rutas de administración
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [DashboardController::class, 'users'])->name('users.index');
    Route::get('/users/{user}/edit', [DashboardController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [DashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [DashboardController::class, 'deleteUser'])->name('users.delete');
    Route::delete('/users/{user}/artworks/{artwork}', [DashboardController::class, 'deleteUserArtwork'])->name('admin.users.artworks.delete');
    Route::delete('/users/{user}/panoramic-image', [DashboardController::class, 'deletePanoramicImage'])->name('admin.users.panoramic.delete');

    // Rutas para solicitudes de exhibición
    Route::prefix('exhibition-requests')->group(function () {
        Route::get('/', [DashboardController::class, 'exhibitionRequests'])->name('exhibition-requests.index');
        Route::post('/batch/{userId}/{date}/approve', [DashboardController::class, 'approveBatch'])->name('exhibition-requests.approve-batch');
        Route::delete('/batch/{userId}/{date}/reject', [DashboardController::class, 'rejectBatch'])->name('exhibition-requests.reject-batch');
        Route::get('/batch/{userId}/{date}/artworks', [DashboardController::class, 'getGroupArtworks'])->name('exhibition-requests.get-group-artworks');
        Route::get('/{groupKey}/artworks', [DashboardController::class, 'getGroupArtworks'])->name('exhibition-requests.group-artworks');
    });
});

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
