<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DepoListController extends Controller
{
    /**
     * Shows a list of all users who have the 'depo' role (i.e., Depo Managers).
     */
    public function index()
    {
        // 'depo' রোল আছে এমন ইউজারদের ফিল্টার করে লোড করা হচ্ছে।
        // সাথে ইউজারদের সম্পর্কিত depo entity-ও eager load করা হচ্ছে।
        $depos = User::whereHas('roles', function ($query) {
                        $query->where('slug', 'depo');
                    })
                    ->with('depo') // Depo মডেলের ডেটা লোড করুন
                    ->orderBy('id', 'desc')
                    ->paginate(10); // আপনার প্রয়োজন অনুযায়ী পেইজ লিমিট দিন

        // নিশ্চিত করুন আপনার ভিউ ফাইলটি resources/views/superadmin/depo/index.blade.php
        return view('superadmin.depo.index', compact('depos'));
    }
}