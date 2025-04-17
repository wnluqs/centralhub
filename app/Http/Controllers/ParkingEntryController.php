<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParkingEntry;
use Illuminate\Support\Facades\DB;

class ParkingEntryController extends Controller
{
    public function getParkingData()
    {
        // For example, if you're grouping by minute_slot (you can adjust this as needed)
        $data = DB::table('parking_entries')
            ->selectRaw("
                DATE_FORMAT(entry_time, '%H:%i') as minute_slot,
                SUM(CASE WHEN status = 'Paid' THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN status = 'Unpaid' THEN 1 ELSE 0 END) as unpaid_count,
                SUM(CASE WHEN status = 'Pending Payment' THEN 1 ELSE 0 END) as pending_count
            ")
            ->groupBy('minute_slot')
            ->orderBy('minute_slot')
            ->get();

        return response()->json($data);
    }
}
