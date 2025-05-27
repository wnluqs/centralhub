<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Terminal;

class TerminalSeeder extends Seeder
{
    public function run()
    {
        $terminals = [];

        // KUANTAN: KN1A01 - KN9A99
        for ($i = 1; $i <= 9; $i++) {
            for ($j = 1; $j <= 99; $j++) {
                $terminals[] = [
                    'id' => sprintf('KN%dA%02d', $i, $j),
                    'branch' => 'Kuantan',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // KUANTAN: KN1B01 - KN1B64
        for ($j = 1; $j <= 64; $j++) {
            $terminals[] = [
                'id' => sprintf('KN1B%02d', $j),
                'branch' => 'Kuantan',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // MACHANG: M01A01 - M01A49
        for ($j = 1; $j <= 49; $j++) {
            $terminals[] = [
                'id' => sprintf('M01A%02d', $j),
                'branch' => 'Machang',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // KUALA TERENGGANU multiple prefixes
        $ktPrefixes = [
            ['prefix' => 'N01A', 'count' => 37],
            ['prefix' => 'P01A', 'count' => 34],
            ['prefix' => 'T01A', 'count' => 88],
            ['prefix' => 'STR', 'count' => 177],
        ];

        foreach ($ktPrefixes as $set) {
            for ($j = 1; $j <= $set['count']; $j++) {
                $terminals[] = [
                    'id' => sprintf('%s%02d', $set['prefix'], $j),
                    'branch' => 'Kuala Terengganu',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Terminal::insert($terminals);
    }
}
