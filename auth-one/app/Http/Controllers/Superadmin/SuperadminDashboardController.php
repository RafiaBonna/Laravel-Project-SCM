<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperadminDashboardController extends Controller
{
    public function index()
    {
        // সরাসরি নতুন SuperAdmin ভিউ রিটার্ন করবে
        return view('superadmin.dashboard');
    }
}