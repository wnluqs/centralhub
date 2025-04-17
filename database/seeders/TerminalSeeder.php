<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Terminal;

class TerminalSeeder extends Seeder
{
    public function run()
    {
        Terminal::insert([
            ['id' => 'VS001', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'UK001', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'BA001', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'VS002', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'UK002', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'BA002', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
