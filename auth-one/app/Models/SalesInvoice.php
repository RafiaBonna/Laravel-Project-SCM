<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'invoice_date',
        'total_amount',
        'user_id', // Super Admin যে তৈরি করেছে
        'depo_id', // যে ডিপোর কাছে বিক্রি করা হয়েছে
        'status',
        'approved_by', // Depo-এর যে ইউজার Approve করেছে
        'approved_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'approved_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    // রিলেশনশিপস

    // ইনভয়েসের আইটেমসমূহ (একাধিক পণ্য)
    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    // ইনভয়েস তৈরি করেছে যে ইউজার (Super Admin)
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ইনভয়েসটি যার কাছে বিক্রি করা হয়েছে
    public function depo()
    {
        // ⚠️ ধরে নেওয়া হচ্ছে 'Depo' মডেল আছে
        return $this->belongsTo(Depo::class, 'depo_id');
    }
    
    // অনুমোদন/বাতিল করেছে যে ইউজার (Depo User)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}