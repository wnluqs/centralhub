<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\AuditLog;

class LogSuccessfulLogout
{
    public function handle(Logout $event)
    {
        AuditLog::create([
            'user_id' => $event->user->id,
            'event' => 'logout',
            'description' => 'User logged out',
            'ip_address' => request()->ip(),
        ]);
    }
}
