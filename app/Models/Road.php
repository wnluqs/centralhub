<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Road extends Model
{
    // app/Models/Road.php
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
