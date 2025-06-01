<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BTS extends Model
{
    use HasFactory;

    protected $table = 'bts'; // 👈 important

    protected $fillable = [
        'staff_id',
        'terminal_id',
        'status',
        'location',
        'event_date',
        'event_code_name',
        'comment',
        'parts_request',
        'terminal_status',
        'damage_type',
        'action_by',
        'action_status',
        'photo',
    ];
}
