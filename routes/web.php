<?php

use App\Support\RuntimeEnvironment;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\DisasterRecoveryController;
use App\Http\Controllers\PublicDisasterRecoveryController;
use Illuminate\Support\Facades\Route;

// ─── Public routes ────────────────────────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('login');
});

// License invalid / not configured screen
Route::get('/license-required', function () {
    return \Inertia\Inertia::render('LicenseRequired');
})->name('license.required');

Route::post('/license-required/configure', function (\Illuminate\Http\Request $request) {
    $request->validate(['license_key' => 'required|string|min:10']);

    // Write the key into .env
    $key   = strtoupper(trim($request->license_key));
    $terminalId = config('license.terminal_id') ?: (string) \Illuminate\Support\Str::uuid();
    $envPath = RuntimeEnvironment::environmentFilePath();
    $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';

    if ($envContent === false) {
        $envContent = '';
    }

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

Route::get('/disaster-recovery/restore', [PublicDisasterRecoveryController::class, 'create'])->name('disaster-recovery.restore.create');
Route::post('/disaster-recovery/restore', [PublicDisasterRecoveryController::class, 'store'])->name('disaster-recovery.restore.store');

// ─── Authenticated + license-validated routes ──────────────────────────────
Route::get('/dashboard', function () {
    $businessTypes = app(\App\Services\LicenseService::class)->get('tenant.business_types', []);

    // Some minor normalization since the cloud sends 'supermarket' but the dashboard checks 'supermart'
    if (in_array('supermarket', $businessTypes) && !in_array('supermart', $businessTypes)) {
        $businessTypes[] = 'supermart';
    }

    $metrics = app(\App\Services\DashboardService::class)->getMetrics($businessTypes);

    return \Inertia\Inertia::render('Dashboard', [
        'metrics' => $metrics,
        // Don't pass enabledModules here, let it fall back to HandleInertiaRequests 
        // OR pass the newly normalized businessTypes
        'enabledModules' => $businessTypes,
    ]);
})->middleware(['auth', 'validate.license'])->name('dashboard');

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
    Route::post('/checkout', [SalesController::class, 'checkout'])->name('checkout');

    Route::get('/purchases', [SupplyController::class, 'getPurchase'])->name('purchases');

    Route::get('/stock', [InventoryController::class, 'showStock'])->name('stock');

    Route::get('/orderform', function () {
        return view('user.form.purchaseform');
    })->name('order');

    Route::get('/expenses', function () {
        return view('user.form.expenseform');
    })->name('expenses');

    // Global Settings -> Terminals
    Route::get('/global/settings/terminals', [\App\Http\Controllers\TerminalController::class, 'index'])->name('global.settings.terminals');
    Route::post('/global/settings/terminals/{device}/toggle', [\App\Http\Controllers\TerminalController::class, 'toggleStatus'])->name('global.settings.terminals.toggle');
    Route::get('/global/settings/disaster-recovery', [DisasterRecoveryController::class, 'index'])->name('global.settings.disaster-recovery');
    Route::put('/global/settings/disaster-recovery', [DisasterRecoveryController::class, 'update'])->name('global.settings.disaster-recovery.update');
    Route::post('/global/settings/disaster-recovery/snapshot', [DisasterRecoveryController::class, 'snapshot'])->name('global.settings.disaster-recovery.snapshot');
    Route::post('/global/settings/disaster-recovery/pick-folder', [DisasterRecoveryController::class, 'pickFolder'])->name('global.settings.disaster-recovery.pick-folder');
    Route::post('/global/settings/disaster-recovery/pairing-token', [DisasterRecoveryController::class, 'generatePairingToken'])->name('global.settings.disaster-recovery.pairing-token');
    Route::post('/global/settings/disaster-recovery/pair', [DisasterRecoveryController::class, 'pair'])->name('global.settings.disaster-recovery.pair');
    Route::post('/global/settings/disaster-recovery/promote', [DisasterRecoveryController::class, 'promote'])->name('global.settings.disaster-recovery.promote');

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

    Route::get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/download', [App\Http\Controllers\ActivityLogController::class, 'download'])->name('activity-logs.download');
    // Pharmacy Module
    Route::get('/pharmacy/dashboard', [\App\Http\Controllers\PharmacyController::class, 'dashboard'])->name('pharmacy.dashboard');
    Route::get('/pharmacy/catalog', [\App\Http\Controllers\PharmacyController::class, 'catalog'])->name('pharmacy.catalog');
    Route::post('/pharmacy/catalog', [\App\Http\Controllers\PharmacyController::class, 'storeDrug'])->name('pharmacy.catalog.store');
    Route::put('/pharmacy/catalog/{id}', [\App\Http\Controllers\PharmacyController::class, 'updateDrug'])->name('pharmacy.catalog.update');
    Route::delete('/pharmacy/catalog/{id}', [\App\Http\Controllers\PharmacyController::class, 'deleteDrug'])->name('pharmacy.catalog.delete');
    Route::get('/pharmacy/pos', [\App\Http\Controllers\PharmacyController::class, 'pos'])->name('pharmacy.pos');
    Route::get('/pharmacy/prescriptions', [\App\Http\Controllers\PharmacyController::class, 'prescriptions'])->name('pharmacy.prescriptions');
    Route::post('/pharmacy/prescriptions', [\App\Http\Controllers\PharmacyController::class, 'storePrescription'])->name('pharmacy.prescriptions.store');
    Route::post('/pharmacy/prescriptions/{id}/dispense', [\App\Http\Controllers\PharmacyController::class, 'dispensePrescription'])->name('pharmacy.prescriptions.dispense');
    Route::put('/pharmacy/prescriptions/{id}', [\App\Http\Controllers\PharmacyController::class, 'updatePrescription'])->name('pharmacy.prescriptions.update');
    Route::delete('/pharmacy/prescriptions/{id}', [\App\Http\Controllers\PharmacyController::class, 'deletePrescription'])->name('pharmacy.prescriptions.delete');
    Route::get('/pharmacy/sales', [\App\Http\Controllers\PharmacyController::class, 'sales'])->name('pharmacy.sales');
    Route::get('/pharmacy/stock', [\App\Http\Controllers\PharmacyController::class, 'stock'])->name('pharmacy.stock');
    Route::put('/pharmacy/stock/{id}', [\App\Http\Controllers\PharmacyController::class, 'updateStock'])->name('pharmacy.stock.update');
    Route::get('/pharmacy/alerts', [\App\Http\Controllers\PharmacyController::class, 'alerts'])->name('pharmacy.alerts');
    Route::get('/pharmacy/suppliers', [\App\Http\Controllers\PharmacyController::class, 'suppliers'])->name('pharmacy.suppliers');
    Route::get('/pharmacy/orders', [\App\Http\Controllers\PharmacyController::class, 'orders'])->name('pharmacy.orders');

    // Supermart Module
    Route::get('/supermart/dashboard', [\App\Http\Controllers\SupermartController::class, 'dashboard'])->name('supermart.dashboard');
    Route::get('/supermart/catalog', [\App\Http\Controllers\SupermartController::class, 'catalog'])->name('supermart.catalog');
    Route::post('/supermart/catalog', [\App\Http\Controllers\SupermartController::class, 'storeProduct'])->name('supermart.catalog.store');
    Route::put('/supermart/catalog/{id}', [\App\Http\Controllers\SupermartController::class, 'updateProduct'])->name('supermart.catalog.update');
    Route::delete('/supermart/catalog/{id}', [\App\Http\Controllers\SupermartController::class, 'deleteProduct'])->name('supermart.catalog.delete');
    Route::get('/supermart/pos', [\App\Http\Controllers\SupermartController::class, 'pos'])->name('supermart.pos');
    Route::get('/supermart/orders', [\App\Http\Controllers\SupermartController::class, 'orders'])->name('supermart.orders');
    Route::get('/supermart/categories', [\App\Http\Controllers\SupermartController::class, 'categories'])->name('supermart.categories');
    Route::post('/supermart/categories', [\App\Http\Controllers\SupermartController::class, 'storeCategory'])->name('supermart.categories.store');
    Route::put('/supermart/categories/{category}', [\App\Http\Controllers\SupermartController::class, 'updateCategory'])->name('supermart.categories.update');
    Route::delete('/supermart/categories/{category}', [\App\Http\Controllers\SupermartController::class, 'deleteCategory'])->name('supermart.categories.delete');
    Route::get('/supermart/stock', [\App\Http\Controllers\SupermartController::class, 'stock'])->name('supermart.stock');
    Route::get('/supermart/stock/{id}/history', [\App\Http\Controllers\SupermartController::class, 'stockHistory'])->name('supermart.stock.history');
    Route::put('/supermart/stock/{id}', [\App\Http\Controllers\SupermartController::class, 'updateStock'])->name('supermart.stock.update');
    Route::get('/supermart/suppliers', [\App\Http\Controllers\SupermartController::class, 'suppliers'])->name('supermart.suppliers');
    Route::post('/supermart/suppliers', [\App\Http\Controllers\SupermartController::class, 'storeSupplier'])->name('supermart.suppliers.store');
    Route::put('/supermart/suppliers/{id}', [\App\Http\Controllers\SupermartController::class, 'updateSupplier'])->name('supermart.suppliers.update');
    Route::delete('/supermart/suppliers/{id}', [\App\Http\Controllers\SupermartController::class, 'deleteSupplier'])->name('supermart.suppliers.delete');
    Route::get('/supermart/invoices', [\App\Http\Controllers\SupermartController::class, 'invoices'])->name('supermart.invoices');
    Route::get('/supermart/reports', [\App\Http\Controllers\SupermartController::class, 'reports'])->name('supermart.reports');

    // Hotel Module
    Route::get('/hotel/dashboard', [\App\Http\Controllers\HotelController::class, 'dashboard'])->name('hotel.dashboard');
    Route::get('/hotel/bookings', [\App\Http\Controllers\HotelController::class, 'bookings'])->name('hotel.bookings');
    Route::post('/hotel/bookings', [\App\Http\Controllers\HotelController::class, 'storeBooking'])->name('hotel.bookings.store');
    Route::get('/hotel/guests', [\App\Http\Controllers\HotelController::class, 'guests'])->name('hotel.guests');
    Route::post('/hotel/guests', [\App\Http\Controllers\HotelController::class, 'storeGuest'])->name('hotel.guests.store');
    Route::get('/hotel/rooms', [\App\Http\Controllers\HotelController::class, 'rooms'])->name('hotel.rooms');
    Route::post('/hotel/rooms', [\App\Http\Controllers\HotelController::class, 'storeRoom'])->name('hotel.rooms.store');
    Route::get('/hotel/housekeeping', [\App\Http\Controllers\HotelController::class, 'housekeeping'])->name('hotel.housekeeping');
    Route::post('/hotel/housekeeping/{id}', [\App\Http\Controllers\HotelController::class, 'updateHousekeeping'])->name('hotel.housekeeping.update');
    Route::get('/hotel/roomservice', [\App\Http\Controllers\HotelController::class, 'roomService'])->name('hotel.roomservice');
    Route::post('/hotel/roomservice', [\App\Http\Controllers\HotelController::class, 'storeRoomService'])->name('hotel.roomservice.store');
    Route::get('/hotel/invoices', [\App\Http\Controllers\HotelController::class, 'invoices'])->name('hotel.invoices');
    Route::get('/hotel/reports', [\App\Http\Controllers\HotelController::class, 'reports'])->name('hotel.reports');
    Route::get('/hotel/settings', [\App\Http\Controllers\HotelController::class, 'settings'])->name('hotel.settings');
});

require __DIR__ . '/auth.php';
