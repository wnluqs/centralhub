<?php

namespace App\Exports;

use App\Models\CallInbound;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CallInboundExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return CallInbound::select('caller_name', 'phone', 'call_time', 'category', 'notes', 'department_referred')->get();
    }

    public function headings(): array
    {
        return [
            'Caller Name',
            'Phone',
            'Call Time',
            'Category',
            'Notes',
            'Department Referred',
        ];
    }
}
