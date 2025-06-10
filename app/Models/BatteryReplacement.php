<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatteryReplacement extends Model
{
    use HasFactory;

    protected $fillable = [
        'terminal_id',
        'staff_id',
        'status',
        'photo',
        'comment',
        'submitted_by',
        'created_by',
        'verified',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
