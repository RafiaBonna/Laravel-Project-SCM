<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialPurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_invoice_id', 'raw_material_id', 'batch_number', 
        'quantity', 'unit_price', 'total_price'
    ];

    public function invoice() { return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id'); }
    public function rawMaterial() { return $this->belongsTo(RawMaterial::class); }
}