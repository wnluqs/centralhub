<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Road;
use App\Models\Zone;

class RoadController extends Controller
{
    // ✅ Correct controller method
    public function getByZone($zoneId)
    {
        $roads = Road::where('zone_id', $zoneId)->pluck('name');
        return response()->json($roads);
    }
}
