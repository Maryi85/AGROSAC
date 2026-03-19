<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Worker\DashboardController as WorkerDashboardController;
use App\Http\Controllers\Worker\LoanController as WorkerLoanController;
use App\Http\Controllers\Worker\ProfileController as WorkerProfileController;

Route::middleware(['auth', 'user.active', 'role:worker', \App\Http\Middleware\PreventBackHistory::class])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/', [WorkerDashboardController::class , 'index'])->name('index');
    Route::get('/dashboard/data', [WorkerDashboardController::class , 'data'])->name('dashboard.data');
    Route::get('/tasks', [WorkerDashboardController::class , 'tasks'])->name('tasks');
    Route::post('/tasks/{task}/complete', [WorkerDashboardController::class , 'completeTask'])->name('tasks.complete');
    Route::get('/tasks/pdf', [WorkerDashboardController::class , 'downloadTasksPdf'])->name('tasks.pdf');
    Route::get('/tools', [WorkerDashboardController::class , 'tools'])->name('tools');
    Route::post('/tools/request', [WorkerDashboardController::class , 'requestTool'])->name('tools.request');
    Route::post('/tools/{loan}/return', [WorkerDashboardController::class , 'returnTool'])->name('tools.return');
    Route::get('/tools/pdf', [WorkerDashboardController::class , 'downloadToolsPdf'])->name('tools.pdf');
    Route::get('/reports', [WorkerDashboardController::class , 'reports'])->name('reports');
    Route::post('/reports/generate', [WorkerDashboardController::class , 'generateReport'])->name('reports.generate');
    Route::get('/reports/pdf', [WorkerDashboardController::class , 'downloadReportsPdf'])->name('reports.pdf');

    // Worker loan management routes
    Route::resource('loans', WorkerLoanController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('loans/{loan}/return', [WorkerLoanController::class , 'returnForm'])->name('loans.return-form');
    Route::post('loans/{loan}/return', [WorkerLoanController::class , 'processReturn'])->name('loans.process-return');

    // Perfil del trabajador
    Route::get('profile', [WorkerProfileController::class , 'edit'])->name('profile');
    Route::put('profile', [WorkerProfileController::class , 'update'])->name('profile.update');
});
