<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Supplier; // ✅ Supplier Model import করা হয়েছে
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    /**
     * Supplier-দের তালিকা দেখায়। (R - Read / Index)
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('id', 'desc')->paginate(10);
        return view('superadmin.suppliers.index', compact('suppliers'));
    }

    /**
     * নতুন Supplier তৈরির ফর্ম দেখায়। (C - Create Form)
     */
    public function create()
    {
        return view('superadmin.suppliers.create');
    }

    /**
     * নতুন Supplier-কে সিস্টেমে সংরক্ষণ করে। (C - Create Store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:suppliers,email',
            'contact_person' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        Supplier::create($request->all());

        return redirect()->route('superadmin.suppliers.index')
                         ->with('success', 'Supplier created successfully!');
    }

    /**
     * একটি নির্দিষ্ট Supplier-এর তথ্য দেখায়। (Optional: Show)
     */
    public function show(Supplier $supplier)
    {
        return view('superadmin.suppliers.show', compact('supplier'));
    }

    /**
     * একটি নির্দিষ্ট Supplier-কে এডিট করার ফর্ম দেখায়। (U - Edit Form)
     */
    public function edit(Supplier $supplier)
    {
        return view('superadmin.suppliers.edit', compact('supplier'));
    }

    /**
     * একটি নির্দিষ্ট Supplier-এর তথ্য আপডেট করে। (U - Update Store)
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            // Update-এর সময় নিজের নাম বাদ দিয়ে unique চেক করা
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers')->ignore($supplier->id)],
            'phone' => 'nullable|string|max:20',
            'email' => ['nullable', 'email', Rule::unique('suppliers')->ignore($supplier->id)],
            'contact_person' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $supplier->update($request->all());

        return redirect()->route('superadmin.suppliers.index')
                         ->with('success', 'Supplier updated successfully!');
    }

    /**
     * একটি নির্দিষ্ট Supplier-কে সিস্টেম থেকে ডিলিট করে। (D - Delete)
     */
    public function destroy(Supplier $supplier)
    {
        // ✅ CRITICAL: ডিলিট করার আগে নিশ্চিত করুন এই Supplier-এর সাথে কোনো Raw Material Purchase Invoice যুক্ত নেই।
        // বর্তমানে আমরা সহজ রাখার জন্য সরাসরি ডিলিট করছি।
        $supplier->delete();

        return redirect()->route('superadmin.suppliers.index')
                         ->with('success', 'Supplier deleted successfully.');
    }
}