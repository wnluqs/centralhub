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
                'id',
                'terminal_id',
                'zone',
                'road',
                'branch',
                'spare_parts',
                'status',
                'submitted_by',
                'created_at',
                'photo_path',
                'video_path',
                'keypad_grade',
                'screen',
                'keypad',
                'sticker',
                'solar',
                'environment',
                'spotcheck_verified',
                'spotcheck_verified_by',
            ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Terminal ID',
            'Zone',
            'Road',
            'Branch',
            'Spare Parts',
            'Status',
            'Technician',
            'Created At',
            'Photo Path',
            'Video Path',
            'Keypad Grade',
            'Screen Condition',
            'Keypad Condition',
            'Sticker Condition',
            'Solar Condition',
            'Environment Condition',
            'Spotcheck Verified',
            'Spotcheck Verified By',
        ];
    }

    public function map($inspection): array
    {
        return [
            $inspection->id,
            $inspection->terminal_id,
            $inspection->zone,
            $inspection->road,
            $inspection->branch,
            is_array($inspection->spare_parts) ? implode(', ', $inspection->spare_parts) : '',
            $inspection->status,
            $inspection->submitted_by,
            $inspection->created_at,
            $inspection->photo_path,
            $inspection->video_path,
            $inspection->video_grade,
        ];
    }
}
