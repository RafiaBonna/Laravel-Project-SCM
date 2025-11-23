<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\RawMaterial;
use App\Models\PurchaseInvoice;
use App\Models\RawMaterialPurchaseItem;
use App\Models\RawMaterialStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RawMaterialPurchaseController extends Controller
{
    // 📋 1. Purchase Invoice List
    public function index()
    {
        $invoices = PurchaseInvoice::with(['supplier', 'user'])->latest()->paginate(10);
        return view('superadmin.raw_material_purchases.index', compact('invoices'));
    }

    // 🧾 2. Create Form
    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $rawMaterials = RawMaterial::orderBy('name')->get(['id', 'name', 'unit_of_measure']);
        return view('superadmin.raw_material_purchases.create', compact('suppliers', 'rawMaterials'));
    }

    // 💾 3. Store Purchase
    public function store(Request $request)
    {
        // === FIX: Removed strict 'same:calculated_total' validation to avoid floating-point errors ===
        $request->validate([
            'invoice_number' => 'required|string|max:255|unique:purchase_invoices,invoice_number',
            'invoice_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'discount_amount' => 'nullable|numeric|min:0',
            'grand_total' => 'required|numeric|gt:0', // Ensure grand total is greater than 0
            'calculated_total' => 'required|numeric', // Keep calculated total check for logic
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'nullable|exists:raw_materials,id',
            'items.*.new_material_name' => 'nullable|string|max:255',
            'items.*.unit_of_measure' => 'required|string|max:50',
            'items.*.batch_number' => 'required|string|max:50',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total_price' => 'required|numeric|min:0',
        ]);

        // === Manual check for grand total consistency (safer than 'same' rule) ===
        $subTotalFromItems = collect($request->items)->sum('total_price');
        $calculatedGrandTotal = $subTotalFromItems - ($request->discount_amount ?? 0);

        // Check if the difference is negligible (e.g., within 2 decimal places)
        if (abs($calculatedGrandTotal - $request->grand_total) > 0.01) {
             return back()->with('error', 'Validation Error: Grand Total calculation mismatch. Please re-check the item totals and discount.')
                          ->withInput();
        }


        DB::beginTransaction();
        try {
            // $subTotal is now $subTotalFromItems, calculated server-side for safety
            $subTotal = $subTotalFromItems;

            // Create Purchase Invoice
            $invoice = PurchaseInvoice::create([
                'invoice_number' => $request->invoice_number,
                'supplier_id' => $request->supplier_id,
                'invoice_date' => $request->invoice_date,
                'sub_total' => $subTotal,
                'discount_amount' => $request->discount_amount ?? 0,
                'grand_total' => $request->grand_total,
                'notes' => $request->notes,
                'user_id' => Auth::id(),
            ]);

            // Loop items
            foreach ($request->items as $itemData) {

                // ✅ If new material name given, create it first
                if (empty($itemData['raw_material_id']) && !empty($itemData['new_material_name'])) {
                    $rawMaterial = RawMaterial::create([
                        'name' => $itemData['new_material_name'],
                        'unit_of_measure' => $itemData['unit_of_measure'],
                        'description' => 'Added from purchase invoice #' . $request->invoice_number,
                    ]);
                    $itemData['raw_material_id'] = $rawMaterial->id;
                }
                
                // === New material check for validation failure if both are empty ===
                if (empty($itemData['raw_material_id'])) {
                    DB::rollBack();
                    return back()->with('error', 'Error: Raw Material must be selected or a New Material Name must be provided for all items.')->withInput();
                }

                // Create purchase item
                $item = RawMaterialPurchaseItem::create([
                    'purchase_invoice_id' => $invoice->id,
                    'raw_material_id' => $itemData['raw_material_id'],
                    'batch_number' => $itemData['batch_number'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemData['total_price'],
                ]);

                $this->updateStock($item);
            }

            DB::commit();
            return redirect()->route('superadmin.raw-material-purchases.index')
                ->with('success', 'Purchase saved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            // === IMPROVEMENT: Return the detailed error to the user for debugging ===
            return back()->with('error', 'Transaction Error: ' . $e->getMessage() . ' on line ' . $e->getLine())->withInput();
        }
    }

    // 👁️ 5. Show Purchase Invoice
    public function show(PurchaseInvoice $rawMaterialPurchase)
    {
        // Route Model Binding এর মাধ্যমে PurchaseInvoice মডেলের ইনস্ট্যান্স $rawMaterialPurchase-এ পাওয়া যায়।
        $invoice = $rawMaterialPurchase->load([
            'supplier', 
            'user', 
            'items.rawMaterial' // Item এর সাথে কাঁচামালের নাম ও ইউনিট লোড করা হচ্ছে।
        ]);

        return view('superadmin.raw_material_purchases.show', compact('invoice'));
    }

    // 📦 4. Stock Update
    private function updateStock(RawMaterialPurchaseItem $item)
    {
        $existingStock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)
            ->where('batch_number', $item->batch_number)
            ->first();

        $newQty = (float)$item->quantity;
        $newPrice = (float)$item->unit_price;

        if ($existingStock) {
            $oldQty = (float)$existingStock->stock_quantity;
            $oldAvg = (float)$existingStock->average_purchase_price;
            $newAvg = (($oldQty * $oldAvg) + ($newQty * $newPrice)) / ($oldQty + $newQty);

            $existingStock->increment('stock_quantity', $newQty);
            $existingStock->update([
                'average_purchase_price' => round($newAvg, 4),
                'last_in_date' => now()->toDateString(),
            ]);
        } else {
            RawMaterialStock::create([
                'raw_material_id' => $item->raw_material_id,
                'batch_number' => $item->batch_number,
                'stock_quantity' => $newQty,
                'average_purchase_price' => $newPrice,
                'last_in_date' => now()->toDateString(),
            ]);
        }
    }
}