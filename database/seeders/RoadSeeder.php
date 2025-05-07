<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Road;
use App\Models\Zone;

class RoadSeeder extends Seeder
{
    public function run()
    {
        $zoneMap = Zone::where('branch', 'Kuantan')->pluck('id', 'name');

        $roadsByZone = [
            'Air Putih' => ['Jalan Air Putih', 'Lorong Air Putih 1', 'Lorong Air Putih 2'],
            'Alor Akar' => ['Jalan Alor Akar', 'Lorong Alor Akar 3', 'Lorong Alor Akar 16'],
            'Astana Permai' => ['Jalan Astana Permai 1'],
            'Balok' => ['Jalan Balok Makmur', 'Lorong Balok Makmur 5'],
            'Bandar Damansara' => ['Jalan Damansara 1', 'Jalan Damansara 2'],
            'Bandar Indera Mahkota' => ['Lorong IM 3/20', 'Lorong IM 3/21'],
            'Bandar Kuantan Putri' => ['Jalan Kuantan Putri 1'],
            'Bandar Putra' => ['Jalan Putra 1'],
            'Batu 3' => ['Jalan Batu 3'],
            'Bukit Rangin Perdana' => ['Lorong BRP 1', 'Lorong BRP 2'],
            'Bukit Sekilau' => ['Lorong Sekilau 11', 'Lorong Sekilau 18'],
            'Bukit Setongkol' => ['Jalan Setongkol', 'Lorong Setongkol 2'],
            'Cenderawasih' => ['Lorong Cenderawasih 2'],
            'Cenderawasih Baru' => ['Lorong Cenderawasih Baru 5'],
            'Chengal Lempong' => ['Jalan Chengal Lempong 1'],
            'Dato Bahaman' => ['Lorong Dato Bahaman 3'],
            'Gambang' => ['Jalan Gambang', 'Lorong Gambang 1'],
            'Indera Mahkota 1' => ['Lorong IM 1/11', 'Lorong IM 1/17'],
            'Indera Mahkota 2' => ['Lorong IM 2/3'],
            'Indera Mahkota 3' => ['Lorong IM 3/16'],
            'Indera Mahkota 5' => ['Lorong IM 5/1'],
            'Indera Mahkota 6' => ['Lorong IM 6/1'],
            'Indera Mahkota 7' => ['Lorong IM 7/2'],
            'Indera Mahkota 8' => ['Lorong IM 8/1'],
            'Indera Mahkota 9' => ['Lorong IM 9/1'],
            'Indera Mahkota 10' => ['Lorong IM 10/1'],
            'Indera Mahkota 11' => ['Lorong IM 11/1'],
            'Jalan Beserah' => ['Jalan Beserah', 'Lorong Beserah 2'],
            'Jalan Bukit Sekilau' => ['Jalan Bukit Sekilau'],
            'Jalan Haji Ahmad' => ['Jalan Haji Ahmad'],
            'Jalan Kuantan - Gambang' => ['Jalan Kuantan - Gambang'],
            'Jalan Tun Ismail' => ['Jalan Tun Ismail 1'],
            'Kg Jawa' => ['Lorong Kg Jawa 2'],
            'Kg Tengah' => ['Lorong Kg Tengah 1'],
            'Kota SAS' => ['Lorong SAS 1'],
            'Kuantan Perdana' => ['Jalan Kuantan Perdana 1'],
            'Pantai Sepat' => ['Jalan Pantai Sepat 1'],
            'Permatang Badak' => ['Jalan Permatang Badak'],
            'Permatang Badak Baru' => ['Jalan Permatang Badak Baru 1'],
            'Perumahan Pelindung Aman' => ['Jalan Pelindung Aman 1'],
            'Semambu' => ['Jalan Semambu', 'Lorong Semambu 3'],
            'Taman Guru' => ['Lorong Taman Guru 2'],
            'Taman Impian' => ['Jalan Impian 1'],
        ];

        foreach ($roadsByZone as $zone => $roads) {
            foreach ($roads as $road) {
                Road::firstOrCreate([
                    'name' => $road,
                    'zone_name' => $zone,
                ]);
            }
        }
    }
}
