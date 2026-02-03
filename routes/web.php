<?php

use App\Http\Controllers\Customer\KasirController;
use App\Http\Controllers\Kitchen\DapurController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ==========================================
// 🏠 LANDING PAGE & CUSTOMER FRONTEND
// ==========================================
Route::get('/', function () {
    return view('home'); // Landing Page
})->name('home');


// ==========================================
// 📱 KASIR SELF-SERVICE (TABLET)
// ==========================================
// Route::prefix('kasir')->name('kasir.')->group(function () {
//     // View Interface
//     Route::get('/', [KasirController::class, 'index'])->name('index');
// });


// ==========================================
// 🍳 KITCHEN DISPLAY SYSTEM (KDS)
// ==========================================
Route::prefix('dapur')->name('dapur.')->group(function () {
    // View Interface
    Route::get('/', [DapurController::class, 'index'])->name('index');
});


// ==========================================
// 🔌 API ENDPOINTS (Internal AJAX)
// ==========================================
// Note: Usually placed in api.php, but placed here for simplicity 
// and session sharing if needed (e.g. CSRF protection)
Route::prefix('api')->name('api.')->group(function () {

    // --- Public / Customer APIs ---
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [KasirController::class, 'getProducts'])->name('index');
    });

    Route::prefix('toppings')->name('toppings.')->group(function () {
        Route::get('/', [KasirController::class, 'getToppings'])->name('index');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        // Submit New Order
        Route::post('/', [KasirController::class, 'storeOrder'])->name('store');

        // --- Kitchen APIs ---
        // Get Pending Orders
        Route::get('/pending', [DapurController::class, 'getPendingOrders'])->name('pending');

        // Update Order Status
        Route::patch('/{id}/status', [DapurController::class, 'updateStatus'])->name('status.update');
    });

});

// ==========================================
// 📦 INVENTORY MANAGEMENT
// ==========================================
Route::prefix('inventory')->name('inventory.')->group(function () {
    // View
    Route::get('/', [App\Http\Controllers\Inventory\InventoryController::class, 'index'])->name('index');

    // API-like Routes for CRUD (using Web middleware for Session/CSRF)
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/{id}', [App\Http\Controllers\Inventory\InventoryController::class, 'getProduct'])->name('show');
        Route::post('/', [App\Http\Controllers\Inventory\InventoryController::class, 'storeProduct'])->name('store');
        Route::post('/{id}', [App\Http\Controllers\Inventory\InventoryController::class, 'updateProduct'])->name('update'); // Using POST for file upload spoofing
        Route::delete('/{id}', [App\Http\Controllers\Inventory\InventoryController::class, 'destroyProduct'])->name('destroy');
    });

    Route::prefix('toppings')->name('toppings.')->group(function () {
        Route::get('/{id}', [App\Http\Controllers\Inventory\InventoryController::class, 'getTopping'])->name('show');
        Route::post('/', [App\Http\Controllers\Inventory\InventoryController::class, 'storeTopping'])->name('store');
        Route::patch('/{id}', [App\Http\Controllers\Inventory\InventoryController::class, 'updateTopping'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Inventory\InventoryController::class, 'destroyTopping'])->name('destroy');
    });
});

