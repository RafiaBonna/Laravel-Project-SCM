<?php

use Illuminate\Support\Facades\Route;

// Superadmin Controllers
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
| SUPERADMIN ROUTES
|--------------------------------------------------------------------------
| Prefix: superadmin
| Middleware: auth + role:superadmin
|--------------------------------------------------------------------------
*/

Route::prefix('superadmin')->middleware(['auth', 'role:superadmin'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])
        ->name('superadmin.dashboard');

    // USER MANAGEMENT
    Route::resource('/users', UserController::class)
        ->names('superadmin.users');

    // --------------------------------------------------
    // RAW MATERIAL MANAGEMENT
    // --------------------------------------------------
    Route::resource('/raw-materials', RawMaterialController::class)
        ->names('superadmin.raw-materials');

    Route::resource('/raw-material-purchases', RawMaterialPurchaseController::class)
        ->names('superadmin.raw-material-purchases');

    // RAW MATERIAL STOCK OUT CRUD
    Route::resource('/raw-material-stock-out', RawMaterialStockOutController::class)
        ->names('superadmin.raw-material-stock-out');

    // API: Get Batches for Raw Material Stock Out
    Route::get('/api/raw-material-stock/batches/{rawMaterialId}', 
        [RawMaterialStockOutController::class, 'getStockBatches']
    )->name('superadmin.raw-material-stock.api.batches');

    // 🌟 RAW MATERIAL STOCK REPORT
    Route::get('/raw-material-stock-out/report', 
        [RawMaterialStockOutController::class, 'stockReport']
    )->name('superadmin.raw-material-stock-out.report');

    // STOCK INDEX for Raw Materials
    Route::get('/raw-material-stock', [RawMaterialController::class, 'stockIndex'])
        ->name('superadmin.raw-material-stock.index');

    // WASTAGE CRUD
    Route::resource('/wastage', WastageController::class)
        ->names('superadmin.wastage');

    // API: Wastage Batch Loader
    Route::get('/api/wastage/batches/{rawMaterialId}', 
        [WastageController::class, 'getRawMaterialBatches']
    )->name('superadmin.api.wastage.batches');

    // --------------------------------------------------
    // PRODUCT MANAGEMENT
    // --------------------------------------------------
    Route::resource('/products', ProductController::class)
        ->names('superadmin.products');

    // PRODUCT RECEIVE / PURCHASE
    Route::resource('/product-receives', ProductReceiveController::class)
        ->names('superadmin.product-receives');

    // AJAX: Dynamic Item Row load
    Route::get('/product-receives/get-item-row', 
        [ProductReceiveController::class, 'getItemRow']
    )->name('superadmin.product-receives.get-item-row');

    // API: Product Rates
    Route::get('/api/products/rates/{productId}', 
        [ProductController::class, 'getRates']
    )->name('superadmin.api.products.rates');

    // --------------------------------------------------
    // SALES MANAGEMENT
    // --------------------------------------------------
    Route::prefix('sales')->name('superadmin.sales.')->group(function () {

        Route::get('/', [SalesInvoiceController::class, 'index'])->name('index');

        Route::get('/create', [SalesInvoiceController::class, 'create'])->name('create');

        Route::post('/', [SalesInvoiceController::class, 'store'])->name('store');

        Route::get('/{id}', [SalesInvoiceController::class, 'show'])->name('show');

        // API: Stock Batches By Product ID
        Route::get('/api/product-stock/batches/{productId}', 
            [SalesInvoiceController::class, 'getProductBatches']
        )->name('api.product-stock.batches');
    });

    // --------------------------------------------------
    // SETTINGS & MASTER DATA
    // --------------------------------------------------

    // SUPPLIERS CRUD
    Route::resource('/suppliers', SupplierController::class)
        ->names('superadmin.suppliers');

    // DEPO LIST
    Route::get('/depo', [DepoListController::class, 'index'])
        ->name('superadmin.depo.index');

    // DISTRIBUTOR LIST
    Route::get('/distributor', [DepoListController::class, 'index'])
        ->name('superadmin.distributor.index');

});
