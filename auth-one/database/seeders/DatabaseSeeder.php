<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ CRITICAL: RoleSeeder অবশ্যই UserSeeder-এর আগে কল করতে হবে।
        $this->call(RoleSeeder::class); 
        $this->call(UserSeeder::class);
    }
}