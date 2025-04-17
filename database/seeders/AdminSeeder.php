<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Make sure Admin role exists
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // Check if admin already exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@vista.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('admin1234'), // You can change this securely
            ]
        );

        // Assign role if not already assigned
        if (!$admin->hasRole('Admin')) {
            $admin->assignRole($adminRole);
        }
    }
}

