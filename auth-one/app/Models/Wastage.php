<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wastage extends Model
{
    use HasFactory;

    protected $fillable = [
        'wastage_date',
        'raw_material_id',
        'raw_material_stock_id',
        'batch_number',
        'quantity_wasted',
        'unit_cost',
        'total_cost',
        'reason',
        'user_id',
    ];
    
    // Relationships
    
    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
    
    public function stock()
    {
        return $this->belongsTo(RawMaterialStock::class, 'raw_material_stock_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}