<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Terminal;
use App\Models\TerminalParking;

class TerminalParkingSeeder extends Seeder
{
    public function run(): void
    {
        $terminals = Terminal::all();

        foreach ($terminals as $terminal) {
            TerminalParking::firstOrCreate([
                'terminal_id' => $terminal->id,
            ], [
                'branch'    => $terminal->branch ?? 'Unknown',
                'status'    => $terminal->status ?? 'Inactive',
                'location'  => $terminal->location ?? 'No Location',
                'latitude'  => $terminal->latitude ?? 0,
                'longitude' => $terminal->longitude ?? 0,
            ]);
        }
    }
}
