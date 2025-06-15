<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function Termwind\terminal;

class FTLT extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch', // ✅ Add this
        'zone',
        'location',
        'checkin_photo',
        'checkout_photo',
        'notes',
        'staff_id',
        'user_id',
        'check_in_time',
        'check_out_time',
        'terminal_id',
    ];

    protected $table = 'ftlt';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id', 'staff_id');
    }
}
