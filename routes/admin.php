<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlotController;
use App\Http\Controllers\Admin\CropController;
use App\Http\Controllers\Admin\CropTrackingController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\ToolController;
use App\Http\Controllers\Admin\ToolEntryController;
use App\Http\Controllers\Admin\ToolDamageController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\SupplyController;
use App\Http\Controllers\Admin\SupplyConsumptionController;
use App\Http\Controllers\Admin\SupplyMovementController;
use App\Http\Controllers\Admin\LedgerController;
use App\Http\Controllers\Admin\ForemanController;
use App\Http\Controllers\Admin\FarmSettingController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Admin\ProfileController;

Route::middleware(['auth', 'role:admin', \App\Http\Middleware\PreventBackHistory::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class , 'index'])->name('index');
    Route::get('data', [DashboardController::class , 'data'])->name('data');
    Route::get('profile', [ProfileController::class , 'edit'])->name('profile');
    Route::put('profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::resource('plots', PlotController::class)->except(['show', 'edit']);
    Route::get('plots/pdf', [PlotController::class , 'downloadPdf'])->name('plots.pdf');
    Route::get('crops/pdf', [CropController::class , 'downloadPdf'])->name('crops.pdf');
    Route::post('crops/{crop}/disable', [CropController::class , 'disable'])->name('crops.disable');
    Route::post('crops/{crop}/enable', [CropController::class , 'enable'])->name('crops.enable');
    Route::resource('crops', CropController::class);

    // Crop Tracking routes
    Route::resource('crop-tracking', CropTrackingController::class)->except(['show'])->parameters([
        'crop-tracking' => 'tracking'
    ]);


    Route::get('tasks/pdf', [TaskController::class , 'downloadPdf'])->name('tasks.pdf');
    Route::get('tasks/workers/list', [TaskController::class , 'getWorkers'])->name('tasks.workers.list');
    Route::get('tasks/crops/list', [TaskController::class , 'getCrops'])->name('tasks.crops.list');
    Route::post('tasks/{task}/approve', [TaskController::class , 'approve'])->name('tasks.approve');
    Route::post('tasks/{task}/invalidate', [TaskController::class , 'invalidate'])->name('tasks.invalidate');
    Route::resource('tasks', TaskController::class)->except(['show']);
    Route::get('tools/pdf', [ToolController::class , 'downloadPdf'])->name('tools.pdf');
    Route::resource('tools', ToolController::class);
    Route::resource('tool-entries', ToolEntryController::class);
    Route::resource('tool-damage', ToolDamageController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    // Loan management routes (admin only manages approvals, not creates loans)
    Route::get('loans/pdf', [LoanController::class , 'downloadPdf'])->name('loans.pdf');
    Route::get('loans', [LoanController::class , 'index'])->name('loans.index');
    Route::get('loans/{loan}', [LoanController::class , 'show'])->name('loans.show');
    Route::post('loans/{loan}/return', [LoanController::class , 'return'])->name('loans.return');
    Route::post('loans/{loan}/mark-lost', [LoanController::class , 'markAsLost'])->name('loans.mark-lost');
    Route::post('loans/{loan}/mark-damaged', [LoanController::class , 'markAsDamaged'])->name('loans.mark-damaged');
    Route::delete('loans/{loan}', [LoanController::class , 'destroy'])->name('loans.destroy');

    // Loan approval routes
    Route::post('loans/{loan}/approve', [LoanController::class , 'approve'])->name('loans.approve');
    Route::post('loans/{loan}/reject', [LoanController::class , 'reject'])->name('loans.reject');
    Route::post('loans/{loan}/confirm-return', [LoanController::class , 'confirmReturn'])->name('loans.confirm-return');
    Route::post('loans/{loan}/process-approved', [LoanController::class , 'processApproved'])->name('loans.process-approved');
    Route::get('supplies/pdf', [SupplyController::class , 'downloadPdf'])->name('supplies.pdf');
    Route::resource('supplies', SupplyController::class);
    Route::resource('supply-consumptions', SupplyConsumptionController::class);
    Route::resource('supply-movements', SupplyMovementController::class);
    Route::resource('ledger', LedgerController::class);
    Route::get('ledger-dashboard', [LedgerController::class , 'dashboard'])->name('ledger.dashboard');
    Route::get('ledger-dashboard/pdf', [LedgerController::class , 'downloadDashboardPdf'])->name('ledger.dashboard.pdf');
    Route::get('ledger-crop-analysis/pdf', [LedgerController::class , 'downloadCropAnalysisPdf'])->name('ledger.crop-analysis.pdf');
    Route::get('ledger-movements/pdf', [LedgerController::class , 'downloadMovementsPdf'])->name('ledger.movements.pdf');
    Route::get('foremen/pdf', [ForemanController::class , 'downloadPdf'])->name('foremen.pdf');
    Route::post('foremen/{foreman}/toggle-status', [ForemanController::class , 'toggleStatus'])->name('foremen.toggle-status')->middleware('ajax.validation');
    Route::resource('foremen', ForemanController::class)->middleware('ajax.validation');

    // Farm settings routes
    Route::get('farm-settings/edit', [FarmSettingController::class , 'edit'])->name('farm-settings.edit');
    Route::put('farm-settings', [FarmSettingController::class , 'update'])->name('farm-settings.update');
    Route::delete('farm-settings', [FarmSettingController::class , 'destroy'])->name('farm-settings.destroy');
    Route::get('workers/pdf', [WorkerController::class , 'downloadPdf'])->name('workers.pdf');
    Route::resource('workers', WorkerController::class);
    Route::post('workers/{worker}/toggle-status', [WorkerController::class , 'toggleStatus'])->name('workers.toggle-status');
    Route::get('workers/{worker}/report', [WorkerController::class , 'report'])->name('workers.report');
    Route::get('workers/{worker}/report-data', [WorkerController::class , 'reportData'])->name('workers.report-data');
    Route::get('workers/{worker}/report-pdf', [WorkerController::class , 'reportPdf'])->name('workers.report-pdf');
});
