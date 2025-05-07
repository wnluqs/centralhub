<?php

namespace Database\Seeders;

use App\Models\FTLT;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FTLTSeeder extends Seeder
{
    public function run(): void
    {
        // Get a random Technical user (make sure such user exists)
        $user = User::role('Technical')->inRandomOrder()->first();

        // Fallback if no user found
        if (!$user) {
            $user = User::first(); // fallback to any user
        }

        FTLT::create([
            'user_id'        => $user->id,
            'staff_id'      => 'V001', // Example staff ID, adjust as needed
            'zone'           => 'Kuantan',
            'location'       => 'Batu Gajah',
            'check_in_time'  => now(),
            'checkin_photo'  => 'ftlt_photos/sample.jpg',
            'notes'          => 'Seeder test entry',
            'check_out_time' => now(),
        ]);
    }
}
