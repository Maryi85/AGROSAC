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
use App\Http\Controllers\Admin\LedgerController;
use App\Http\Controllers\Admin\ForemanController;
use App\Http\Controllers\ForemanController as ForemanDashboardController;
use App\Http\Controllers\ForemanTaskController;
use App\Http\Controllers\ForemanWorkerController;
use App\Http\Controllers\ForemanToolController;
use App\Http\Controllers\ForemanLoanController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\WorkerController as WorkerDashboardController;

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
    Route::resource('crops', CropController::class);
    Route::post('crops/{crop}/disable', [CropController::class, 'disable'])->name('crops.disable');
    Route::post('crops/{crop}/enable', [CropController::class, 'enable'])->name('crops.enable');
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/admin-approve', [TaskController::class, 'approve'])->name('tasks.approve');
    Route::post('tasks/{task}/invalidate', [TaskController::class, 'invalidate'])->name('tasks.invalidate');
    Route::get('tasks/workers/list', [TaskController::class, 'getWorkers'])->name('tasks.workers.list');
    Route::get('tasks/crops/list', [TaskController::class, 'getCrops'])->name('tasks.crops.list');
    Route::resource('tools', ToolController::class);
    Route::resource('loans', LoanController::class);
    Route::post('loans/{loan}/return', [LoanController::class, 'return'])->name('loans.return');
    Route::post('loans/{loan}/mark-lost', [LoanController::class, 'markAsLost'])->name('loans.mark-lost');
    Route::post('loans/{loan}/mark-damaged', [LoanController::class, 'markAsDamaged'])->name('loans.mark-damaged');
    Route::resource('supplies', SupplyController::class);
    Route::resource('supply-consumptions', SupplyConsumptionController::class);
    Route::resource('ledger', LedgerController::class);
    Route::get('ledger-dashboard', [LedgerController::class, 'dashboard'])->name('ledger.dashboard');
    Route::resource('foremen', ForemanController::class)->middleware('ajax.validation');
    Route::post('foremen/{foreman}/toggle-status', [ForemanController::class, 'toggleStatus'])->name('foremen.toggle-status')->middleware('ajax.validation');
    Route::resource('workers', WorkerController::class);
    Route::post('workers/{worker}/reset-password', [WorkerController::class, 'resetPassword'])->name('workers.reset-password');
    Route::post('workers/{worker}/toggle-status', [WorkerController::class, 'toggleStatus'])->name('workers.toggle-status');
    Route::get('workers/{worker}/tasks', [WorkerController::class, 'tasks'])->name('workers.tasks');
    Route::get('workers-daily-tasks', [WorkerController::class, 'dailyTasks'])->name('workers.daily-tasks');
    Route::get('workers-harvest-tasks', [WorkerController::class, 'harvestTasks'])->name('workers.harvest-tasks');
    Route::post('tasks/{task}/approve', [WorkerController::class, 'approveTask'])->name('workers.approve-task');
    Route::post('tasks/{task}/reject', [WorkerController::class, 'rejectTask'])->name('workers.reject-task');
});

// Foreman routes
Route::middleware(['auth', 'role:foreman'])->prefix('foreman')->name('foreman.')->group(function () {
    Route::get('/', [ForemanDashboardController::class, 'index'])->name('index');
    Route::get('/tasks', [ForemanDashboardController::class, 'tasks'])->name('tasks');
    Route::get('/inventory', [ForemanDashboardController::class, 'inventory'])->name('inventory');
    Route::get('/loans', [ForemanDashboardController::class, 'loans'])->name('loans');
    
    // Task management routes
    Route::resource('tasks', ForemanTaskController::class)->except(['show']);
    Route::get('tasks/{task}', [ForemanTaskController::class, 'show'])->name('tasks.show');
    Route::post('tasks/{task}/approve', [ForemanTaskController::class, 'approve'])->name('tasks.approve');
    Route::post('tasks/{task}/invalidate', [ForemanTaskController::class, 'invalidate'])->name('tasks.invalidate');
    Route::post('tasks/{task}/complete', [ForemanTaskController::class, 'complete'])->name('tasks.complete');
    Route::get('tasks/workers/list', [ForemanTaskController::class, 'getWorkers'])->name('tasks.workers.list');
    Route::get('tasks/crops/list', [ForemanTaskController::class, 'getCrops'])->name('tasks.crops.list');
    
    // Worker management routes
    Route::resource('workers', ForemanWorkerController::class)->only(['index', 'show', 'edit', 'update']);
    Route::post('workers/{worker}/toggle-status', [ForemanWorkerController::class, 'toggleStatus'])->name('workers.toggle-status');
    
    // Tool management routes
    Route::resource('tools', ForemanToolController::class);
    
    // Loan management routes
    Route::resource('loans', ForemanLoanController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('loans/{loan}/return', [ForemanLoanController::class, 'return'])->name('loans.return');
    Route::post('loans/{loan}/mark-lost', [ForemanLoanController::class, 'markAsLost'])->name('loans.mark-lost');
    Route::post('loans/{loan}/mark-damaged', [ForemanLoanController::class, 'markAsDamaged'])->name('loans.mark-damaged');
    Route::delete('loans/{loan}', [ForemanLoanController::class, 'destroy'])->name('loans.destroy');
});

// Worker routes
Route::middleware(['auth', 'role:worker'])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/', [WorkerDashboardController::class, 'index'])->name('index');
    Route::get('/tasks', [WorkerDashboardController::class, 'tasks'])->name('tasks');
    Route::post('/tasks/{task}/complete', [WorkerDashboardController::class, 'completeTask'])->name('tasks.complete');
    Route::get('/tools', [WorkerDashboardController::class, 'tools'])->name('tools');
    Route::post('/tools/request', [WorkerDashboardController::class, 'requestTool'])->name('tools.request');
    Route::post('/tools/{loan}/return', [WorkerDashboardController::class, 'returnTool'])->name('tools.return');
    Route::get('/reports', [WorkerDashboardController::class, 'reports'])->name('reports');
    Route::post('/reports/generate', [WorkerDashboardController::class, 'generateReport'])->name('reports.generate');
});
