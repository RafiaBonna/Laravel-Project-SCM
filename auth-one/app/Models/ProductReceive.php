<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReceive extends Model
{
    use HasFactory;

    protected $fillable = [
        'receive_no',
        'receive_date',
        'note',
        'total_received_qty',
        'receiver_id',
        'total_cost',
        'received_by_user_id',
    ];

    // The user who is recorded as the receiver
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // The user who actually entered the receive in the system
    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    // Relation with items
    public function items()
    {
        return $this->hasMany(ProductReceiveItem::class);
    }
    public function user()
{
    return $this->belongsTo(User::class, 'received_by_user_id');
}
}
