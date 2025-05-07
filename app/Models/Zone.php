<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    // app/Models/Zone.php
    public function roads()
    {
        return $this->hasMany(Road::class);
    }
}
