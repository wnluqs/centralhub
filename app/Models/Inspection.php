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
        'screen',
        'keypad',
        'sticker',
        'solar',
        'environment',
        'submitted_by', // ← replaced with technician_name
        'branch'
    ];

    protected $casts = [
        'spare_parts' => 'array',
    ];

    public function terminal()
    {
        return $this->belongsTo(Terminal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
