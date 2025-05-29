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
            ['name' => 'Zone Machang', 'branch' => 'Machang'],

            // Kuantan (from Excel)
            'BALOK BARAT',
            'BUKIT SEKILAU',
            'BUKIT SETONGKOL',
            'CENDERAWASIH',
            'GEBENG',
            'HAJI AHMAD',
            'INDERA MAHKOTA (KELOMPOK 1)',
            'INDERA MAHKOTA (KELOMPOK 2)',
            'INDERA MAHKOTA (KELOMPOK 3)',
            'INDERA MAHKOTA (KELOMPOK 4)',
            'INDERA MAHKOTA (KELOMPOK 5)',
            'INDERA MAHKOTA (KELOMPOK 6)',
            'INDERA SEMPURNA',
            'INDERAPURA',
            'JALAN AIR PUTIH DAN SEMAMBU',
            'JALAN BESERAH',
            'JALAN BUKIT UBI',
            'JALAN DATO LIM HOE LEK',
            'JALAN DATO WONG AH JANG',
            'JALAN GAMBANG (JAYA GADING)',
            'JALAN GAMBANG (SERI DAMAI BATU 6)',
            'JALAN GAMBANG (TAMAN TAS)',
            'JALAN PENJARA',
            'KOD KAWASAN',
            'MAKTAB ADABI',
            'NIRWANA BATU 3 (COMMERCIAL CENTRE)',
            'PANDAN PERDANA',
            'PANDAN SEJAHTERA',
            'PDG. MBK',
            'SERI DAMAI AMAN',
            'SERI DAMAI PERDANA',
            'SERI FAJAR',
            'STADIUM',
            'SUNGAI ISAP',
            'SUNGAI LEMBING (KELOMPOK 1)',
            'SUNGAI LEMBING (KELOMPOK 2)',
            'SUNGAI LEMBING (KELOMPOK 3)',
            'SUNGAI LEMBING (KELOMPOK 4)',
            'TAMAN GURU',
            'TANAH PUTIH (PERODUA)',
            'TANAH PUTIH BARU (PETRONAS)',
            'TANJUNG LUMPUR',
            'TELUK SISEK',

            // Kuala Terengganu
            ['name' => 'Zone A', 'branch' => 'Kuala Terengganu'],
            ['name' => 'Zone B', 'branch' => 'Kuala Terengganu'],
            ['name' => 'Zone C', 'branch' => 'Kuala Terengganu'],
            ['name' => 'Zone D', 'branch' => 'Kuala Terengganu'],
            ['name' => 'Zone E', 'branch' => 'Kuala Terengganu'],
            ['name' => 'Zone F', 'branch' => 'Kuala Terengganu'],
        ];

        foreach ($zones as $zone) {
            if (is_array($zone)) {
                // Already has name and branch
                Zone::firstOrCreate([
                    'name' => $zone['name'],
                    'branch' => $zone['branch'],
                ]);
            } else {
                // Plain string zone (assumed Kuantan)
                Zone::firstOrCreate([
                    'name' => $zone,
                    'branch' => 'Kuantan',
                ]);
            }
        }
    }
}
