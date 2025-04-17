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
        // 1. Build the query
        $query = Complaint::select('terminal_id', 'zone', 'road', 'remarks', 'created_at');

        // 2. Apply filters if present
        if ($this->terminalId) {
            $query->where('terminal_id', 'like', "%{$this->terminalId}%");
        }
        if ($this->zone) {
            $query->where('zone', 'like', "%{$this->zone}%");
        }

        // 3. Return the filtered collection
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
            $complaint->zone,
            $complaint->road,
            $complaint->remarks,
            $complaint->created_at->format('Y-m-d H:i:s'), // Format timestamp
        ];
    }
}
