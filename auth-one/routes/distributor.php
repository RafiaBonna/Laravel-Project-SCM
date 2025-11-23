<?php

use App\Http\Controllers\Distributor\DistributorDashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('distributor')->group(function () {
    Route::middleware(['auth', 'role:distributor'])->group(function () {
        
        // Dashboard Route
        Route::get('/dashboard', [DistributorDashboardController::class, 'index'])->name('distributor.dashboard');

      
    });
});