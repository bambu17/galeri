<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GalleryController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/photos');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('photos', GalleryController::class)->except('show');
});
