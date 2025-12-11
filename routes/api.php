<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('throttle:login'); // ← đây là throttle login