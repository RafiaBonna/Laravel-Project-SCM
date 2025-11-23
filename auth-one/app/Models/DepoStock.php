<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepoStock extends Model
{
    use HasFactory;
    
    // ⚠️ এই টেবিলটি Depo-এর হাতে থাকা পণ্যের মোট পরিমাণ রাখবে
    // Depo-এর স্টকে পণ্য যোগ করার জন্য এই মডেলটি ব্যবহার হবে।
    
    protected $fillable = [
        'depo_id',
        'product_id',
        'quantity',
        'batch_no', // কেন্দ্রীয় স্টক থেকে যে ব্যাচ এসেছে
    ];

    // ডিপো যার স্টক
    public function depo()
    {
        return $this->belongsTo(Depo::class);
    }

    // পণ্য
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}