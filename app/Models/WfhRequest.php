<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WfhRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'date',
        'reason',
        // If you want to allow status and approval_notes to be mass assigned, include them too:
        'status',
        'approval_notes',
    ];

    // Rest of your model code...
}
