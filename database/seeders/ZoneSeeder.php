<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            // Machang
            ['name' => 'Pasir Puteh', 'branch' => 'Machang'],
            ['name' => 'Labok', 'branch' => 'Machang'],
            ['name' => 'Ulu Sat', 'branch' => 'Machang'],

            // Kuantan (from Excel)
            ['name' => 'Air Putih', 'branch' => 'Kuantan'],
            ['name' => 'Alor Akar', 'branch' => 'Kuantan'],
            ['name' => 'Astana Permai', 'branch' => 'Kuantan'],
            ['name' => 'Balok', 'branch' => 'Kuantan'],
            ['name' => 'Bandar Damansara', 'branch' => 'Kuantan'],
            ['name' => 'Bandar Indera Mahkota', 'branch' => 'Kuantan'],
            ['name' => 'Bandar Kuantan Putri', 'branch' => 'Kuantan'],
            ['name' => 'Bandar Putra', 'branch' => 'Kuantan'],
            ['name' => 'Batu 3', 'branch' => 'Kuantan'],
            ['name' => 'Bukit Rangin Perdana', 'branch' => 'Kuantan'],
            ['name' => 'Bukit Sekilau', 'branch' => 'Kuantan'],
            ['name' => 'Bukit Setongkol', 'branch' => 'Kuantan'],
            ['name' => 'Cenderawasih', 'branch' => 'Kuantan'],
            ['name' => 'Cenderawasih Baru', 'branch' => 'Kuantan'],
            ['name' => 'Chengal Lempong', 'branch' => 'Kuantan'],
            ['name' => 'Dato Bahaman', 'branch' => 'Kuantan'],
            ['name' => 'Gambang', 'branch' => 'Kuantan'],
            ['name' => 'Indera Mahkota 1', 'branch' => 'Kuantan'],
            ['name' => 'Indera Mahkota 2', 'branch' => 'Kuantan'],
            ['name' => 'Indera Mahkota 3', 'branch' => 'Kuantan'],
            ['name' => 'Indera Mahkota 5', 'branch' => 'Kuantan'],
            ['name' => 'Indera Mahkota 6', 'branch' => 'Kuantan'],
            ['name' => 'Indera Mahkota 7', 'branch' => 'Kuantan'],
            ['name' => 'Indera Mahkota 8', 'branch' => 'Kuantan'],
            ['name' => 'Indera Mahkota 9', 'branch' => 'Kuantan'],
            ['name' => 'Indera Mahkota 10', 'branch' => 'Kuantan'],
            ['name' => 'Indera Mahkota 11', 'branch' => 'Kuantan'],
            ['name' => 'Jalan Beserah', 'branch' => 'Kuantan'],
            ['name' => 'Jalan Bukit Sekilau', 'branch' => 'Kuantan'],
            ['name' => 'Jalan Haji Ahmad', 'branch' => 'Kuantan'],
            ['name' => 'Jalan Kuantan - Gambang', 'branch' => 'Kuantan'],
            ['name' => 'Jalan Tun Ismail', 'branch' => 'Kuantan'],
            ['name' => 'Kg Jawa', 'branch' => 'Kuantan'],
            ['name' => 'Kg Tengah', 'branch' => 'Kuantan'],
            ['name' => 'Kota SAS', 'branch' => 'Kuantan'],
            ['name' => 'Kuantan Perdana', 'branch' => 'Kuantan'],
            ['name' => 'Pantai Sepat', 'branch' => 'Kuantan'],
            ['name' => 'Permatang Badak', 'branch' => 'Kuantan'],
            ['name' => 'Permatang Badak Baru', 'branch' => 'Kuantan'],
            ['name' => 'Perumahan Pelindung Aman', 'branch' => 'Kuantan'],
            ['name' => 'Semambu', 'branch' => 'Kuantan'],
            ['name' => 'Taman Guru', 'branch' => 'Kuantan'],
            ['name' => 'Taman Impian', 'branch' => 'Kuantan'],

            // Kuala Terengganu
            ['name' => 'Batu Buruk', 'branch' => 'Kuala Terengganu'],
            ['name' => 'Seberang Takir', 'branch' => 'Kuala Terengganu'],
            ['name' => 'Kuala Ibai', 'branch' => 'Kuala Terengganu'],
        ];

        foreach ($zones as $zone) {
            Zone::firstOrCreate([
                'name' => $zone['name'],
                'branch' => $zone['branch'],
            ]);
        }
    }
}
