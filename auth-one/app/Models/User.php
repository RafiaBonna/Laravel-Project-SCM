<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne; 
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // ✅ নতুন আমদানি

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // 'role', // ❌ OLD: role কলামটি আর fillable হবে না
        'status',
    ];

    // ... (অন্যান্য protected properties যেমন $hidden, $casts অপরিবর্তিত থাকবে)

    // =========================================================
    // ✅ NEW: RELATIONSHIPS FOR RBAC
    // =========================================================

    /**
     * User has many Roles (Many-to-Many).
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
    
    // =========================================================
    // ✅ NEW: RBAC HELPER FUNCTIONS
    // =========================================================

    /**
     * Checks if the user has a specific role.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }
    
    /**
     * Gets the primary role slug for redirection after login.
     * আমাদের অ্যাপ্লিকেশনে একটি ইউজার একটিই প্রাইমারি রোল পাবে।
     */
    public function getPrimaryRole(): string
    {
        // রোলগুলো Priority অনুযায়ী চেক করুন
        if ($this->hasRole('superadmin')) {
            return 'superadmin';
        }
        if ($this->hasRole('depo')) {
            return 'depo';
        }
        if ($this->hasRole('distributor')) {
            return 'distributor';
        }
        
        // কোনো নির্দিষ্ট রোল না পেলে 'user' ডিফল্ট হিসেবে ধরুন
        return 'user'; 
    }

    // ... (Optional: You can keep depo() and distributor() HasOne relations
    // as they are, for accessing Depo/Distributor-specific details)

    public function depo(): HasOne
    {
        return $this->hasOne(Depo::class);
    }
    
    public function distributor(): HasOne
    {
        return $this->hasOne(Distributor::class);
    }
}