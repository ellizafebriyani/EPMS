<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/** root diarahkan ke login */
Route::get('/', fn () => redirect()->route('login'));

require __DIR__.'/auth.php'; // Wajib: muat route login/logout

/** halaman privat */
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/** fallback debug (opsional; hapus jika tidak perlu) */
Route::fallback(fn () => response('Route tidak ditemukan. Cek web.php & auth.php', 404));
