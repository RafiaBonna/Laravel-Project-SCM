<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_number', 'factory_name', 'issue_date', 'user_id', 
        'total_quantity_issued', 'total_issue_cost', 'notes'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function items()
    {
        return $this->hasMany(ProductionIssueItem::class);
    }
}