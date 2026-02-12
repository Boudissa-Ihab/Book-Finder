<?php

use App\Enums\Roles;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\api\Auth\RegistrationController;
use App\Http\Controllers\Api\Books\BookController;
use App\Http\Controllers\Api\Books\FavoriteController;
use App\Http\Controllers\Api\Books\GoogleApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Guest routes */
Route::middleware('guest:sanctum')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', RegistrationController::class);
});

/* Authenticated routes */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);
    // Books
    Route::get('/books', [BookController::class, 'index']);

    // Favorites
    Route::prefix('favorites')->controller(FavoriteController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/{book_id}', 'store');
        Route::delete('/{book_id}', 'destroy');
    });

    // Google Books API Routes (Admin only access)
    Route::prefix('google/books')->middleware("role:".Roles::ADMIN)
    ->controller(GoogleApiController::class)->group(function () {
        Route::get('/view', 'view');
        Route::get('/search', 'search');
        Route::post('/import', 'import');
    });
});

Route::get('/api/documentation', function () {
    return view('l5-swagger::index');
});

// If no GET route matches, we return a 404 response
Route::fallback(function () {
    return response()->json(['message' => 'Not found'], 404);
});
