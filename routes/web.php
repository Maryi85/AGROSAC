<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\PlotController;
use App\Http\Controllers\Admin\CropController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\ToolController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\SupplyController;
use App\Http\Controllers\Admin\SupplyConsumptionController;
use App\Http\Controllers\Admin\SupplyMovementController;
use App\Http\Controllers\Admin\LedgerController;
use App\Http\Controllers\Admin\CropTrackingController;
use App\Http\Controllers\Admin\ToolEntryController;
use App\Http\Controllers\Admin\ToolDamageController;
use App\Http\Controllers\Foreman\ToolEntryController as ForemanToolEntryController;
use App\Http\Controllers\Foreman\ToolDamageController as ForemanToolDamageController;
use App\Http\Controllers\Foreman\SupplyController as ForemanSupplyController;
use App\Http\Controllers\Foreman\SupplyConsumptionController as ForemanSupplyConsumptionController;
use App\Http\Controllers\Foreman\SupplyMovementController as ForemanSupplyMovementController;
use App\Http\Controllers\Admin\ForemanController;
use App\Http\Controllers\Foreman\DashboardController as ForemanDashboardController;
use App\Http\Controllers\Foreman\TaskController as ForemanTaskController;
use App\Http\Controllers\Foreman\WorkerController as ForemanWorkerController;
use App\Http\Controllers\Foreman\ToolController as ForemanToolController;
use App\Http\Controllers\Foreman\LoanController as ForemanLoanController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FarmSettingController;
use App\Http\Controllers\Worker\DashboardController as WorkerDashboardController;
use App\Http\Controllers\Worker\LoanController as WorkerLoanController;
use App\Http\Controllers\Worker\ProfileController as WorkerProfileController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    // Password reset routes
 Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth'])->get('/dashboard', function () {
    $user = request()->user();
    return match ($user->role ?? 'worker') {
        'admin' => redirect()->route('admin.index'),
        'foreman' => redirect()->route('foreman.index'),
        default => redirect()->route('worker.index'),
    };
})->name('dashboard');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::resource('plots', PlotController::class)->except(['show','edit']);
    Route::get('plots/pdf', [PlotController::class, 'downloadPdf'])->name('plots.pdf');
    Route::get('crops/pdf', [CropController::class, 'downloadPdf'])->name('crops.pdf');
    Route::post('crops/{crop}/disable', [CropController::class, 'disable'])->name('crops.disable');
    Route::post('crops/{crop}/enable', [CropController::class, 'enable'])->name('crops.enable');
    Route::resource('crops', CropController::class);
    
    // Crop Tracking routes
    Route::resource('crop-tracking', CropTrackingController::class)->except(['show'])->parameters([
        'crop-tracking' => 'tracking'
    ]);
    
    
    Route::get('tasks/pdf', [TaskController::class, 'downloadPdf'])->name('tasks.pdf');
    Route::get('tasks/workers/list', [TaskController::class, 'getWorkers'])->name('tasks.workers.list');
    Route::get('tasks/crops/list', [TaskController::class, 'getCrops'])->name('tasks.crops.list');
    Route::post('tasks/{task}/admin-approve', [TaskController::class, 'approve'])->name('tasks.approve');
    Route::post('tasks/{task}/invalidate', [TaskController::class, 'invalidate'])->name('tasks.invalidate');
    Route::resource('tasks', TaskController::class);
    Route::get('tools/pdf', [ToolController::class, 'downloadPdf'])->name('tools.pdf');
    Route::resource('tools', ToolController::class);
    Route::resource('tool-entries', ToolEntryController::class);
    Route::resource('tool-damage', ToolDamageController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    // Loan management routes (admin only manages approvals, not creates loans)
    Route::get('loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
    Route::post('loans/{loan}/return', [LoanController::class, 'return'])->name('loans.return');
    Route::post('loans/{loan}/mark-lost', [LoanController::class, 'markAsLost'])->name('loans.mark-lost');
    Route::post('loans/{loan}/mark-damaged', [LoanController::class, 'markAsDamaged'])->name('loans.mark-damaged');
    Route::delete('loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');
    
    // Loan approval routes
    Route::post('loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::post('loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
    Route::post('loans/{loan}/confirm-return', [LoanController::class, 'confirmReturn'])->name('loans.confirm-return');
    Route::post('loans/{loan}/process-approved', [LoanController::class, 'processApproved'])->name('loans.process-approved');
    Route::get('loans/pdf', [LoanController::class, 'downloadPdf'])->name('loans.pdf');
    Route::get('supplies/pdf', [SupplyController::class, 'downloadPdf'])->name('supplies.pdf');
    Route::resource('supplies', SupplyController::class);
    Route::resource('supply-consumptions', SupplyConsumptionController::class);
    Route::resource('supply-movements', SupplyMovementController::class);
    Route::resource('ledger', LedgerController::class);
    Route::get('ledger-dashboard', [LedgerController::class, 'dashboard'])->name('ledger.dashboard');
    Route::get('ledger-dashboard/pdf', [LedgerController::class, 'downloadDashboardPdf'])->name('ledger.dashboard.pdf');
    Route::get('ledger-crop-analysis/pdf', [LedgerController::class, 'downloadCropAnalysisPdf'])->name('ledger.crop-analysis.pdf');
    Route::get('ledger-movements/pdf', [LedgerController::class, 'downloadMovementsPdf'])->name('ledger.movements.pdf');
    Route::get('foremen/pdf', [ForemanController::class, 'downloadPdf'])->name('foremen.pdf');
    Route::post('foremen/{foreman}/toggle-status', [ForemanController::class, 'toggleStatus'])->name('foremen.toggle-status')->middleware('ajax.validation');
    Route::resource('foremen', ForemanController::class)->middleware('ajax.validation');
    
    // Farm settings routes
    Route::get('farm-settings/edit', [FarmSettingController::class, 'edit'])->name('farm-settings.edit');
    Route::put('farm-settings', [FarmSettingController::class, 'update'])->name('farm-settings.update');
    Route::get('workers/pdf', [WorkerController::class, 'downloadPdf'])->name('workers.pdf');
    Route::resource('workers', WorkerController::class);
    Route::post('workers/{worker}/reset-password', [WorkerController::class, 'resetPassword'])->name('workers.reset-password');
    Route::post('workers/{worker}/toggle-status', [WorkerController::class, 'toggleStatus'])->name('workers.toggle-status');
    Route::get('workers/{worker}/tasks', [WorkerController::class, 'tasks'])->name('workers.tasks');
    Route::get('workers/{worker}/report', [WorkerController::class, 'report'])->name('workers.report');
    Route::get('workers-daily-tasks', [WorkerController::class, 'dailyTasks'])->name('workers.daily-tasks');
    Route::get('workers-harvest-tasks', [WorkerController::class, 'harvestTasks'])->name('workers.harvest-tasks');
    Route::post('tasks/{task}/approve', [WorkerController::class, 'approveTask'])->name('workers.approve-task');
    Route::post('tasks/{task}/reject', [WorkerController::class, 'rejectTask'])->name('workers.reject-task');
});

// Foreman routes
Route::middleware(['auth', 'user.active', 'role:foreman'])->prefix('foreman')->name('foreman.')->group(function () {
    Route::get('/', [ForemanDashboardController::class, 'index'])->name('index');
    Route::get('/dashboard-tasks', [ForemanDashboardController::class, 'tasks'])->name('dashboard.tasks');
    Route::get('/inventory', [ForemanDashboardController::class, 'inventory'])->name('inventory');
    Route::get('/dashboard-loans', [ForemanDashboardController::class, 'loans'])->name('dashboard.loans');
    
    // Task management routes
    Route::get('tasks/pdf', [ForemanTaskController::class, 'downloadPdf'])->name('tasks.pdf');
    Route::get('tasks/workers/list', [ForemanTaskController::class, 'getWorkers'])->name('tasks.workers.list');
    Route::get('tasks/crops/list', [ForemanTaskController::class, 'getCrops'])->name('tasks.crops.list');
    Route::post('tasks/{task}/approve', [ForemanTaskController::class, 'approve'])->name('tasks.approve');
    Route::post('tasks/{task}/invalidate', [ForemanTaskController::class, 'invalidate'])->name('tasks.invalidate');
    Route::post('tasks/{task}/complete', [ForemanTaskController::class, 'complete'])->name('tasks.complete');
    Route::resource('tasks', ForemanTaskController::class)->except(['show']);
    Route::get('tasks/{task}', [ForemanTaskController::class, 'show'])->name('tasks.show');
    
    // Worker management routes
    Route::get('workers/pdf', [ForemanWorkerController::class, 'downloadPdf'])->name('workers.pdf');
    Route::post('workers/{worker}/toggle-status', [ForemanWorkerController::class, 'toggleStatus'])->name('workers.toggle-status');
    Route::resource('workers', ForemanWorkerController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    
    // Tool management routes
    Route::get('tools/pdf', [ForemanToolController::class, 'downloadPdf'])->name('tools.pdf');
    Route::resource('tools', ForemanToolController::class);
    
    // Inventory management routes (tool entries and damage)
    Route::resource('tool-entries', ForemanToolEntryController::class);
    Route::resource('tool-damage', ForemanToolDamageController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    
    // Loan management routes (all admin functionalities)
    Route::resource('loans', ForemanLoanController::class)->only(['index', 'show', 'create', 'store', 'destroy']);
    Route::post('loans/{loan}/return', [ForemanLoanController::class, 'return'])->name('loans.return');
    Route::post('loans/{loan}/mark-lost', [ForemanLoanController::class, 'markAsLost'])->name('loans.mark-lost');
    Route::post('loans/{loan}/mark-damaged', [ForemanLoanController::class, 'markAsDamaged'])->name('loans.mark-damaged');
    
    // Loan approval routes
    Route::post('loans/{loan}/approve', [ForemanLoanController::class, 'approve'])->name('loans.approve');
    Route::post('loans/{loan}/reject', [ForemanLoanController::class, 'reject'])->name('loans.reject');
    Route::post('loans/{loan}/confirm-return', [ForemanLoanController::class, 'confirmReturn'])->name('loans.confirm-return');
    Route::post('loans/{loan}/process-approved', [ForemanLoanController::class, 'processApproved'])->name('loans.process-approved');
    Route::get('loans/pdf', [ForemanLoanController::class, 'downloadPdf'])->name('loans.pdf');
    
    // Supplies management routes (all admin functionalities)
    Route::get('supplies/pdf', [ForemanSupplyController::class, 'downloadPdf'])->name('supplies.pdf');
    Route::resource('supplies', ForemanSupplyController::class);
    Route::resource('supply-consumptions', ForemanSupplyConsumptionController::class);
    Route::resource('supply-movements', ForemanSupplyMovementController::class);
});

// Worker routes
Route::middleware(['auth', 'user.active', 'role:worker'])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/', [WorkerDashboardController::class, 'index'])->name('index');
    Route::get('/tasks', [WorkerDashboardController::class, 'tasks'])->name('tasks');
    Route::post('/tasks/{task}/complete', [WorkerDashboardController::class, 'completeTask'])->name('tasks.complete');
    Route::get('/tasks/pdf', [WorkerDashboardController::class, 'downloadTasksPdf'])->name('tasks.pdf');
    Route::get('/tools', [WorkerDashboardController::class, 'tools'])->name('tools');
    Route::post('/tools/request', [WorkerDashboardController::class, 'requestTool'])->name('tools.request');
    Route::post('/tools/{loan}/return', [WorkerDashboardController::class, 'returnTool'])->name('tools.return');
    Route::get('/tools/pdf', [WorkerDashboardController::class, 'downloadToolsPdf'])->name('tools.pdf');
    Route::get('/reports', [WorkerDashboardController::class, 'reports'])->name('reports');
    Route::post('/reports/generate', [WorkerDashboardController::class, 'generateReport'])->name('reports.generate');
    Route::get('/reports/pdf', [WorkerDashboardController::class, 'downloadReportsPdf'])->name('reports.pdf');
    
    // Worker loan management routes
    Route::resource('loans', WorkerLoanController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('loans/{loan}/return', [WorkerLoanController::class, 'returnForm'])->name('loans.return-form');
    Route::post('loans/{loan}/return', [WorkerLoanController::class, 'processReturn'])->name('loans.process-return');

    // Perfil del trabajador
    Route::get('profile', [WorkerProfileController::class, 'edit'])->name('profile');
    Route::put('profile', [WorkerProfileController::class, 'update'])->name('profile.update');
});
