<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            ZoneSeeder::class,
            RoadSeeder::class,
            TerminalSeeder::class,          // <-- Must run BEFORE
            TerminalParkingSeeder::class,  // <-- Safe to run after TerminalSeeder
        ]);
    }
}
