<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionIssueItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_issue_id', 'raw_material_id', 'raw_material_stock_id', 
        'batch_number', 'quantity_issued', 'unit_cost', 'total_cost'
    ];
    
    public function issue()
    {
        return $this->belongsTo(ProductionIssue::class, 'production_issue_id');
    }
    
    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
    
    public function stock()
    {
        // RawMaterialStock মডেলটি প্রয়োজন হবে
        return $this->belongsTo(RawMaterialStock::class, 'raw_material_stock_id');
    }
    
    public function productReceive()
    {
        return $this->belongsTo(ProductReceive::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}