<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'terminal_id',
        'zone',
        'road',
        'photos',
        'remarks',
        'status'
    ];

    public function terminal()
    {
        return $this->belongsTo(Terminal::class);
    }
}
