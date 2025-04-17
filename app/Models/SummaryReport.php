<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummaryReport extends Model
{
    protected $table = 'reports';

    protected $fillable = [
        'terminal_id',
        'spare_part_1',
        'spare_part_2',
        'spare_part_3',
        'status',
    ];

    // If you have a relationship to Terminal:
    public function terminal()
    {
        return $this->belongsTo(Terminal::class, 'terminal_id');
    }
}
