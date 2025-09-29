<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\PlotController;
use App\Http\Controllers\Admin\CropController;

Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth'])->get('/dashboard', function () {
    $user = auth()->user();
    return match ($user->role ?? 'worker') {
        'admin' => redirect()->route('admin.'),
        'foreman' => redirect()->route('foreman.'),
        default => redirect()->route('worker.'),
    };
})->name('dashboard');

// Admin placeholders
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => view('admin.index'));
    Route::resource('plots', PlotController::class)->except(['show','edit']);
    Route::resource('crops', CropController::class)->except(['show','edit']);
    Route::get('/tasks', fn () => 'admin tasks');
    Route::get('/tools', fn () => 'admin tools');
    Route::get('/loans', fn () => 'admin loans');
    Route::get('/supplies', fn () => 'admin supplies');
    Route::get('/consumptions', fn () => 'admin consumptions');
    Route::get('/ledger', fn () => 'admin ledger');
});

// Foreman placeholders
Route::middleware(['auth', 'role:foreman'])->prefix('foreman')->name('foreman.')->group(function () {
    Route::get('/', fn () => 'foreman dashboard');
    Route::get('/tasks', fn () => 'foreman tasks');
    Route::get('/inventory', fn () => 'foreman inventory');
    Route::get('/loans', fn () => 'foreman loans');
});

// Worker placeholders
Route::middleware(['auth', 'role:worker'])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/', fn () => 'worker dashboard');
    Route::get('/tasks', fn () => 'worker tasks');
    Route::get('/loans', fn () => 'worker loans');
    Route::get('/reports', fn () => 'worker reports');
});
