<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Foreman\DashboardController as ForemanDashboardController;
use App\Http\Controllers\Foreman\TaskController as ForemanTaskController;
use App\Http\Controllers\Foreman\WorkerController as ForemanWorkerController;
use App\Http\Controllers\Foreman\ToolController as ForemanToolController;
use App\Http\Controllers\Foreman\ToolEntryController as ForemanToolEntryController;
use App\Http\Controllers\Foreman\ToolDamageController as ForemanToolDamageController;
use App\Http\Controllers\Foreman\LoanController as ForemanLoanController;
use App\Http\Controllers\Foreman\SupplyController as ForemanSupplyController;
use App\Http\Controllers\Foreman\SupplyConsumptionController as ForemanSupplyConsumptionController;
use App\Http\Controllers\Foreman\SupplyMovementController as ForemanSupplyMovementController;
use App\Http\Controllers\Foreman\ProfileController as ForemanProfileController;

Route::middleware(['auth', 'user.active', 'role:foreman', \App\Http\Middleware\PreventBackHistory::class])->prefix('foreman')->name('foreman.')->group(function () {
    Route::get('/', [ForemanDashboardController::class , 'index'])->name('index');
    Route::get('data', [ForemanDashboardController::class , 'data'])->name('data');
    Route::get('profile', [ForemanProfileController::class , 'edit'])->name('profile');
    Route::put('profile', [ForemanProfileController::class , 'update'])->name('profile.update');
    Route::get('/dashboard-tasks', [ForemanDashboardController::class , 'tasks'])->name('dashboard.tasks');
    Route::get('/inventory', [ForemanDashboardController::class , 'inventory'])->name('inventory');
    Route::get('/dashboard-loans', [ForemanDashboardController::class , 'loans'])->name('dashboard.loans');

    // Task management routes
    Route::get('tasks/pdf', [ForemanTaskController::class , 'downloadPdf'])->name('tasks.pdf');
    Route::get('tasks/workers/list', [ForemanTaskController::class , 'getWorkers'])->name('tasks.workers.list');
    Route::get('tasks/crops/list', [ForemanTaskController::class , 'getCrops'])->name('tasks.crops.list');
    Route::post('tasks/{task}/approve', [ForemanTaskController::class , 'approve'])->name('tasks.approve');
    Route::post('tasks/{task}/invalidate', [ForemanTaskController::class , 'invalidate'])->name('tasks.invalidate');
    Route::post('tasks/{task}/complete', [ForemanTaskController::class , 'complete'])->name('tasks.complete');
    Route::resource('tasks', ForemanTaskController::class)->except(['show']);
    Route::get('tasks/{task}', [ForemanTaskController::class , 'show'])->name('tasks.show');

    // Worker management routes
    Route::get('workers/pdf', [ForemanWorkerController::class , 'downloadPdf'])->name('workers.pdf');
    Route::post('workers/{worker}/toggle-status', [ForemanWorkerController::class , 'toggleStatus'])->name('workers.toggle-status');
    Route::resource('workers', ForemanWorkerController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);

    // Tool management routes
    Route::get('tools/pdf', [ForemanToolController::class , 'downloadPdf'])->name('tools.pdf');
    Route::resource('tools', ForemanToolController::class);

    // Inventory management routes (tool entries and damage)
    Route::resource('tool-entries', ForemanToolEntryController::class);
    Route::resource('tool-damage', ForemanToolDamageController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);

    // Loan management routes (all admin functionalities)
    Route::get('loans/pdf', [ForemanLoanController::class , 'downloadPdf'])->name('loans.pdf');
    Route::resource('loans', ForemanLoanController::class)->only(['index', 'show', 'create', 'store', 'destroy']);
    Route::post('loans/{loan}/return', [ForemanLoanController::class , 'return'])->name('loans.return');
    Route::post('loans/{loan}/mark-lost', [ForemanLoanController::class , 'markAsLost'])->name('loans.mark-lost');
    Route::post('loans/{loan}/mark-damaged', [ForemanLoanController::class , 'markAsDamaged'])->name('loans.mark-damaged');

    // Loan approval routes
    Route::post('loans/{loan}/approve', [ForemanLoanController::class , 'approve'])->name('loans.approve');
    Route::post('loans/{loan}/reject', [ForemanLoanController::class , 'reject'])->name('loans.reject');
    Route::post('loans/{loan}/confirm-return', [ForemanLoanController::class , 'confirmReturn'])->name('loans.confirm-return');
    Route::post('loans/{loan}/process-approved', [ForemanLoanController::class , 'processApproved'])->name('loans.process-approved');

    // Supplies management routes (all admin functionalities)
    Route::get('supplies/pdf', [ForemanSupplyController::class , 'downloadPdf'])->name('supplies.pdf');
    Route::resource('supplies', ForemanSupplyController::class);
    Route::resource('supply-consumptions', ForemanSupplyConsumptionController::class);
    Route::resource('supply-movements', ForemanSupplyMovementController::class);
});
