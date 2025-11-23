<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;

class DistributorDashboardController extends Controller
{
    public function index()
    {
        return view('distributor.dashboard');
    }
}
