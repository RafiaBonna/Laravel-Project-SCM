<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    use HasFactory;
    
    // আপনি মাইগ্রেশনে product_stock_id field যোগ করেছেন
    public $timestamps = false; // সাধারণত আইটেম টেবিলে timestamps লাগে না
    
    protected $fillable = [
        'sales_invoice_id',
        'product_id',
        'product_stock_id', // ⚠️ কেন্দ্রীয় স্টকের ব্যাচ ID
        'quantity',
        'unit_price',
        'sub_total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'sub_total' => 'decimal:2',
    ];

    // রিলেশনশিপস

    // যে ইনভয়েসের অংশ
    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    // পণ্য
    public function product()
    {
        // ⚠️ ধরে নেওয়া হচ্ছে 'Product' মডেল আছে
        return $this->belongsTo(Product::class);
    }
    
    // যে ব্যাচ/স্টক থেকে বিক্রি হয়েছে
    public function stock()
    {
        // ⚠️ স্টক আউট করার জন্য এই রিলেশনশিপটি খুব গুরুত্বপূর্ণ
        return $this->belongsTo(ProductStock::class, 'product_stock_id');
    }
}