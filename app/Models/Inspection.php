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
        'spare_parts',
        'status',
        'photo_path',
        'video_path',
        'keypad_grade',
        'screen_condition',
        'keypad_condition',
        'sticker_condition',
        'solar_condition',
        'environment_condition',
        'technician_name',
        'branch'
    ];

    protected $casts = [
        'spare_parts' => 'array',
    ];

    public function terminal()
    {
        return $this->belongsTo(Terminal::class);
    }
}
