<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FTLT extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone',
        'location',
        'checkin_photo',
        'checkout_photo',
        'notes',
        'staff_id',
        'user_id',
        'check_in_time',
        'check_out_time',
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
