<?php

namespace App\Http\Controllers\Depo;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use App\Models\DepoStock;    // ⚠️ ডিপো স্টকের মডেল
use App\Models\ProductStock; // ⚠️ কেন্দ্রীয় স্টকের মডেল
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Auth facade ব্যবহার করা হলো
use Illuminate\Validation\ValidationException; // এটি এখন অপ্রয়োজনীয়, তবে রেখে দেওয়া যেতে পারে

class SalesApprovalController extends Controller
{
    /**
     * Depo-এর জন্য সব ইনভয়েসের তালিকা (Index View-এর জন্য)
     */
    public function index()
    {
        // ডিপোর ইউজার যে ডিপোর সাথে যুক্ত, শুধুমাত্র সেই ইনভয়েসগুলো দেখানো হবে
        $depoId = Auth::user()->depo_id; 
        
        $invoices = SalesInvoice::where('depo_id', $depoId)
                                 ->with(['creator', 'items.product', 'approvedBy'])
                                 ->latest()
                                 ->paginate(15);

        // **View Return (সংশোধিত)**: আপনার Depo ইনডেক্স ভিউ ব্যবহার করা উচিত
        return view('depo.invoices.index', compact('invoices')); 
    }

    /**
     * শুধুমাত্র Pending ইনভয়েসের তালিকা (pending.blade.php ব্যবহার করবে)
     */
    public function pending()
    {
        $depoId = Auth::user()->depo_id; 
        
        $invoices = SalesInvoice::where('depo_id', $depoId)
                                 ->where('status', 'Pending') // শুধুমাত্র Pending স্ট্যাটাস
                                 ->with(['creator', 'items.product'])
                                 ->latest()
                                 ->paginate(15);

        // **View Return (সংশোধিত)**: resources/views/depo/invoices/pending.blade.php
        return view('depo.invoices.pending', compact('invoices'));
    }

    /**
     * একটি ইনভয়েস বিস্তারিত দেখা (show.blade.php ব্যবহার করবে)
     */
    public function show(SalesInvoice $salesInvoice)
    {
        // নিশ্চিত করা হচ্ছে যে ইনভয়েসটি এই Depo-এর
        if ($salesInvoice->depo_id !== Auth::user()->depo_id) {
            abort(403, 'Unauthorized action. এই ইনভয়েসটি দেখার অনুমতি আপনার নেই।');
        }
        
        // items.stock লোড করা হচ্ছে যা SalesInvoiceItem-এর product_stock_id দিয়ে ProductStock মডেলের সাথে যুক্ত।
        $salesInvoice->load(['creator', 'items.product', 'items.stock', 'approvedBy']); 
        
        // **View Return (সংশোধিত)**: resources/views/depo/invoices/show.blade.php
        return view('depo.invoices.show', ['invoice' => $salesInvoice]);
    }

    /**
     * ইনভয়েস অনুমোদন করা ও স্টক আপডেট করা (CORE LOGIC)
     */
    public function approve(SalesInvoice $salesInvoice)
    {
        // ১. অনুমোদনের প্রাথমিক চেক: ডিপো আইডি এবং স্ট্যাটাস
        if ($salesInvoice->depo_id !== Auth::user()->depo_id || $salesInvoice->status !== 'Pending') {
            return back()->with('error', 'এই ইনভয়েসটি অনুমোদন করার অনুমতি নেই বা এটি ইতিমধ্যেই অনুমোদিত/বাতিল করা হয়েছে।');
        }

        DB::beginTransaction();
        try {
            // ২. স্টক ম্যানেজমেন্ট লজিক (Central Stock Out এবং Depo Stock In)
            foreach ($salesInvoice->items as $item) {
                $batchId = $item->product_stock_id;
                $quantity = $item->quantity;

                // A. Central ProductStock থেকে Stock Out করা (Decrement)
                // ⚠️ কনকারেন্সি (Concurrency) রক্ষার জন্য lockForUpdate() ব্যবহার করা হলো।
                $centralStock = ProductStock::lockForUpdate()->find($batchId); 
                
                if (!$centralStock || $centralStock->available_quantity < $quantity) {
                    DB::rollBack();
                    throw new \Exception("Central Stock Batch (ID: {$batchId}) এ পণ্যের ({$item->product->name}) পর্যাপ্ত পরিমাণ নেই। অনুমোদনের আগে স্টক পরিবর্তন হয়েছে।");
                }
                
                // Central ProductStock থেকে পরিমাণ হ্রাস
                $centralStock->decrement('available_quantity', $quantity);

                // B. Depo-এর স্টকে পণ্য যোগ করা (Stock In)
                // একই Depo, Product এবং Batch No-এর জন্য স্টক খুঁজে বের করে আপডেট করা হবে
                $depoStock = DepoStock::firstOrNew([
                    'depo_id' => $salesInvoice->depo_id,
                    'product_id' => $item->product_id,
                    'batch_no' => $centralStock->batch_no ?? null, // ব্যাচ নম্বর সংরক্ষণ
                ]);
                
                // Depo স্টকের পরিমাণ বৃদ্ধি
                $depoStock->quantity += $quantity;
                $depoStock->save();
            }

            // ৩. ইনভয়েসের স্ট্যাটাস আপডেট করা
            $salesInvoice->update([
                'status' => 'Approved',
                'approved_by' => Auth::id(), // অনুমোদনকারীর ID
                'approved_at' => now(),      // অনুমোদনের সময়
                'cancellation_reason' => null, // অনুমোদনের পর বাতিলের কারণ মুছে ফেলা
            ]);

            DB::commit();

            return redirect()->route('depo.invoices.show', $salesInvoice->id)->with('success', "ইনভয়েস #{$salesInvoice->invoice_no} সফলভাবে অনুমোদিত হয়েছে। কেন্দ্রীয় স্টক থেকে পণ্য আউট এবং ডিপো স্টকে ইন হয়েছে।");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'ইনভয়েস অনুমোদনে ব্যর্থ: ' . $e->getMessage());
        }
    }
    
    /**
     * ইনভয়েস বাতিল করা (স্টক আউট হবে না)
     */
    public function cancel(Request $request, SalesInvoice $salesInvoice)
    {
        // ১. বাতিলের প্রাথমিক চেক
        if ($salesInvoice->depo_id !== Auth::user()->depo_id || $salesInvoice->status !== 'Pending') {
            return back()->with('error', 'এই ইনভয়েসটি বাতিল করার অনুমতি নেই বা এটি ইতিমধ্যেই অনুমোদিত/বাতিল করা হয়েছে।');
        }

        // ২. বাতিলের কারণের জন্য ভ্যালিডেশন
        $request->validate([
            'cancellation_reason' => 'required|string|max:500', 
        ], [
            'cancellation_reason.required' => 'ইনভয়েস বাতিল করার জন্য কারণটি অবশ্যই উল্লেখ করতে হবে।',
        ]);
        
        // ৩. স্ট্যাটাস আপডেট করা (বাতিল হলেও অনুমোদনের/বাতিলের তথ্য সংরক্ষণ করা)
        $salesInvoice->update([
            'status' => 'Canceled', 
            'cancellation_reason' => $request->input('cancellation_reason'),
            'approved_by' => Auth::id(), // কে বাতিল করল তার আইডি সংরক্ষণ
            'approved_at' => now(),      // বাতিলের সময় সংরক্ষণ
        ]);

        // **Redirect Return (সংশোধিত)**: বিস্তারিত পেজে নয়, Pending তালিকায় ফিরে যাওয়া যৌক্তিক
        return redirect()->route('depo.invoices.pending')->with('success', "ইনভয়েস #{$salesInvoice->invoice_no} সফলভাবে বাতিল করা হয়েছে।");
    }
}