<?php

namespace App\Exports;

use App\Models\Inspection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InspectionsExport implements FromCollection, WithHeadings
{
    protected $search;

    // Accept an optional search parameter for filtering.
    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return Inspection::with('terminal')
            ->when($this->search, function ($query, $search) {
                return $query->where('terminal_id', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get([
                'terminal_id',
                'zone',
                'road',
                'spare_part_1',
                'spare_part_2',
                'spare_part_3',
                'status',
                'technician_name',
                'created_at'
            ]);
    }

    public function headings(): array
    {
        return [
            'Terminal ID',
            'Zone',
            'Road',
            'Spare Part 1',
            'Spare Part 2',
            'Spare Part 3',
            'Status',
            'Technician Name',
            'Created At'
        ];
    }
}
