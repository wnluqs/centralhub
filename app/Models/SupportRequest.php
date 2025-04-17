<?php

// app/Models/SupportRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportRequest extends Model
{
    protected $fillable = ['user_id', 'message', 'is_read'];
}
