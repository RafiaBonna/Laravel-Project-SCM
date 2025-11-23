<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'distributor_id', // ✅ CRITICAL: এটি যোগ করুন
        'name',
        'phone',
    ];

    /**
     * Customer belongs to a Distributor.
     */
    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}