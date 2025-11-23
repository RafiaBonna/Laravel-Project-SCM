<?php

namespace App\Http\Controllers\Depo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // আপনার ইউজার মডেল ইম্পোর্ট করুন
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DistributorController extends Controller
{
    /**
     * ডিস্ট্রিবিউটরদের তালিকা দেখাবে। (depo.users.index)
     */
    public function index()
    {
        // লগইন করা ডিপো অ্যাডমিনের আইডি থেকে Distributor-দের খুঁজে বের করবে।
        // ধরে নেওয়া হচ্ছে Depo User-এর কাছে depo_id আছে।
        $depoId = auth()->user()->depo_id;

        // শুধু এই Depo-এর অধীনে থাকা 'distributor' role-এর ইউজারদের আনা হলো।
        $distributors = User::where('role', 'distributor')
                            ->where('depo_id', $depoId)
                            ->paginate(10); 

        // ভিউ ফাইল লোড করা হলো: resources/views/depo/users/index.blade.php
        return view('depo.users.index', compact('distributors'));
    }

    /**
     * নতুন ডিস্ট্রিবিউটর তৈরির ফর্ম দেখাবে। (depo.users.create)
     */
    public function create()
    {
        // ভিউ ফাইল লোড করা হলো: resources/views/depo/users/create.blade.php
        return view('depo.users.create');
    }

    /**
     * নতুন ডিস্ট্রিবিউটর ডাটাবেসে সংরক্ষণ করবে। (depo.users.store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'distributor', 
            'depo_id' => auth()->user()->depo_id, // লগইন করা ডিপো আইডি সেট করা হলো
        ]);

        return redirect()->route('depo.users.index')->with('success', 'নতুন ডিস্ট্রিবিউটর সফলভাবে তৈরি হয়েছে!');
    }

    /**
     * একটি নির্দিষ্ট ডিস্ট্রিবিউটরের তথ্য সম্পাদনার (edit) ফর্ম দেখাবে। (depo.users.edit)
     */
    public function edit(User $distributor)
    {
        // নিরাপত্তা নিশ্চিত করা: ডিস্ট্রিবিউটরটি এই ডিপোর অধীনেই আছে কিনা
        if ($distributor->depo_id !== auth()->user()->depo_id || $distributor->role !== 'distributor') {
            abort(403, 'Unauthorized action.');
        }
        // ভিউ ফাইল লোড করা হলো: resources/views/depo/users/edit.blade.php
        return view('depo.users.edit', compact('distributor'));
    }

    /**
     * ডিস্ট্রিবিউটরের তথ্য আপডেট করবে। (depo.users.update)
     */
    public function update(Request $request, User $distributor)
    {
        // নিরাপত্তা নিশ্চিত করা
        if ($distributor->depo_id !== auth()->user()->depo_id || $distributor->role !== 'distributor') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($distributor->id)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($distributor->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $distributor->name = $request->name;
        $distributor->email = $request->email;
        $distributor->phone = $request->phone;
        if ($request->password) {
            $distributor->password = Hash::make($request->password);
        }
        $distributor->save();

        return redirect()->route('depo.users.index')->with('success', 'ডিস্ট্রিবিউটর সফলভাবে আপডেট হয়েছে!');
    }

    /**
     * ডিস্ট্রিবিউটরকে ডিলিট করবে। (depo.users.destroy)
     */
    public function destroy(User $distributor)
    {
        // নিরাপত্তা নিশ্চিত করা
        if ($distributor->depo_id !== auth()->user()->depo_id || $distributor->role !== 'distributor') {
            abort(403, 'Unauthorized action.');
        }
        
        $distributor->delete();

        return redirect()->route('depo.users.index')->with('success', 'ডিস্ট্রিবিউটর সফলভাবে মুছে ফেলা হয়েছে!');
    }
    
    // show() মেথডটি এখন প্রয়োজন নেই, তাই এটি খালি রাখা হলো বা বাদ দেওয়া হলো।
    public function show(User $distributor)
    {
        abort(404);
    }
}