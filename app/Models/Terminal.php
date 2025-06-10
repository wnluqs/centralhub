<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Terminal extends Model
{
    // Let Eloquent know the primary key is 'id', but it is not an integer
    protected $primaryKey = 'terminal_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // (Optional) If your table name is not 'terminals', specify here:
    // protected $table = 'terminals';
    public function parkingData()
    {
        return $this->hasOne(TerminalParking::class, 'terminal_id');
    }
}
