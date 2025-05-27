<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Road;
use App\Models\Zone;

class RoadSeeder extends Seeder
{
    public function run()
    {
        $zoneMap = Zone::pluck('id', 'name');

        $roadsByZone = [ // Machang roads (example block, append to your full list)
            'Zone Machang' => [
                'Jalan Bahagia',
                'Jalan Bakat',
                'Jalan Bakti',
                "Jalan Dato' Hashim",
                'Jalan HilirPasar',
                'Jalan Kemahkotaan',
                'Jalan Kenanga',
                'Jalan Kuala Krai (Stesen Bas)',
                'Jalan Kweng Umat',
                'Jalan Melati',
                'Jalan Padang',
                'Jalan Pasir Puteh',
                'Jalan Pejabat',
                'Jalan Penerangan',
                'Jalan Tanjung',
                'Jalan Tok Kemuning',
                'Lorong Belakang Pos',
                'Lorong Belakang Wisma Pas Machang',
                'Lorong Hijab',
            ],
            // Kuala Terengganu zones
            'Zone A' => [
                'Jalan Kampung Cina',
                'Pesisir Payang',
                'Jalan Sultan Zainal Abidin',
            ],
            'Zone B' => [
                'Jalan Banggol',
                'Jalan Kampung Tiong',
                'Jalan Sultan Ismail',
                'Jalan Kota Lama',
                'Jalan Engku Pengiran Anom 2',
                'Jalan Air Jernih',
                'Jalan Engku Sar',
                'Jalan Balik Bukit',
                'Kampung Daik',
            ],
            'Zone C' => [
                'Kampung Daik',
                'Jalan Kota',
                'Jalan Masjid Abidin',
                'Jalan Data Isaacs',
                'Jalan Tok Lam',
                'Jalan Pejabat',
                'Jalan Petani',
                'Jalan Sultan Mahmud',
                'Jalan Sultan Omar',
                'Jalan Sultan Sulaiman',
            ],
            'Zone D' => [
                'Jalan Pejabat',
                'Jalan Tengku Ampuan Mariam',
                'Jalan Pusara',
                'Jalan Sultan Sulaiman',
                'Jalan Kemajuan',
                'Jalan Bukit Kechil',
            ],
            'Zone E' => [
                'Jalan Hiliran',
                'Jalan Sultan Mohamad',
                'Cabang Tiga',
            ],
            'Zone F' => [
                'Dataran Austin',
                'Jalan Gong Pak Damat',
                'Kuala Nerus',
            ],
            // Kuantan roads
            'BALOK BARAT' => ['LORONG BALOK BARAT 13', 'LORONG BALOK BARAT 14', 'LORONG BALOK BARAT 15', 'LORONG BALOK BARAT 16', 'JALAN BALOK PERMAI 2'],
            'BUKIT SEKILAU' => ['JALAN BUKIT SEKILAU', 'LORONG SEKILAU 1'],
            'BUKIT SETONGKOL' => ['JALAN BUKIT SETONGKOL', 'JALAN BUKIT SETONGKOL 8', 'LORONG BUKIT SETONGKOL 53', 'LORONG BUKIT SETONGKOL 56', 'LORONG BUKIT SETONGKOL MAJU 13', 'LORONG BUKIT SETONGKOL MAJU 14', 'LORONG BUKIT SETONGKOL MAJU 18', 'LORONG BUKIT SETONGKOL JAYA 108', 'LORONG BUKIT SETONGKOL PERDANA 1'],
            'CENDERAWASIH' => ['JALAN PAYA TIGA'],
            'GEBENG' => ['JALAN GEBENG 2/6', 'JALAN GEBENG 2/7', 'JALAN GEBENG 2/8'],
            'HAJI AHMAD ' => ['JALAN BESERAH', 'JALAN SRI SETALI 122', 'JALAN HAJI AHMAD', 'JALAN HAJI AHMAD 1', 'JALAN HAJI AHMAD 2', 'JALAN HAJI AHMAD 3', 'JALAN HAJI AHMAD 4', 'JALAN HAJI AHMAD 5', 'JALAN HAJI AHMAD 6', 'JALAN HAJI AHMAD 7', 'JALAN HAJI AHMAD 9', 'LORONG HAJI AHMAD 10', 'LORONG GALING 2', 'LORONG GALING 6'],
            'INDERA MAHKOTA (KELOMPOK 1)' => ['JALAN SEKILAU 1', 'JALAN BUKIT UBI', 'JALAN  IM 8/33', 'JALAN IM 7/1', 'JALAN IM 7/2', 'JALAN IM 7/3', 'JALAN IM 7/4', 'JALAN IM 7/5', 'JALAN IM 7/15', 'JALAN IM 7/16', 'JALAN IM 7/18', 'JALAN IM 7/19'],
            'INDERA MAHKOTA (KELOMPOK 2)' => ['JALAN INDERA MAHKOTA 2', 'JALAN IM 2/4', 'JALAN IM 2/5', 'JALAN IM 2/6'],
            'INDERA MAHKOTA (KELOMPOK 3)' => ['LORONG IM 5/2', 'JALAN IM 5/1'],
            'INDERA MAHKOTA (KELOMPOK 4)' => ['JALAN IM 3/10', 'JALAN IM 3/11', 'JALAN IM 3/12', 'JALAN IM 3/13', 'LORONG IM 3/19', 'LORONG IM 16/1'],
            'INDERA MAHKOTA (KELOMPOK 5)' => ['LORONG IM 15/18', 'LORONG IM 15/19', 'LORONG IM 14/1', 'LORONG IM 14/2', 'LORONG IM 14/30', 'LORONG IM 14/31', 'LORONG IM 14/32'],
            'INDERA MAHKOTA (KELOMPOK 6)' => ['LORONG SHAHZAN IM 3'],
            'INDERA SEMPURNA' => ['LORONG IS 102', 'LORONG IS 103', 'JALAN INDERA SEMPURNA 1/2', 'JALAN INDERA SEMPURNA 1/3', 'JALAN INDERA SEMPURNA 1/4', 'LORONG IS 1', 'LORONG IS 2'],
            'INDERAPURA' => ['JALAN SERI INDERAPURA', 'LORONG SERI INDERPURA 8', 'LORONG SERI INDERAPURA 8/1', 'LORONG SERI INDERAPURA 9'],
            'JALAN AIR PUTIH DAN SEMAMBU' => ['JALAN AIR PUTIH', 'JALAN AIR PUTIH 5', 'JALAN AIR PUTIH 6', 'JALAN SERI SETALI 1', 'JALAN SERI SETALI 2', 'LORONG SERI SETALI 112', 'LORONG SERI SETALI 113/1', 'LORONG AIR PUTIH 128', 'LORONG AIR PUTIH 130', 'LORONG SEMAMBU BARU 42'],
            'JALAN BESERAH' => ['JALAN BESERAH', 'LORONG AIR PUTIH 2', 'JALAN SRI KUANTAN 65', 'JALAN SRI KUANTAN 79', 'JALAN SRI KUANTAN 80', 'JALAN SRI KUANTAN 81', 'JALAN ALOR AKAR', 'JALAN KUBANG BUAYA', 'JALAN SEMAMBU BARU 2'],
            'JALAN BUKIT UBI' => ['JALAN BUKIT UBI', 'LORONG SERI TERUNTUM 139'],
            'JALAN DATO LIM HOE LEK' => ['JALAN DATO LIM HOE LEK'],
            'JALAN DATO WONG AH JANG' => ['JALAN DATO WONG AH JANG', 'JALAN TANAH PUTIH', 'JALAN MAT KILAU ', 'JALAN MAT KILAU 1', 'LORONG MAT KILAU 1/1', 'LORONG MAT KILAU 1/2', 'LORONG MAT KILAU 16', 'JALAN SERI TERUNTUM 3', 'LORONG SERI TERUNTUM 85', 'JALAN DARAT MAKBAR'],
            'JALAN GAMBANG (JAYA GADING)' => ['LORONG JAYA GADING 11', 'LORONG JAYA GADING 12', 'LORONG JAYA GADING 13', 'LORONG JAYA GADING 14', 'PADANG PARKIR PASARAYA ALFA', 'LORONG JAYA GADING 18', 'LORONG JAYA GADING 28', 'LORONG JAYA GADING 29', 'LORONG JAYA GADING 30', 'LORONG JAYA GADING 31', 'LORONG JAYA GADING 32', 'LORONG JAYA GADING 33', 'LORONG JAYA GADING 34', 'JALAN JAYA GADING', 'JALAN JAYA GADING 1', 'JALAN GAMBANG'],
            'JALAN GAMBANG (SERI DAMAI BATU 6)' => ['JALAN TANAH PUTIH', 'JALAN SERI DAMAI PERDANA 2', 'LORONG SERI DAMAI PERDANA 2', 'LORONG SERI DAMAI PERDANA 5'],
            'JALAN GAMBANG (TAMAN TAS)' => ['JALAN GAMBANG', 'LORONG PANDAN DAMAI 2/201', 'LORONG PANDAN DAMAI 2/5', 'LORONG PANDAN DAMAI 2/4', 'LORONG PANDAN DAMAI 2/3', 'LORONG PANDAN DAMAI 2/2', 'JALAN PANDAN DAMAI 3', 'JALAN PANDAN DAMAI 2', 'PADANG PARKIR DISEBERANG CASWAY', 'DATARAN PAYA BESAR', 'JALAN TAS 1', 'JALAN TAS 2', 'JALAN TAS 4'],
            'JALAN PENJARA' => ['JALAN PENJARA', 'LORONG MAT KILAU 24', 'LORONG MAT KILAU 24/1'],
            'KOD KAWASAN' => ['NAMA JALAN', 'NAMA JALAN', 'NAMA JALAN'],
            'MAKTAB ADABI ' => ['LORONG BELUKAR MAJU 2'],
            'NIRWANA BATU 3 (COMMERCIAL CENTRE)' => ['LORONG KURNIA JAYA 2', 'JALAN KURNIA JAYA 2', 'JALAN KURNIA JAYA 3'],
            'PANDAN PERDANA' => ['JALAN PANDAN PERDANA 1/5', 'JALAN PANDAN PERDANA 1/6'],
            'PANDAN SEJAHTERA' => ['LORONG PANDAN SEJAHTERA 3/1'],
            'PDG. MBK' => ['JALAN MAHKOTA', 'JALAN MAHKOTA SQUARE', 'JALAN BESAR', 'JALAN TELUK SISEK', 'JALAN TERUNTUM', 'LORONG BELAKANG MAHKOTA', 'JALAN LEBAI ALI', 'JALAN PASAR LAMA', 'JALAN BANK', 'JALAN SEKITAR BSN', 'JALAN HAJI ABDUL AZIZ', 'JALAN MERDEKA', 'JALAN MASJID', 'JALAN TANAH PUTIH', 'JALAN BUKIT UBI', 'JALAN GAMBUT', 'JALAN GAMBUT 1', 'JALAN GAMBUT 2', 'JALAN GAMBUT 3', 'JALAN HAJI ABDUL RAHMAN', 'LORONG ABDUL RAHMAN 1', 'LORONG RUSA 1', 'JALAN DATO WONG AH JANG ', 'JALAN DATO WONG AH JANG 1', 'JALAN DATO WONG AH JANG 2', 'JALAN DATO WONG AH JANG 4', 'JALAN DATO WONG AH JANG 5', 'LORONG TUN ISMAIL 1', 'LORONG TUN ISMAIL 2', 'LORONG TUN ISMAIL 3', 'LORONG TUN ISMAIL 4', 'LORONG TUN ISMAIL 5', 'LORONG TUN ISMAIL 6', 'LORONG TUN ISMAIL 7', 'JALAN TUN ISMAIL 9'],
            'SERI DAMAI AMAN' => ['LORONG SERI DAMAI AMAN 37', 'LORONG SERI DAMAI AMAN 38'],
            'SERI DAMAI PERDANA' => ['LORONG SERI DAMAI PERDANA 57'],
            'SERI FAJAR' => ['LORONG SERI FAJAR 1/1'],
            'STADIUM' => ['JALAN TUN ISMAIL', 'JALAN PASAR', 'JALAN PASAR BARU 1', 'JALAN PASAR BARU 2', 'LORONG PASAR BARU 1', 'LORONG PASAR BARU 2', 'LORONG PASAR BARU 3', 'LORONG PASAR BARU 4', 'JALAN STADIUM', 'JALAN PUTRA SQUARE 1', 'JALAN PUTRA SQUARE 2', 'JALAN PUTRA SQUARE 3', 'JALAN PUTRA SQUARE 4', 'JALAN PUTRA SQUARE 5', 'JALAN PUTRA SQUARE 6', 'JALAN PUTRA SQUARE 7', 'JALAN TUN ISMAIL 1/1', 'JALAN TUN ISMAIL 1', 'LORONG TUN ISMAIL 1', 'LORONG TUN ISMAIL 2', 'LORONG TUN ISMAIL 8', 'LORONG TUN ISMAIL 9', 'LORONG TUN ISMAIL 10', 'LORONG TUN ISMAIL 11', 'LORONG TUN ISMAIL 12'],
            'SUNGAI ISAP' => ['JALAN SUNGAI ISAP JAYA 1', 'LORONG SUNGAI ISAP JAYA 2', 'LORONG SUNGAI ISAP JAYA 1', 'LORONG SUNGAI ISAP JAYA 6', 'JALAN SUNGAI ISAP JAYA', 'LORONG SUNGAI ISAP AMAN', 'LORONG SUNGAI ISAP AMAN 2'],
            'SUNGAI LEMBING (KELOMPOK 1)' => ['LORONG KAMPUNG PADANG BARU 5', 'JALAN KAMPUNG PADANG DAMAI'],
            'SUNGAI LEMBING (KELOMPOK 2)' => ['JALAN KAMPUNG PADANG 2', 'JALAN KAMPUNG PADANG 13', 'JALAN KAMPUNG PADANG 3', 'LORONG PADANG MAJU 1', 'LORONG PADANG MAJU 2', 'LORONG KAMPUNG PADANG PERMAI 47', 'LORONG PADANG PERMAI 40', 'LORONG KAMPUNG PADANG PERMAI 1', 'LORONG KAMPUNG PADANG JAYA 27'],
            'SUNGAI LEMBING (KELOMPOK 3)' => ['LORONG KAMPUNG PADANG JAYA 6', 'LORONG KAMPUNG PADANG JAYA 1'],
            'SUNGAI LEMBING (KELOMPOK 4)' => ['JALAN BUKIT GOH'],
            'TAMAN GURU' => ['JALAN GAMBANG', 'JALAN RASAU AMAN 1'],
            'TANAH PUTIH (PERODUA)' => ['JALAN TANAH PUTIH '],
            'TANAH PUTIH BARU (PETRONAS)' => ['LORONG LENGKOK KANAN 1', 'LORONG LENGKOK KANAN 2', 'LORONG LENGKOK KANAN 22', 'JALAN INDUSTRI TANAH PUTIH BARU', 'JALAN INDUSTRI TANAH PUTIH BARU 5'],
            'TANJUNG LUMPUR' => ['JALAN PROMENADE VILLA 2', 'JALAN PROMENADE VILLA 3', 'JALAN PROMENADE VILLA 4', 'JALAN TANJUNG LUMPUR 7', 'LORONG PERAMU AMAN 30', 'LORONG PERAMU AMAN 31', 'JALAN TANJUNG LUMPUR', 'JALAN TELUK BAHARU 1', 'JALAN TELUK BAHARU 2', 'JALAN TELUK BAHARU 4'],
            'TELUK SISEK ' => ['JALAN TELUK SISEK', 'LORONG SERI KUANTAN 1', 'LORONG SERI KUANTAN 2', 'JALAN SERI KUANTAN 2', 'JALAN ALOR AKAR'],
        ];

        foreach ($roadsByZone as $zone => $roads) {
            foreach ($roads as $road) {
                Road::firstOrCreate([
                    'name' => $road,
                    'zone_id' => $zoneMap[$zone] ?? null,
                ]);
            }
        }
    }
}
