<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller; 

class RoleRedirectController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login'); 
        }

        $user = Auth::user();
        
        $roleSlug = $user->getPrimaryRole();

        switch ($roleSlug) {
            case 'superadmin':
                return redirect()->route('superadmin.dashboard');
            // ❌ Admin-এর জন্য কোনো কেস নেই
            case 'depo':
                return redirect()->route('depo.dashboard');
            case 'distributor':
                return redirect()->route('distributor.dashboard');
            default:
                return redirect()->route('user.dashboard'); 
        }
    }
}