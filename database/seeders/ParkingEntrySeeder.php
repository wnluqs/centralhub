<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParkingEntry;
use Carbon\Carbon;

class ParkingEntrySeeder extends Seeder
{
    public function run()
    {
        $statuses = ['Paid', 'Unpaid', 'Pending Payment'];
        $spots    = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];

        for ($i = 0; $i < 20; $i++) {
            ParkingEntry::create([
                'license_plate' => 'ABC' . rand(100, 999),
                'parking_spot'  => $spots[array_rand($spots)],
                // Random entry time within the last 24 hours
                'entry_time'    => Carbon::now()->subMinutes(rand(0, 1440)),
                // 50% chance of exit_time being NULL or random
                'exit_time'     => rand(0, 1) ? null : Carbon::now()->subMinutes(rand(0, 1440)),
                // Random status
                'status'        => $statuses[array_rand($statuses)],
            ]);
        }
    }
}
