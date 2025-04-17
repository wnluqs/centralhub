<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TerminalParkingSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'number'    => 'TP-1001',
                'status'             => 'Active',
                'zone_code'          => 'Z-01',
                'last_communication' => '2025-03-24 14:30:00',
                'location'           => 'Downtown Terminal',
                'latitude'           => 40.712776,
                'longitude'          => -74.005974,
                'battery_health'     => 'Good',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'number'    => 'TP-1002',
                'status'             => 'Inactive',
                'zone_code'          => 'Z-02',
                'last_communication' => '2025-03-20 08:15:00',
                'location'           => 'City Center Terminal',
                'latitude'           => 34.052235,
                'longitude'          => -118.243683,
                'battery_health'     => 'Low',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'number'    => 'TP-1003',
                'status'             => 'Active',
                'zone_code'          => 'Z-03',
                'last_communication' => '2025-03-23 18:45:00',
                'location'           => 'Suburb North Terminal',
                'latitude'           => 51.507351,
                'longitude'          => -0.127758,
                'battery_health'     => 'Excellent',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'number'    => 'TP-1004',
                'status'             => 'Maintenance',
                'zone_code'          => 'Z-04',
                'last_communication' => '2025-03-22 12:00:00',
                'location'           => 'Airport Terminal',
                'latitude'           => 35.689487,
                'longitude'          => 139.691711,
                'battery_health'     => 'Critical',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'number'    => 'TP-1005',
                'status'             => 'Active',
                'zone_code'          => 'Z-05',
                'last_communication' => '2025-03-24 07:50:00',
                'location'           => 'Industrial Park Terminal',
                'latitude'           => 48.856613,
                'longitude'          => 2.352222,
                'battery_health'     => 'Good',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ];

        DB::table('terminal_parkings')->insert($data);
    }
}
