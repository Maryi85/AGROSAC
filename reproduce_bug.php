<?php

use App\Models\Supply;
use App\Models\SupplyMovement;
use App\Models\SupplyConsumption;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test the logic
DB::beginTransaction();

try {
    // 1. Create a test supply
    $supply = Supply::create([
        'name' => 'Test Supply ' . time(),
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
        'created_by' => 1 // Assuming admin id 1 exists
    ]);

    $supply->updateStock();
    echo "Stock after entry: " . $supply->current_stock . "\n";

    // 3. Record consumption
    SupplyConsumption::create([
        'supply_id' => $supply->id,
        'qty' => 10,
        'total_cost' => 100,
        'used_at' => now(),
    ]);

    $supply->updateStock();
    echo "Stock after consumption: " . $supply->current_stock . "\n";

    if ($supply->current_stock == 90) {
        echo "SUCCESS: Stock updated correctly to 90.\n";
    }
    else {
        echo "FAILURE: Stock is " . $supply->current_stock . ", expected 90.\n";
    }

}
catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
finally {
    DB::rollBack();
}
