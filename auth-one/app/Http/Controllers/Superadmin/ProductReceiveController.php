<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReceive;
use App\Models\ProductReceiveItem;
use App\Models\ProductStock;
use App\Models\User;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductReceiveController extends Controller
{
    public function index()
    {
        $productReceives = ProductReceive::with('receiver', 'receivedBy')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('superadmin.product_receives.index', compact('productReceives'));
    }

    public function create()
    {
        $products = Product::where('is_active', 1)->pluck('name', 'id');
        $users = User::pluck('name', 'id');

        // Logic for auto-generating receive_no
        $lastReceive = ProductReceive::orderBy('id', 'desc')->first();
        $receive_no = 'PR-001';
        if ($lastReceive) {
            // Extracts the number part and increments it
            $lastNumber = (int) filter_var($lastReceive->receive_no, FILTER_SANITIZE_NUMBER_INT);
            $receive_no = 'PR-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        return view('superadmin.product_receives.create', [
            'products' => $products,
            'users' => $users,
            'receiveNos' => [$receive_no], // Pass the single generated number
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'receive_no' => 'required|string|unique:product_receives,receive_no',
            'receive_date' => 'required|date',
            'receiver_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            // Ensure this is required as it must be submitted
            'items.*.qty' => 'required|numeric|min:0.01', 
            'items.*.cost_rate' => 'nullable|numeric|min:0',
            'items.*.mrp' => 'nullable|numeric|min:0',
            'items.*.retail' => 'nullable|numeric|min:0',
            'items.*.distributor' => 'nullable|numeric|min:0',
            'items.*.depo_selling' => 'nullable|numeric|min:0',
            'items.*.batch_no' => 'nullable|string',
            'items.*.production_date' => 'nullable|date',
            'items.*.expiry_date' => 'nullable|date',
        ]);

        $totalQty = 0;
        $totalCost = 0;

        try {
            DB::beginTransaction();
            
            // 2. Create ProductReceive header
            $receive = ProductReceive::create([
                'receive_no' => $request->receive_no,
                'receive_date' => $request->receive_date,
                'receiver_id' => $request->receiver_id,
                'note' => $request->note,
                'received_by_user_id' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $qty = $item['qty'];
                $costRate = $item['cost_rate'] ?? 0;
                $itemCost = $qty * $costRate;

                // 3. Save item
                $receive->items()->create([
                    'product_id' => $item['product_id'],
                    'batch_no' => $item['batch_no'] ?? null,
                    'received_quantity' => $qty,
                    'mrp' => $item['mrp'] ?? 0,
                    'retail' => $item['retail'] ?? 0,
                    'distributor' => $item['distributor'] ?? 0,
                    'depo_selling' => $item['depo_selling'] ?? 0,
                    'cost_rate' => $costRate,
                    'total_item_cost' => $itemCost,
                    'production_date' => $item['production_date'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                ]);

                $totalQty += $qty;
                $totalCost += $itemCost;

                // 4. Update stock
                $productStock = ProductStock::firstOrNew(['product_id' => $item['product_id']]);
                // Ensure available_quantity is initialized if it's new
                $productStock->available_quantity = ($productStock->available_quantity ?? 0) + $qty;
                $productStock->save();
            }

            // 5. Update total qty and cost on header
            $receive->update([
                'total_received_qty' => $totalQty,
                'total_cost' => $totalCost,
            ]);

            DB::commit();

            return redirect()->route('superadmin.product-receives.index')
                ->with('success', 'Product Receive Entry saved successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            // Log the error for debugging
            \Log::error("Product Receive Store Error: " . $e->getMessage()); 
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save Product Receive Entry. Please check the logs.');
        }
    }
    public function show($id)
    {
        // Find the record along with its associated items and the involved users (receiver/receivedBy)
        $productReceive = ProductReceive::with('items.product', 'receiver', 'receivedBy')
            ->findOrFail($id);

        return view('superadmin.product_receives.show', compact('productReceive'));
    }
}