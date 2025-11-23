<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit_of_measure',
        'description', // ✅ নিশ্চিত করা হলো
    ];
    
    public function purchaseItems()
    {
        return $this->hasMany(RawMaterialPurchaseItem::class);
    }

    /**
     * কাঁচামালের সাথে সংশ্লিষ্ট সমস্ত স্টক/ব্যাচ (RawMaterialStock) রিলেশনশিপ।
     * এই মেথডটি RawMaterialStockOutController-এর ত্রুটি ঠিক করবে।
     */
    public function stocks()
    {
        return $this->hasMany(RawMaterialStock::class);
    }
}