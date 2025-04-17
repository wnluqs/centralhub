<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParkingRecord;

class ParkingRecordController extends Controller
{
    public function index()
    {
        $parkingRecords = ParkingRecord::latest()->take(10)->get();
        return response()->json($parkingRecords);
    }
}
