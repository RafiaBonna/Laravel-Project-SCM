<?php

namespace Database\Seeders;

use App\Models\Role; 
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // সমস্ত রোলের ডেটাবেস আইডিগুলি লোড করুন
        $roles = Role::pluck('id', 'slug');

        // Super Admin User
        $superadmin = User::create([
            'name' => 'SuperAdmin', 
            'email' => 'sup@gmail.com', // Superadmin-এর ইমেইল
            'status' => 'active',
            'password' => Hash::make('1234'),
        ]);
        $superadmin->roles()->attach($roles['superadmin']); 

        // Depo User
        $depo = User::create([
            'name' => 'Depot',
            'email' => 'depo@gmail.com',
            'status' => 'active',
            'password' => Hash::make('1234'),
        ]);
        $depo->roles()->attach($roles['depo']); 

        // Distributor User
        $distributor = User::create([
            'name' => 'Distributor',
            'email' => 'dist@gmail.com',
            'status' => 'active',
            'password' => Hash::make('1234'),
        ]);
        $distributor->roles()->attach($roles['distributor']); 
        
        // ❌ admin@gmail.com ইউজারটি এখানে তৈরি হবে না।
    }
}