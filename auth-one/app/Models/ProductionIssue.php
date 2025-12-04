<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionIssue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * * আপনার RawMaterialStockOutController থেকে আসা কলামগুলো এখানে যোগ করা হয়েছে।
     *
     * @var array
     */
    protected $fillable = [
        'issue_number',
        'factory_name',
        'issue_date',
        'user_id',
        'notes',
        'total_quantity_issued',
        'total_issue_cost',
    ];

    
    // Accessor for index.blade.php's `slip_number` column
    public function getSlipNumberAttribute()
    {
        return $this->issue_number;
    }
    
    // Accessor for index.blade.php's `Issued To` column (using factory_name)
    public function getIssuedToAttribute()
    {
        return $this->factory_name;
    }

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function items()
    {
        return $this->hasMany(ProductionIssueItem::class);
    }
}