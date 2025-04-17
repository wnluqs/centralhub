<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'terminal_id',
        'zone',
        'road',
        'spare_part_1',
        'spare_part_2',
        'spare_part_3',
        'status',
        'photos',
        'technician_name'
    ];

    public function terminal()
    {
        return $this->belongsTo(Terminal::class);
    }
}
