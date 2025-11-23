<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;
    
    // ✅ Role Seeder-এর জন্য fillable কলাম
    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Role has many Users (Many-to-Many).
     */
    public function users(): BelongsToMany
    {
        // User মডেলে সম্পর্ক স্থাপন
        return $this->belongsToMany(User::class);
    }
}