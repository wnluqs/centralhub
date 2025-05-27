<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerminalParking extends Model
{
    protected $fillable = [
        'terminal_id',
        'branch',
        'status',
        'location',
        'latitude',
        'longitude',
    ];

    public function terminal()
    {
        return $this->belongsTo(Terminal::class, 'terminal_id');
    }

}
