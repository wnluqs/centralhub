<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create department roles
        Role::firstOrCreate(['name' => 'HR']);
        Role::firstOrCreate(['name' => 'Operations']);
        Role::firstOrCreate(['name' => 'ControlCenter']);
        Role::firstOrCreate(['name' => 'Technical']);
        Role::firstOrCreate(['name' => 'Accounting']);
        Role::firstOrCreate(['name' => 'Secretary']);

        // Create admin role
        Role::firstOrCreate(['name' => 'Admin']);
        // NEW: Create TechnicalLead role
        Role::firstOrCreate(['name' => 'TechnicalLead']);
    }
}
