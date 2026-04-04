<?php

use App\Http\Controllers\Api\V1\ApplicationController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FinanceController;
use App\Http\Controllers\Api\V1\HotelController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\KycController;
use App\Http\Controllers\Api\V1\LicenseController;
use App\Http\Controllers\Api\V1\PersonnelController;
use App\Http\Controllers\Api\V1\SalesController;
use App\Http\Controllers\Api\V1\SupplyController;
use App\Http\Controllers\Api\V1\DisasterRecoveryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Api V1 is live';
});

Route::controller(LicenseController::class)->group(function () {
    Route::get('/license/status', 'status');
});

Route::prefix('dr')->controller(DisasterRecoveryController::class)->group(function () {
    Route::post('/pairing', 'pairing');
    Route::get('/status', 'status');
    Route::get('/bundles/{bundleUuid}', 'download');
    Route::post('/heartbeat', 'heartbeat');
});

Route::middleware('validate.license')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/license_key', 'validateLicenseKey');
        Route::post('/logout', 'logout')->middleware('auth:sanctum');
        Route::get('/check-auth', 'checkAuth')->middleware('auth:sanctum');
        Route::post('/forgot-password', 'forgotPassword');
        Route::post('/reset-password', 'resetPassword');
        Route::post('/update-password', 'updatePassword')->middleware('auth:sanctum');
        Route::get('/user-profile', 'userProfile')->middleware('auth:sanctum');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::controller(InventoryController::class)->group(function () {
            Route::get('/getItem', 'getInventoryItem');
            Route::get('/get_inventory', 'getInventoryItem');
            Route::post('/addItem', 'addInventoryItem');
            Route::patch('/updateItem', 'updateInventoryItem');
            Route::delete('/deleteItem', 'deleteInventoryItem');
        });

        Route::controller(SalesController::class)->group(function () {
            Route::post('/checkout', 'checkout');
            Route::get('/generate_pos_code', 'generatePosCode');
            Route::get('/getSales', 'getSales');
            Route::get('/receipts/{receiptNumber}', 'getSalesByReceipt');
        });

        Route::controller(HotelController::class)->group(function () {
            Route::get('/get_rooms', 'getAllRooms');
            Route::get('/get_room_satistics', 'getRoomStatistics');
            Route::post('/book_room', 'bookRoom');
            Route::get('/get_booked_date/{roomId}', 'getBookedDates');
        });
    });

    Route::controller(FinanceController::class)->group(function () {
        Route::post('/expenses', 'Expenses');
        Route::get('/dailysummary', 'DailyFinance');
    });

    Route::controller(PersonnelController::class)->group(function () {
        //staff
        Route::post('/addStaff', 'addStaff');
        Route::get('/staffData', 'staffData');
        Route::post('/updateStaff', 'updateStaff');
        Route::post('/deleteStaff', 'deleteStaff');
        //suppliers
        Route::post('/addOrder', 'addOrder');
        Route::get('/supplierData', 'supplierData');
        Route::post('/updateSupplier', 'updateSupplier');
        Route::post('/deleteSupplier', 'deleteSupplier');
        //purchases
        Route::get('/getPurchase', 'getPurchase');
    });

    Route::controller(SupplyController::class)->group(function () {
        //suppliers
        Route::post('/addOrder', 'addOrder');
        Route::get('/supplierData', 'supplierData');
        Route::post('/updateSupplier', 'updateSupplier');
        Route::post('/deleteSupplier', 'deleteSupplier');
        //purchases
        Route::get('/getPurchase', 'getPurchase');
    });
});
