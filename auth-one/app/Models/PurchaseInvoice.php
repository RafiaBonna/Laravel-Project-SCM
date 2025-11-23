<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number', 'supplier_id', 'invoice_date', 'sub_total',
        'discount_amount', 'grand_total', 'notes', 'user_id'
    ];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(RawMaterialPurchaseItem::class, 'purchase_invoice_id'); }
}