<?php

namespace App\Exports;

use App\Models\Report;
use App\Models\Complaint;
use App\Models\LocalReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection, WithHeadings
{
    protected $type;

    public function __construct($type = null)
    {
        $this->type = $type;
    }

    public function collection(): Collection
    {
        // 1) BTS
        $bts = Report::select(
            'terminal_id',
            'location',
            'event_date',
            'event_code_name',
            'comment',
            'parts_request',
            'terminal_status'
        )
            ->get()
            ->map(fn($r) => [
                'type'             => 'BTS',
                'terminal_id'      => $r->terminal_id,
                'location'         => $r->location,
                'event_date'       => $r->event_date,
                'event_code_name'  => $r->event_code_name,
                'comment'          => $r->comment,
                'parts_request'    => $r->parts_request,
                'terminal_status'  => $r->terminal_status,
            ]);

        // 2) Complaints
        $complaints = Complaint::select(
            'terminal_id',
            'zone as location',
            'created_at as event_date',
            'types_of_damages',
            DB::raw("'' as event_code_name"),
            'remarks as comment',
            DB::raw("'' as parts_request"),
            'attended_at',
            'fixed_at',
            'status as terminal_status'
        )
            ->get()
            ->map(fn($c) => [
                'type'             => 'Complaint',
                'terminal_id'      => $c->terminal_id,
                'location'         => $c->location,
                'event_date'       => $c->event_date,
                'event_code_name'  => '',
                'comment'          => $c->comment,
                'parts_request'    => '',
                'terminal_status'  => $c->terminal_status,
            ]);

        // 3) Local Reports
        $local = LocalReport::select(
            DB::raw("'' as terminal_id"),
            'zone as location',
            'created_at as event_date',
            DB::raw("'' as event_code_name"),
            'public_complaints as comment',
            DB::raw("'' as parts_request"),
            DB::raw("'' as terminal_status")
        )
            ->get()
            ->map(fn($l) => [
                'type'             => 'Local',
                'terminal_id'      => '',
                'location'         => $l->location,
                'event_date'       => $l->event_date,
                'event_code_name'  => '',
                'comment'          => $l->comment,
                'parts_request'    => '',
                'terminal_status'  => '',
            ]);

        // Merge & sort
        $all = $bts->merge($complaints)->merge($local)
            ->sortByDesc('event_date')
            ->values();

        // Return filtered by type if requested
        return match ($this->type) {
            'BTS'       => $bts,
            'Complaint' => $complaints,
            'Local'     => $local,
            default     => $all,
        };
    }

    public function headings(): array
    {
        return [
            'Type',
            'Terminal ID',
            'Location',
            'Event Date',
            'Event Code - Name',
            'Comment',
            'Parts Request',
            'Terminal Status',
        ];
    }
}
