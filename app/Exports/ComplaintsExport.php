<?php

namespace App\Exports;

use App\Models\Complaint;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ComplaintsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $terminalId;
    protected $zone;

    public function __construct($terminalId = null, $zone = null)
    {
        $this->terminalId = $terminalId;
        $this->zone = $zone;
    }

    public function collection()
    {
        // Eager load zone relationship
        $query = Complaint::with('zone');

        // Apply filters
        if ($this->terminalId) {
            $query->where('terminal_id', 'like', "%{$this->terminalId}%");
        }

        if ($this->zone) {
            // Filter by zone name via relationship
            $query->whereHas('zone', function ($q) {
                $q->where('name', 'like', "%{$this->zone}%");
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['Terminal ID', 'Zone', 'Road', 'Remarks', 'Created At'];
    }

    public function map($complaint): array
    {
        return [
            $complaint->terminal_id,
            $complaint->zone->name ?? '-', // Access zone name via relationship
            $complaint->road,
            $complaint->remarks,
            $complaint->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
