<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallInbound extends Model
{
    use HasFactory;

    protected $fillable = [
        'caller_name',
        'phone',
        'call_time',
        'category',
        'notes',
        'department_referred',
    ];
}
