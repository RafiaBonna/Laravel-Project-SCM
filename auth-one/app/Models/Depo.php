<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Depo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'location',
    ];

    // ✅ Depo belongs to a User (1-to-1)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    // ✅ Depo manages many Distributors (1-to-Many)
    public function distributors(): HasMany
    {
        return $this->hasMany(Distributor::class);
    }
}