<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'terminal_id',
        'zone_id',
        'road',
        'remarks',
        'photos',
        'status',
        'assigned_to',
        'types_of_damages',
        'attended_at',
        'fixed_at',
    ];

    protected $casts = [
        'attended_at' => 'datetime',
        'fixed_at' => 'datetime',
        'photos' => 'array',
        'types_of_damages' => 'array',
    ];

    public function terminal()
    {
        return $this->belongsTo(Terminal::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }
}
