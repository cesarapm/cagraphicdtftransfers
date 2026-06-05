<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Authentication routes (outside Sanctum middleware, but with CSRF protection)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Redirigir todas las rutas a la vista principal para que Vue Router las maneje
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
