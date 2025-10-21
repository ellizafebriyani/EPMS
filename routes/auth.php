<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// 👉 Paksa render view login kita
Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

// Proses login (tetap controller Breeze)
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');
