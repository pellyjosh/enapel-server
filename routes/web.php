<?php

use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SupplyController;
use Illuminate\Support\Facades\Route;

// ─── Public routes ────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('auth.login');
})->name('login');

// License invalid / not configured screen
Route::get('/license-required', function () {
    return view('license.required');
})->name('license.required');

Route::post('/license-required/configure', function (\Illuminate\Http\Request $request) {
    $request->validate(['license_key' => 'required|string|min:10']);

    // Write the key into .env
    $key   = strtoupper(trim($request->license_key));
    $terminalId = config('license.terminal_id') ?: (string) \Illuminate\Support\Str::uuid();
    $envPath = base_path('.env');
    $envContent = file_get_contents($envPath);

    if (str_contains($envContent, 'LICENSE_KEY=')) {
        $envContent = preg_replace('/^LICENSE_KEY=.*/m', "LICENSE_KEY={$key}", $envContent);
    } else {
        $envContent .= "\nLICENSE_KEY={$key}";
    }

    if (str_contains($envContent, 'TERMINAL_IDENTIFIER=')) {
        $envContent = preg_replace('/^TERMINAL_IDENTIFIER=.*/m', "TERMINAL_IDENTIFIER={$terminalId}", $envContent);
    } else {
        $envContent .= "\nTERMINAL_IDENTIFIER={$terminalId}";
    }

    file_put_contents($envPath, $envContent);

    config([
        'license.key' => $key,
        'license.terminal_id' => $terminalId,
    ]);

    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Cache::forget('enapel_license_payload');

    $payload = app(\App\Services\LicenseService::class)->refresh();

    if (($payload['valid'] ?? false) === true) {
        return redirect()->route('login')->with('status', 'License activated successfully.');
    }

    return redirect()->route('license.required')->with([
        'license_error' => true,
        'license_reason' => $payload['reason'] ?? 'unknown',
        'license_message' => $payload['message'] ?? 'License validation failed.',
    ]);
})->name('license.configure');

// ─── Authenticated + license-validated routes ──────────────────────────────
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'validate.license'])->name('dashboard');

Route::middleware(['auth', 'validate.license'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/inventory', [InventoryController::class, 'showInventory'])->name('inventory.show');
    Route::post('/inventory/store', [InventoryController::class, 'storeInventory'])->name('inventory.store');
    Route::put('/inventory/update/{id}', [InventoryController::class, 'updateInventory'])->name('inventory.update');
    Route::post('/inventory/delete/{id}', [InventoryController::class, 'deleteInventory'])->name('inventory.delete');

    Route::get('/staff', [PersonnelController::class, 'showStaff'])->name('staff');
    Route::post('/staff/create', [PersonnelController::class, 'createStaff'])->name('staff.store');
    Route::put('/staff/update/{id}', [PersonnelController::class, 'updateStaff'])->name('staff.update');
    Route::post('/staff/delete/{id}', [PersonnelController::class, 'deleteStaff'])->name('staff.delete');

    Route::get('/supplier', [SupplyController::class, 'supplierData'])->name('supplier.show');
    Route::post('/supplier/create', [SupplyController::class, 'addOrder'])->name('neworder');
    Route::put('/supplier/update/{id}', [SupplyController::class, 'updateSupplier'])->name('supplier.update');
    Route::post('/supplier/delete/{id}', [SupplyController::class, 'deleteSupplier'])->name('supplier.delete');

    Route::post('/expenses', [FinanceController::class, 'Expenses'])->name('expense.create');
    Route::get('/finance', [FinanceController::class, 'DailyFinance'])->name('finance');

    Route::get('/sales', [SalesController::class, 'getSales'])->name('sales');

    Route::get('/purchases', [SupplyController::class, 'getPurchase'])->name('purchases');

    Route::get('/stock', [InventoryController::class, 'showStock'])->name('stock');

    Route::get('/orderform', function () {
        return view('user.form.purchaseform');
    })->name('order');

    Route::get('/expenses', function () {
        return view('user.form.expenseform');
    })->name('expenses');

    Route::get('/devices', function () {
        return view('user.devices.device');
    })->name('devices');

    Route::get('/user_activity', function () {
        return view('user.reports.useractivity');
    })->name('user');

    Route::prefix('sync')->group(function () {
        Route::get('/sync', function () {
            return view('sync.sync');
        })->name('sync');
        Route::get('/finance', [FinanceController::class, 'syncFinance'])->name('sync_finance');
        Route::get('/purchase', [SupplyController::class, 'syncPurchase'])->name('sync_purchases');
        Route::get('/sales', [SalesController::class, 'syncSales'])->name('sync_sales');

        Route::get('/stock', [InventoryController::class, 'syncStock'])->name('sync_stock');

        Route::get('/users', function () {
            return view('sync.Sync_useractivity');
        })->name('sync_users');
    });
});




require __DIR__ . '/auth.php';
