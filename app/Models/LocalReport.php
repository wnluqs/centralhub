<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocalReport extends Model
{
    protected $fillable = [
        'branch',
        'zone',
        'road',
        'public_complaints',
        'public_others',
        'operations_complaints',
        'operations_others',
        'technician_name',
        'landmark',
        'latitude',
        'longitude',
        'photos',
        'videos',
    ];

    protected $casts = [
        'public_complaints' => 'array',
        'operations_complaints' => 'array',
        'photos' => 'array',
        'videos' => 'array',
    ];

}
