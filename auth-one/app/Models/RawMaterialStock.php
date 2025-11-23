<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_id', 'batch_number', 'stock_quantity', 
        'average_purchase_price', 'last_in_date'
    ];
    
    // protected $guarded = []; // Fillable ব্যবহার করা হয়েছে, তাই এটি দরকার নেই

    public function rawMaterial() { 
        return $this->belongsTo(RawMaterial::class); 
    }

    // ✅ NEW: Production Issue Items (Stock Out)
    public function productionIssueItems()
    {
        // ProductionIssueItem মডেলে stock_id এর মাধ্যমে সম্পর্ক তৈরি করা হলো
        return $this->hasMany(ProductionIssueItem::class, 'raw_material_stock_id');
    }
}