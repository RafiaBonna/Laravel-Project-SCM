<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Wastage;
use App\Models\RawMaterial;
use App\Models\RawMaterialStock; // Required for Stock update and batch loading
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WastageController extends Controller
{
    /**
     * 🔹 Wastage List
     */
    public function index()
    {
        $wastages = Wastage::with(['rawMaterial', 'user'])->latest()->paginate(10);
        return view('superadmin.wastage.index', compact('wastages'));
    }

    /**
     * 🔹 Create Form
     * Only show raw materials that have stock greater than 0 in the dropdown.
     */
    public function create()
    {
        $rawMaterials = RawMaterial::whereHas('stocks', function ($q) {
            $q->where('stock_quantity', '>', 0);
        })->orderBy('name')->get(['id', 'name', 'unit_of_measure']);

        return view('superadmin.wastage.create', compact('rawMaterials'));
    }

    /**
     * 🔹 Store Wastage
     */
    public function store(Request $request)
    {
        $request->validate([
            'wastage_date' => 'required|date',
            'raw_material_id' => 'required|exists:raw_materials,id',
            'raw_material_stock_id' => 'required|exists:raw_material_stocks,id',
            'quantity_wasted' => 'required|numeric|gt:0', // quantity must be greater than 0
            'reason' => 'required|string|max:255',
        ]);
        
        $stock = RawMaterialStock::findOrFail($request->raw_material_stock_id);
        
        // Check if sufficient stock is available
        if ($request->quantity_wasted > $stock->stock_quantity) {
             return back()->withInput()->with('error', 'Wastage quantity cannot be greater than the available stock quantity ('.$stock->stock_quantity.').');
        }

        DB::beginTransaction();
        try {
            // 1. Calculate cost using Average Purchase Price
            $unitCost = $stock->average_purchase_price;
            $totalCost = (float)$request->quantity_wasted * $unitCost;

            // 2. Create Wastage entry
            Wastage::create([
                'wastage_date' => $request->wastage_date,
                'raw_material_id' => $request->raw_material_id,
                'raw_material_stock_id' => $request->raw_material_stock_id,
                'batch_number' => $stock->batch_number,
                'quantity_wasted' => $request->quantity_wasted,
                'unit_cost' => $unitCost,
                'total_cost' => round($totalCost, 2),
                'reason' => $request->reason,
                'user_id' => Auth::id(),
            ]);

            // 3. Update Raw Material Stock (Decrement quantity from stock)
            $stock->decrement('stock_quantity', $request->quantity_wasted);
            
            DB::commit();
            return redirect()->route('superadmin.wastage.index')->with('success', 'Wastage successfully recorded. Stock has been updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Show Wastage Details
     */
    public function show(Wastage $wastage)
    {
        $wastage->load(['rawMaterial', 'user', 'stock']);
        return view('superadmin.wastage.show', compact('wastage'));
    }
    
    /**
     * ❌ Delete Wastage Entry and Reverse Stock
     * Important: When Wastage is deleted, the reduced quantity must be returned to stock (Increment).
     */
    public function destroy(Wastage $wastage)
    {
        DB::beginTransaction();
        try {
            // 1. Load Stock Batch
            $stock = RawMaterialStock::where('id', $wastage->raw_material_stock_id)
                                     ->lockForUpdate() // Lock the row for safety
                                     ->firstOrFail();

            // 2. Return Wastage Quantity to stock (Increment)
            $stock->increment('stock_quantity', $wastage->quantity_wasted);
            
            // 3. Delete Wastage entry
            $wastage->delete();

            DB::commit();
            return redirect()->route('superadmin.wastage.index')
                             ->with('success', 'Wastage entry successfully deleted and stock has been reversed.');
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete Wastage entry. Error: ' . $e->getMessage());
        }
    }

    // =======================================================
    // 💡 Function: For loading batches (AJAX ROUTE)
    // ✅ FIX: Method name changed from getStockBatches to getRawMaterialBatches
    // =======================================================

    /**
     * 🔹 AJAX: Load Stock Batches for a specific Raw Material
     */
    public function getRawMaterialBatches(int $rawMaterialId)
    {
        // Load batches where stock quantity is greater than 0.
        $batches = RawMaterialStock::where('raw_material_id', $rawMaterialId)
            ->where('stock_quantity', '>', 0)
            ->orderBy('id', 'asc') // FIFO logic (ordered by ID)
            ->get([
                'id', 
                'batch_number', 
                'stock_quantity', 
                'average_purchase_price'
            ]);

        // Create a display label for each batch
        $response = $batches->map(function ($batch) {
            return [
                'id'           => $batch->id,
                'batch_number' => $batch->batch_number,
                // সংখ্যাগুলো স্ট্রিং হিসেবে পাঠানো হচ্ছে যাতে জাভাস্ক্রিপ্টে ডেটা লস না হয়
                'stock_quantity' => number_format($batch->stock_quantity, 2, '.', ''), 
                'unit_cost'    => number_format($batch->average_purchase_price, 4, '.', ''), 
                'text'         => "Batch: {$batch->batch_number} (Qty: " . number_format($batch->stock_quantity, 2) . ")",
            ];
        });

        return response()->json($response);
    }
}