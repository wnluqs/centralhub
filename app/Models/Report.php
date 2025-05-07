<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'report'; // Laravel expects plural
    protected $fillable = [
        'terminal_id', 'location', 'event_date', 'event_code_name',
        'comment', 'parts_request', 'photo', 'terminal_status'
    ];

    public function terminal()
    {
        return $this->belongsTo(Terminal::class, 'terminal_id', 'id');
    }
}
