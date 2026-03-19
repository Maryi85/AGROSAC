<?php

use App\Models\Supply;
use App\Models\SupplyMovement;
use App\Models\SupplyConsumption;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test the fixes
DB::beginTransaction();

try {
    $taskService = app(TaskService::class);

    // 1. Create a test supply
    $supply = Supply::create([
        'name' => 'Test Fix ' . time(),
        'unit' => 'kg',
        'unit_cost' => 10,
        'status' => 'active',
    ]);

    echo "Initial stock: " . $supply->current_stock . "\n";

    // 2. Add stock via movement
    SupplyMovement::create([
        'supply_id' => $supply->id,
        'type' => 'entry',
        'quantity' => 100,
        'unit_cost' => 10,
        'total_cost' => 1000,
        'movement_date' => now(),
        'created_by' => 1
    ]);

    $supply->updateStock();
    echo "Stock after entry: " . $supply->current_stock . "\n";

    // 3. Create a task with supply data
    $task = Task::create([
        'type' => 'daily',
        'description' => 'Test Task',
        'status' => 'pending',
        'assigned_to' => 1, // Assumed admin/foreman id
        'scheduled_for' => now(),
        'supplies_data' => [
            [
                'supply_id' => $supply->id,
                'quantity' => 20
            ]
        ]
    ]);

    // 4. Complete the task
    echo "Completing task...\n";
    $taskService->complete($task, []);

    $supply->refresh();
    echo "Stock after task completion: " . $supply->current_stock . "\n";

    if ($supply->current_stock == 80) {
        echo "SUCCESS: Stock updated correctly to 80 (only discounted once).\n";
    }
    else {
        echo "FAILURE: Stock is " . $supply->current_stock . ", expected 80. If it's 60, double-discounting is still happening.\n";
    }

    // 5. Verify the movement was NOT created (now we only use consumption for tasks)
    $movementCount = SupplyMovement::where('task_id', $task->id)->count();
    $consumptionCount = SupplyConsumption::where('task_id', $task->id)->count();

    echo "Movements for task: " . $movementCount . " (Expected 0)\n";
    echo "Consumptions for task: " . $consumptionCount . " (Expected 1)\n";

    if ($movementCount == 0 && $consumptionCount == 1) {
        echo "SUCCESS: Only consumption was recorded, no redundant movement.\n";
    }
    else {
        echo "FAILURE: Movement or Consumption count is incorrect.\n";
    }

}
catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
finally {
    DB::rollBack();
}
