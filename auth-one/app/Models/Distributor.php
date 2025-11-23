<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Distributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'depo_id', // Foreign key to depos table
        'name',
        'location',
    ];

    // ✅ Distributor belongs to a User (1-to-1)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    // ✅ Distributor belongs to a Depo (Many-to-1)
    public function depo(): BelongsTo
    {
        return $this->belongsTo(Depo::class);
    }
    
    // ✅ Distributor has many Customers (1-to-Many)
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}