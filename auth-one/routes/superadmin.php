<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\SuperadminDashboardController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Superadmin\SupplierController;
use App\Http\Controllers\Superadmin\DepoListController;
use App\Http\Controllers\Superadmin\RawMaterialController;
use App\Http\Controllers\Superadmin\RawMaterialPurchaseController;
use App\Http\Controllers\Superadmin\RawMaterialStockOutController;
use App\Http\Controllers\Superadmin\WastageController;
use App\Http\Controllers\Superadmin\ProductController;
use App\Http\Controllers\Superadmin\ProductReceiveController;
use App\Http\Controllers\Superadmin\SalesInvoiceController;

/*
|--------------------------------------------------------------------------
| Superadmin Routes
|--------------------------------------------------------------------------
| Prefix: superadmin
| Middleware: auth, role:superadmin
|--------------------------------------------------------------------------
*/
Route::prefix('superadmin')->middleware(['auth', 'role:superadmin'])->group(function () {

    // Dashboard Route
    Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])
        ->name('superadmin.dashboard');

    // User Management (CRUD)
    Route::resource('/users', UserController::class)
        ->names('superadmin.users');

    // ------------------------------------------
    // RAW MATERIAL MANAGEMENT
    // ------------------------------------------
    Route::resource('/raw-materials', RawMaterialController::class)
        ->names('superadmin.raw-materials');

    Route::resource('/raw-material-purchases', RawMaterialPurchaseController::class)
        ->names('superadmin.raw-material-purchases');

    Route::resource('/raw-material-stock-out', RawMaterialStockOutController::class)
        ->names('superadmin.raw-material-stock-out');

    // FIX 1: Raw Material Stock Out Batch Loading API Route (404 Error Fix)
    Route::get('/api/raw-material-stock/batches/{rawMaterialId}', [RawMaterialStockOutController::class, 'getStockBatches'])
        ->name('superadmin.raw-material-stock.api.batches');

    // FIX 2: Missing Route for Stock Report (stockIndex() Error Fix)
    Route::get('/raw-material-stock', [RawMaterialController::class, 'stockIndex'])
        ->name('superadmin.raw-material-stock.index');
        
    // 4. Wastage Management (CRUD)
    Route::resource('/wastage', WastageController::class)
        ->names('superadmin.wastage');

    // ✅ FIX 3: Missing API Route for Wastage Batch Loading (RouteNotFoundException Fix)
    Route::get('/api/wastage/batches/{rawMaterialId}', [WastageController::class, 'getRawMaterialBatches'])
        ->name('superadmin.api.wastage.batches');
        
    

    // ------------------------------------------
    // PRODUCT & STOCK MANAGEMENT
    // ------------------------------------------
    
    // 1. Product Management (CRUD)
    Route::resource('/products', ProductController::class)
        ->names('superadmin.products');

    // 2. Product Receive/Purchase (CRUD)
    Route::resource('/product-receives', ProductReceiveController::class)
        ->names('superadmin.product-receives');
           // ✅ AJAX Route: নতুন Item Row রেন্ডার করার জন্য
    Route::get('/product-receives/get-item-row', [ProductReceiveController::class, 'getItemRow'])
        ->name('superadmin.product-receives.get-item-row');
         // ✅ API Route: Product Rates
    Route::get('/api/products/rates/{productId}', [ProductController::class, 'getRates'])
        ->name('superadmin.api.products.rates');


// 3. SALES MANAGEMENT (FINAL FIX FOR ROUTING)
Route::prefix('sales')->name('superadmin.sales.')->group(function () {
    
    // Sales Invoice List (GET: /superadmin/sales)
    Route::get('/', [SalesInvoiceController::class, 'index'])->name('index');       
    
    // Create Form (GET: /superadmin/sales/create)
    Route::get('/create', [SalesInvoiceController::class, 'create'])->name('create'); 
    
    // Store Invoice (POST: /superadmin/sales)
    Route::post('/', [SalesInvoiceController::class, 'store'])->name('store');    
    Route::get('/{id}', [SalesInvoiceController::class, 'show'])->name('show');
   

    // API: Load Product Stock Batches (GET: /superadmin/sales/api/product-stock/batches/{productId})
    Route::get('/api/product-stock/batches/{productId}', [SalesInvoiceController::class, 'getProductBatches'])->name('api.product-stock.batches');
});

    // 4. Return Management (Future)
    // 5. Wastage Management (Future)

    // ------------------------------------------
    // SETTINGS & MASTER DATA
    // ------------------------------------------

    // Supplier Management (CRUD)
    Route::resource('/suppliers', SupplierController::class)
        ->names('superadmin.suppliers');

    // Depo Management (List only)
    Route::get('/depo', [DepoListController::class, 'index'])
        ->name('superadmin.depo.index');

    // Distributor Management (List only)
    Route::get('/distributor', [DepoListController::class, 'index'])
        ->name('superadmin.distributor.index');
});