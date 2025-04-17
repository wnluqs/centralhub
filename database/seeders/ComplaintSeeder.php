<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ComplaintSeeder extends Seeder
{
    public function run()
    {
        $complaints = [
            ['Broken meter at the entrance', 'Lot 10, KL', 'Pending', 'complaint_photos/broken_meter1.jpg'],
            ['Receipt output malfunction', 'Parking Zone A, Selangor', 'Resolved', 'complaint_photos/receipt_issue.jpg'],
            ['Display screen not working', 'Terminal 3, Johor', 'In Progress', 'complaint_photos/display_error.jpg'],
            ['Machine buttons unresponsive', 'Basement Parking, Penang', 'Pending', 'complaint_photos/buttons_issue.jpg'],
            ['Ticket jammed in dispenser', 'Parking Lot B, Sabah', 'Resolved', 'complaint_photos/ticket_jammed.jpg'],
            ['Loose wiring found inside', 'Car Park C, Perak', 'In Progress', 'complaint_photos/loose_wiring.jpg'],
            ['Cash payment system failure', 'Mall Parking, Kedah', 'Pending', 'complaint_photos/cash_error.jpg'],
            ['Printer roller damaged', 'Open Space Parking, Sarawak', 'Resolved', 'complaint_photos/printer_damage.jpg'],
            ['Touchscreen unresponsive', 'Smart Parking Zone, KL', 'In Progress', 'complaint_photos/touchscreen_issue.jpg'],
            ['Unauthorized tampering detected', 'Public Parking, Melaka', 'Pending', 'complaint_photos/tampering_alert.jpg'],
        ];

        foreach ($complaints as $complaint) {
            DB::table('complaints')->insert([
                'description' => $complaint[0],
                'location' => $complaint[1],
                'state' => $complaint[2],
                'photo_path' => $complaint[3],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
