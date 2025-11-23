<?php

use App\Http\Controllers\Depo\DepoDashboardController;
use App\Http\Controllers\Depo\SalesApprovalController;
// 🆕 নতুন কন্ট্রোলারটি যোগ করুন
use App\Http\Controllers\Depo\DistributorController; 
use Illuminate\Support\Facades\Route;

Route::prefix('depo')->group(function () {
    Route::middleware(['auth', 'role:depo'])->group(function () {

        // Dashboard Route
        Route::get('/dashboard', [DepoDashboardController::class, 'index'])->name('depo.dashboard');

        // SALES APPROVAL & VIEW
        // Route name prefix → depo.invoices.*
        Route::prefix('invoices')->controller(SalesApprovalController::class)->name('depo.invoices.')->group(function () {
            // All invoices visible to Depo
            Route::get('/', 'index')->name('index');
            Route::get('/pending', 'pending')->name('pending'); // Only Pending Invoices
            Route::get('/{salesInvoice}', 'show')->name('show'); // Invoice Details

            // Approval Action (deduct stock)
            Route::post('/{salesInvoice}/approve', 'approve')->name('approve');

            // Cancellation Action (no stock deduction)
            Route::post('/{salesInvoice}/cancel', 'cancel')->name('cancel');
        });
        
        // 🆕 DISTRIBUTOR MANAGEMENT MODULE (Uses depo.users.* names)
        // এই Route Resource টি depo.users.index, .create, .store ইত্যাদি নাম তৈরি করবে।
        Route::resource('distributors', DistributorController::class)
            ->names([
                'index'   => 'depo.users.index',   // সাইডবার এই রুটটি কল করছে
                'create'  => 'depo.users.create',
                'store'   => 'depo.users.store',
                'show'    => 'depo.users.show',
                'edit'    => 'depo.users.edit',
                'update'  => 'depo.users.update',
                'destroy' => 'depo.users.destroy',
            ])
            ->parameters(['distributors' => 'distributor']); // URL-এ parameter হিসেবে 'distributor' ব্যবহার করবে

    });
});