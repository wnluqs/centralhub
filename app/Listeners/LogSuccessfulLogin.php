<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\AuditLog;

class LogSuccessfulLogin
{
    public function handle(Login $event)
    {
        AuditLog::create([
            'user_id' => $event->user->id,
            'event' => 'login',
            'description' => 'User logged in',
            'ip_address' => request()->ip(),
        ]);
    }
}
