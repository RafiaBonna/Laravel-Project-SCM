<?php

namespace App\Http\Controllers\Depo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DepoDashboardController extends Controller
{
    /**
     * Vendor Dashboard-এর মূল পেজ দেখাবে।
     */
    public function index()
    {
        // নিশ্চিত করুন resources/views/vendor/dashboard.blade.php ফাইলটি তৈরি আছে।
        return view('depo.dashboard'); 
    }
}