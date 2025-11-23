<?php
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'unit',
        
        // 🎯 নতুন যোগ করা রেট ফিল্ডগুলো (Controller-এ ব্যবহৃত)
        'mrp',
        'retail_rate',
        'depo_selling_price',
        'distributor_rate',
        
        'current_stock',
        'description',
        'is_active',
        'created_by',
    ];

    /**
     * একটি প্রোডাক্টের সমস্ত স্টক এন্ট্রি।
     */
    public function stocks(): HasMany
    {
        // নিশ্চিত করুন যে ProductStock মডেলটি আছে
        return $this->hasMany(ProductStock::class);
    }

    /**
     * এই প্রোডাক্টটি যিনি তৈরি করেছেন।
     */
    public function creator(): BelongsTo
    {
        // নিশ্চিত করুন যে User মডেলটি আছে
        return $this->belongsTo(User::class, 'created_by');
    }
}