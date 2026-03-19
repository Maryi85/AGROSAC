<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

Route::get('/', function () {
    return view('welcome');
});

// Developers / Team page (public)
Route::get('/developers', function () {
    return view('developers.index');
})->name('developers.index');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class , 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class , 'login']);
    Route::get('/register', [RegisterController::class , 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class , 'register']);

    // Password reset routes
    Route::get('/forgot-password', [PasswordResetLinkController::class , 'create'])
        ->middleware('guest')
        ->name('password.request');

    Route::post('/forgot-password', [PasswordResetLinkController::class , 'store'])
        ->middleware('guest')
        ->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class , 'create'])
        ->middleware('guest')
        ->name('password.reset');

    Route::post('/reset-password', [NewPasswordController::class , 'store'])
        ->middleware('guest')
        ->name('password.update');
});

Route::post('/logout', [LoginController::class , 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])->get('/dashboard', function () {
    $user = request()->user();
    return match ($user->role ?? 'worker') {
        'admin' => redirect()->route('admin.index'),
        'foreman' => redirect()->route('foreman.index'),
        default => redirect()->route('worker.index'),
    };
})->name('dashboard');
Route::middleware(['auth'])->get('/profile', function () {
    $user = request()->user();
    return match ($user->role ?? 'worker') {
        'admin' => redirect()->route('admin.profile'),
        'foreman' => redirect()->route('foreman.profile'),
        default => redirect()->route('worker.profile'),
    };
})->name('profile');

// Load separated route files
require __DIR__ . '/admin.php';
require __DIR__ . '/foreman.php';
require __DIR__ . '/worker.php';
require __DIR__ . '/storage.php';