<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            // ✅ শুধু Super Admin রাখা হলো
            ['name' => 'Super Admin', 'slug' => 'superadmin', 'description' => 'Full access to the system.'],
            ['name' => 'Depo Manager', 'slug' => 'depo', 'description' => 'Depo level stock and distributor management.'],
            ['name' => 'Distributor', 'slug' => 'distributor', 'description' => 'Manages sales and customers.'],
            // ❌ Admin রোল বাদ দেওয়া হলো
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}