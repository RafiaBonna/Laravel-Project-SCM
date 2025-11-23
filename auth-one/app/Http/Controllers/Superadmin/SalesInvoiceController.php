<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use App\Models\ProductStock;
use App\Http\Requests\StoreSalesInvoiceRequest; // ⚠️ ধাপ ৪-এর Form Request
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator; 
use App\Models\Depo; // Depo মডেল
use App\Models\Product; // Product মডেল
use App\Models\Supplier; // Suppliers মডেল (Fix: To resolve Undefined variable $suppliers)


class SalesInvoiceController extends Controller
{
    // ইনভয়েসের তালিকা (List)
    public function index()
    {
        $invoices = SalesInvoice::with(['creator', 'depo'])->latest()->paginate(20);
        return view('superadmin.sales.index', compact('invoices')); 
    }

    /**
     * নতুন ইনভয়েস তৈরির ফর্ম দেখান (Create Form).
     *
     * @return \Illuminate\View\View
     */
  public function create()
{
    $depos = Depo::all();
    $products = Product::all();

    // Invoice no auto-generate
    $lastInvoice = SalesInvoice::orderBy('id', 'desc')->first();
    $invoice_no = 'INV-001';
    if ($lastInvoice) {
        $lastNumber = (int) filter_var($lastInvoice->invoice_no, FILTER_SANITIZE_NUMBER_INT);
        $invoice_no = 'INV-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    return view('superadmin.sales.create', compact('depos', 'products', 'invoice_no'));
}

    
    // API: নির্দিষ্ট পণ্যের জন্য হাতে থাকা স্টক ব্যাচ লোড করা
    public function getProductBatches($productId)
    {
        // হাতে থাকা স্টকগুলো Expire Date অনুযায়ী পুরাতন থেকে নতুনভাবে অর্ডার করা হলো (FIFO)
        $batches = ProductStock::where('product_id', $productId)
                                ->where('available_quantity', '>', 0)
                                ->orderBy('expiry_date', 'asc')
                                ->get(['id', 'batch_no', 'available_quantity', 'expiry_date']);

        // JSON response for AJAX
        return response()->json($batches);
    }

    /**
     * নতুন Sales Invoice সংরক্ষণ করুন (Store).
     * * @param  \App\Http\Requests\StoreSalesInvoiceRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreSalesInvoiceRequest $request)
    {
        // $validatedData = $request->validated();
        // যেহেতু StoreSalesInvoiceRequest ফর্ম রিকোয়েস্টে অ্যারে ডেটা সঠিকভাবে ভ্যালিডেট করা কঠিন, 
        // তাই আমরা এখানে $request->all() ব্যবহার করে কাস্টম ভ্যালিডেশন করব এবং ট্রানজেকশন ব্যবহার করব।
        
        $validatedData = $request->validated();
        $totalAmount = 0;
        $itemsToAttach = [];

        DB::beginTransaction();
        try {
            // ১. আইটেমগুলো প্রক্রিয়াকরণ এবং স্টক চেকিং
            foreach ($validatedData['items'] as $itemData) {
                $requiredQuantity = (int)$itemData['quantity'];
                $itemSubTotal = $requiredQuantity * (float)$itemData['unit_price'];

                // এই ধাপে আমরা স্টকের পরিমাণ যাচাই করব (সাধারণত রিয়েল-টাইম ERP সিস্টেমে করা হয়)
                // আপনার সিস্টেমে, স্টক কমানোর প্রক্রিয়াটি Depo অনুমোদনের সময় ঘটবে।
                // এখন শুধু নিশ্চিত করা হচ্ছে যে পণ্যটি বিদ্যমান।

                $product = Product::find($itemData['product_id']);
                if (!$product) {
                    DB::rollBack();
                    return back()->withInput()->with('error', "পণ্য ID: {$itemData['product_id']} খুঁজে পাওয়া যায়নি।");
                }
                
                // ⚠️ শুধুমাত্র ভবিষ্যতের ব্যবহারের জন্য প্লেসহোল্ডার লজিক। 
                // স্টক Allocation এর প্রকৃত কাজ Depo অনুমোদনের সময় হবে।
                $allocatedQuantity = 1000000; // আপাতত ধরে নেওয়া হলো পর্যাপ্ত স্টক আছে। 
                                            // এই লজিকটি পরে ProductStock/Inventory লজিক দিয়ে আপডেট হবে।
                
                // যদি প্রয়োজনীয় পরিমাণ স্টক না থাকে (এটি Form Request-এর পরেও একটি জরুরি চেক)
                if ($allocatedQuantity < $requiredQuantity) {
                    DB::rollBack();
                    return back()->withInput()->with('error', "পণ্য ID: {$itemData['product_id']} - এর জন্য পর্যাপ্ত স্টক (প্রয়োজনীয়: {$requiredQuantity}) পাওয়া যায়নি।");
                }
                
                $itemsToAttach[] = [
                    'product_id' => $itemData['product_id'],
                    'quantity' => $requiredQuantity,
                    'unit_price' => (float)$itemData['unit_price'],
                    'sub_total' => round($itemSubTotal, 2),
                    // product_stock_id এখন null থাকবে। অনুমোদনের সময় ডিপো এটি নির্ধারণ করবে।
                    'product_stock_id' => null, 
                ];

                $totalAmount += $itemSubTotal; // গ্র্যান্ড টোটাল আপডেট
            }

            // ২. SalesInvoice তৈরি
            $invoice = SalesInvoice::create([
                'invoice_no' => $validatedData['invoice_no'],
                'invoice_date' => $validatedData['invoice_date'],
                'user_id' => auth()->id(), 
                'depo_id' => $validatedData['depo_id'],
                'status' => 'Pending', // ⚠️ Pending স্ট্যাটাস সেট করা হলো
                'total_amount' => round($totalAmount, 2), 
            ]);

            // ৩. SalesInvoiceItems তৈরি
            $invoice->items()->createMany($itemsToAttach);

            DB::commit();

            return redirect()->route('superadmin.sales.index')->with('success', "Sales Invoice #{$invoice->invoice_no} সফলভাবে তৈরি ও Pending অবস্থায় রাখা হয়েছে। ডিপো-এর অনুমোদনের জন্য অপেক্ষা করছে।");

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e); // ডিবাগিং এর জন্য
            return back()->withInput()->with('error', 'Sales Invoice তৈরি করতে ব্যর্থ: ' . $e->getMessage()); 
        }
    }
    public function show($id)
{
    $invoice = SalesInvoice::with(['creator', 'depo', 'items.product'])->findOrFail($id);

    return view('superadmin.sales.show', compact('invoice'));
}

}