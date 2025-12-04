<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\RawMaterial;
use App\Models\RawMaterialStock;
use App\Models\ProductionIssue;     // Stock Out Header
use App\Models\ProductionIssueItem; // Stock Out Items
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RawMaterialStockOutController extends Controller
{
    /**
     * 🔹 Stock Out List
     * ✅ FIX: This method ensures $stockOuts is passed to the view.
     */
    public function index()
    {
        // $stockOuts ভেরিয়েবলটি fetching এবং passing করা হচ্ছে।
        $stockOuts = ProductionIssue::with('user')->latest()->paginate(10);
        return view('superadmin.raw_material_stock_out.index', compact('stockOuts'));
    }

    /**
     * 🔹 Create Form
     */
    public function create()
    {
        // শুধু যেসব কাঁচামালের স্টক আছে সেগুলো dropdown-এ দেখাবে
        $rawMaterials = RawMaterial::whereHas('stocks', function ($q) {
            $q->where('stock_quantity', '>', 0);
        })->orderBy('name')->get(['id', 'name', 'unit_of_measure']);

        return view('superadmin.raw_material_stock_out.create', compact('rawMaterials'));
    }

    /**
     * 🔹 AJAX: নির্দিষ্ট Raw Material এর জন্য স্টক ব্যাচ লোড করা
     * Route → superadmin/api/raw-material-stock/batches/{rawMaterialId}
     */
    public function getStockBatches(int $rawMaterialId)
    {
        // স্টকে যেগুলোর quantity > 0 শুধু সেগুলোই পাঠানো হচ্ছে
        $batches = RawMaterialStock::where('raw_material_id', $rawMaterialId)
            ->where('stock_quantity', '>', 0)
            ->get(['id', 'batch_number', 'stock_quantity', 'average_purchase_price'])
            ->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'batch_number' => $stock->batch_number,
                    'stock_quantity' => (float)$stock->stock_quantity,
                    'average_purchase_price' => (float)$stock->average_purchase_price,
                ];
            });

        return response()->json($batches);
    }

    /**
     * 🔹 Store (Save Stock Out)
     */
  public function store(Request $request)
    {
        // ✅ ফিক্স: কঠোর ভ্যালিডেশন যোগ করা হয়েছে
        $request->validate([
            'slip_number' => ['required', 'string', 'unique:production_issues,issue_number'], 
            'issue_date' => 'required|date',
            // আইটেম অ্যারে ভ্যালিডেশন:
            'items' => 'required|array|min:1',
            // প্রতিটি আইটেমের জন্য ভ্যালিডেশন:
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.raw_material_stock_id' => 'required|exists:raw_material_stocks,id', // <-- অনুপস্থিত কী ফিক্স করা হয়েছে
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'factory_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ], [
            'slip_number.unique' => 'This issue slip number already exists.',
            'items.required' => 'At least one material item is required.',
            'items.*.raw_material_stock_id.required' => 'For every item, you must select a Batch.', // কাস্টম এরর মেসেজ
        ]);
        
        DB::beginTransaction();
        try {
            // 1️⃣ Production Issue Header তৈরি করা
            $productionIssue = ProductionIssue::create([
                'issue_number' => $request->slip_number,
                'factory_name' => $request->factory_name,
                'issue_date' => $request->issue_date,
                'user_id' => Auth::id(), 
                'notes' => $request->notes,
            ]);

            $totalQuantity = 0;
            $totalCost = 0;

            foreach ($request->items as $item) {
                // ভ্যালিডেশনের কারণে এখন এই কী নিশ্চিতভাবে উপস্থিত থাকবে।
                $issuedQty = $item['quantity'];
                $unitCost = $item['unit_price'];
                $lineTotal = round($issuedQty * $unitCost, 2);

                // 2️⃣ Raw Material Stock আপডেট করা (Stock Out)
                $stock = RawMaterialStock::lockForUpdate()->find($item['raw_material_stock_id']);

                if (!$stock || $stock->stock_quantity < $issuedQty) {
                    DB::rollBack();
                    // যদি পর্যাপ্ত স্টক না থাকে
                    return back()->withInput()->with('error', 'Error: Insufficient stock for batch ' . ($stock ? $stock->batch_number : 'ID ' . $item['raw_material_stock_id']));
                }

                $stock->stock_quantity -= $issuedQty;
                $stock->save();

                // 3️⃣ Production Issue Item তৈরি করা
                ProductionIssueItem::create([
                    'production_issue_id' => $productionIssue->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'raw_material_stock_id' => $item['raw_material_stock_id'],
                    'batch_number' => $item['batch_number'] ?? $stock->batch_number,
                    'quantity_issued' => $issuedQty,
                    'unit_cost' => $unitCost,
                    'total_cost' => $lineTotal,
                ]);

                $totalQuantity += $issuedQty;
                $totalCost += $lineTotal;
            }

            // 4️⃣ মোট যোগফল আপডেট করা
            $productionIssue->update([
                'total_quantity_issued' => $totalQuantity,
                'total_issue_cost' => round($totalCost, 2),
            ]);

            DB::commit();
            return redirect()->route('superadmin.raw-material-stock-out.index')
                             ->with('success', 'Raw material issued successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Show a single issue slip
     */
    public function show(ProductionIssue $raw_material_stock_out)
    {
        $stockOut = $raw_material_stock_out->load(['user', 'items.rawMaterial']);
        return view('superadmin.raw_material_stock_out.show', compact('stockOut'));
    }

    /**
     * 🔹 Delete issue slip
     */
    public function destroy(ProductionIssue $raw_material_stock_out)
    {
        try {
            $raw_material_stock_out->delete();
            return redirect()->route('superadmin.raw-material-stock-out.index')
                             ->with('success', 'ইস্যু স্লিপটি ডিলিট করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->with('error', 'ইস্যু স্লিপটি ডিলিট করা যায়নি: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Raw Material Stock Report Generation
     */
    public function stockReport()
    {
        // DB::raw() ব্যবহার করে প্রতিটি Raw Material এর জন্য সমষ্টিগত স্টক এবং মূল্য গণনা করা হচ্ছে।
        $stockReportData = RawMaterialStock::select(
            'raw_material_id',
            DB::raw('SUM(stock_quantity) as current_stock_qty'), // মোট স্টক পরিমাণ
            DB::raw('SUM(stock_quantity * average_purchase_price) / SUM(stock_quantity) as avg_unit_cost'), // গড় একক খরচ (Weighted Average Price)
            DB::raw('SUM(stock_quantity * average_purchase_price) as total_value') // মোট স্টক মূল্য
        )
        ->where('stock_quantity', '>', 0) // শুধু যেগুলোতে স্টক আছে
        ->groupBy('raw_material_id')
        ->with('rawMaterial') // RawMaterial নাম আনার জন্য
        ->get();

        // ভিউ-তে ডেটা পাঠানো
        return view('superadmin.raw_material_stock_out.stock_report', compact('stockReportData'));
    }

}