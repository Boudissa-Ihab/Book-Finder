<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\api\Auth\RegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:sanctum')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', RegistrationController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);
});

// If no GET route matches, we return a 404 response
Route::fallback(function () {
    return response()->json(['message' => 'Not found'], 404);
});
