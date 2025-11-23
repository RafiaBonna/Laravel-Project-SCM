<?php
// app/Http/Controllers/Superadmin/ProductController.php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Product List / Entry Index
     */
    public function index()
    {
        $products = Product::latest()->paginate(20);
        return view('superadmin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('superadmin.products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'unit' => 'required|string|max:50',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'description' => 'nullable|string',
            
            // 🎯 Financial Fields Validation
            'mrp' => 'required|numeric|min:0',
            'retail_rate' => 'nullable|numeric|min:0',
            'depo_selling_price' => 'nullable|numeric|min:0',
            'distributor_rate' => 'nullable|numeric|min:0',
        ]);

        Product::create([
            'name' => $request->name,
            'unit' => $request->unit,
            'sku' => $request->sku,
            
            // 🎯 Financial Fields Data
            'mrp' => $request->mrp,
            'retail_rate' => $request->retail_rate ?? 0,
            'depo_selling_price' => $request->depo_selling_price ?? 0,
            'distributor_rate' => $request->distributor_rate ?? 0, // নতুন ফিল্ড

            'description' => $request->description,
            'created_by' => Auth::id(),
            'current_stock' => 0,
            'is_active' => true,
        ]);

        return redirect()->route('superadmin.products.index')
                         ->with('success', 'New Product added successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('superadmin.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'unit' => 'required|string|max:50',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'description' => 'nullable|string',
            'is_active' => 'required|boolean', // Active/Inactive স্ট্যাটাস
            
            // 🎯 Financial Fields Validation
            'mrp' => 'required|numeric|min:0',
            'retail_rate' => 'nullable|numeric|min:0',
            'depo_selling_price' => 'nullable|numeric|min:0',
            'distributor_rate' => 'nullable|numeric|min:0',
        ]);

        $product->update([
            'name' => $request->name,
            'unit' => $request->unit,
            'sku' => $request->sku,
            
            // 🎯 Financial Fields Update
            'mrp' => $request->mrp,
            'retail_rate' => $request->retail_rate ?? 0,
            'depo_selling_price' => $request->depo_selling_price ?? 0,
            'distributor_rate' => $request->distributor_rate ?? 0,

            'description' => $request->description,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('superadmin.products.index')
                         ->with('success', 'Product updated successfully!');
    }
    
    /**
     * AJAX/API কলের জন্য প্রোডাক্ট রেটগুলো এনে দেয়।
     */
    public function getRates($id)
    {
        // শুধু রেট ফিল্ডগুলো সিলেক্ট করা হলো
        $product = Product::select('mrp', 'retail_rate', 'depo_selling_price', 'distributor_rate')
                          ->where('id', $id)
                          ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // JSON ফরম্যাটে রেটগুলো রিটার্ন করা
        return response()->json($product);
    }

    // destroy() ফাংশন প্রয়োজন অনুযায়ী পরে যোগ করা যাবে...
    public function show(Product $product) { /* ... */ }
    public function destroy(Product $product) { /* ... */ }
}