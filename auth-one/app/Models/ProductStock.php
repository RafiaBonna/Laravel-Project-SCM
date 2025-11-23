<?php

// app/Models/ProductStock.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // <--- এটি যোগ করা হয়েছে

class ProductStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_no',
        'expiry_date',
        'available_quantity', // ⚠️ আপনার ফিল্ডটি 'available_quantity' হলে এখানে সেই নামটি ব্যবহার করা হয়েছে
    ];

    /**
     * এই স্টকটি কোন প্রোডাক্টের।
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * এই স্টক থেকে যে ইনভয়েস আইটেমগুলো তৈরি হয়েছে (Stock Out Traceability).
     * ⚠️ SalesInvoiceItem মডেলে foreign key হিসেবে 'product_stock_id' ব্যবহার করা হয়েছে।
     */
    public function salesItems(): HasMany 
    {
        return $this->hasMany(SalesInvoiceItem::class, 'product_stock_id');
    }
}