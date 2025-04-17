<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingEntry extends Model
{
    use HasFactory;

    protected $fillable = ['license_plate', 'parking_spot', 'entry_time', 'exit_time', 'status'];
}
