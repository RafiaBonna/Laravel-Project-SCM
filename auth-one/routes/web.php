<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleRedirectController;

/*
|--------------------------------------------------------------------------
| Default Route → Redirect to Login
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Role Based Dashboard Redirect
|--------------------------------------------------------------------------
| লগইন করার পর ইউজারকে তার রোল অনুযায়ী সঠিক ড্যাশবোর্ডে পাঠাবে।
*/
Route::get('/dashboard', [RoleRedirectController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| General User Dashboard (Fallback)
|--------------------------------------------------------------------------
| যদি RoleRedirectController কোনো নির্দিষ্ট রোল না খুঁজে পায়,
| তবে এটি সাধারণ ইউজার ড্যাশবোর্ডে পাঠাবে।
*/
Route::get('/general/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('user.dashboard');

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Logout Route (For Breeze / Custom Auth)
|--------------------------------------------------------------------------
| সাধারণত logout route 'auth.php' থেকে আসে, কিন্তু এখানে fallback হিসেবে রাখা হয়েছে।
*/
Route::post('/logout', function () {
    auth()->logout();
    return redirect()->route('login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Include Role Specific Route Files
|--------------------------------------------------------------------------
| Superadmin, Depo, Distributor – এই তিনটা role এর আলাদা route ফাইল।
| ⚠️ সব সময় 'require' গুলো নিচে রাখো, যাতে default route গুলোর সাথে conflict না হয়।
*/
require __DIR__ . '/auth.php';           // Laravel Breeze routes
require __DIR__ . '/superadmin.php';     // Superadmin routes
require __DIR__ . '/depo.php';           // Depo routes
require __DIR__ . '/distributor.php';    // Distributor routes
