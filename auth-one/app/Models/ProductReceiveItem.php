<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReceiveItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_receive_id',
        'product_id',
        'batch_no',
        'production_date',
        'expiry_date',
        'received_quantity', // The column name used in the store method (Fixed)
        'cost_rate',
        'mrp',
        'retail',
        'distributor',
        'depo_selling',
        'total_item_cost', // The cost calculated in the store method (Fixed)
    ];

    protected $casts = [
        'received_quantity' => 'float',
        'cost_rate' => 'float',
        'mrp' => 'float',
        'retail' => 'float',
        'distributor' => 'float',
        'depo_selling' => 'float',
        'total_item_cost' => 'float',
    ];

    public function receive()
    {
        return $this->belongsTo(ProductReceive::class, 'product_receive_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}