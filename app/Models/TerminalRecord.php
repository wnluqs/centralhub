<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerminalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'terminal_id',
        'type',
        'issues',
        'team_leader',
        'technician_name',
        'zone',
        'road',
        'spare_part_1',
        'spare_part_2',
        'spare_part_3',
        'status',
        'photos'
    ];
}
