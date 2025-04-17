<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerminalParking extends Model
{
    protected $fillable = [
        'number',
        'status',
        'zone_code',           // renamed from warranty_expiry
        'last_communication',  // renamed from serial_number
        'location',
        'latitude',
        'longitude',
        'battery_health'
    ];
}
