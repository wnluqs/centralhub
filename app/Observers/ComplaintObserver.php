<?php

namespace App\Observers;

use App\Models\Complaint;
use App\Models\AuditLog;

class ComplaintObserver
{
    public function created(Complaint $complaint)
    {
        if (!$complaint->exists) {
            return; // Ensures the model is fully saved before logging
        }
        AuditLog::create([
            'user_id' => auth()->id() ?? null,
            'event' => 'created',
            'model' => 'Complaint',
            'model_id' => $complaint->id,
            'description' => 'A new complaint was created',
            'ip_address' => request()->ip(),
        ]);
    }

    public function updated(Complaint $complaint)
    {
        AuditLog::create([
            'user_id' => auth()->id() ?? null,
            'event' => 'updated',
            'model' => 'Complaint',
            'model_id' => $complaint->id,
            'description' => 'Complaint updated',
            'ip_address' => request()->ip(),
        ]);
    }

    public function deleted(Complaint $complaint)
    {
        AuditLog::create([
            'user_id' => auth()->id() ?? null,
            'event' => 'deleted',
            'model' => 'Complaint',
            'model_id' => $complaint->id,
            'description' => 'Complaint deleted',
            'ip_address' => request()->ip(),
        ]);
    }
}
