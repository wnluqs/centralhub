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
        // $this->call(ComplaintSeeder::class);  ← optional, comment if needed
        // Other seeders...
        $this->call(TerminalParkingSeeder::class);
        $this->call(TerminalSeeder::class);
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            ZoneSeeder::class,
            RoadSeeder::class,
        ]);
    }
}
