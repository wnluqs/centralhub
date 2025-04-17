<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'event',
        'model',
        'model_id',
        'description',
        'ip_address',
    ];

    // (Optional) If you want relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
