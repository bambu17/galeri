<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GalleryController;
use App\Http\Middleware\EnsureAdminIsAuthenticated;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/photos');

Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->middleware('throttle:5,1')->name('admin.login.store');

Route::middleware(EnsureAdminIsAuthenticated::class)->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::patch('/photos/{photo}/status', [AdminController::class, 'updatePhotoStatus'])->name('photos.status');
    Route::delete('/photos/{photo}', [AdminController::class, 'destroyPhoto'])->name('photos.destroy');
});

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
