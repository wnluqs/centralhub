<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// LocationController.php
use App\Models\Zone;
use App\Models\Road;


class LocationController extends Controller
{
    public function getZonesByBranch($branch)
    {
        return response()->json(Zone::where('branch', $branch)->get());
    }

    public function getRoadsByZone($zoneName)
    {
        return response()->json(
            Road::where('zone_name', $zoneName)->get()
        );
    }
}
