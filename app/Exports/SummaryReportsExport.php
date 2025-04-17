<?php

namespace App\Exports;

use App\Models\SummaryReport;
use Maatwebsite\Excel\Concerns\FromCollection;

class SummaryReportsExport implements FromCollection
{
    protected $terminal;
    protected $sparePart;

    public function __construct($terminal = null, $sparePart = null)
    {
        $this->terminal  = $terminal;
        $this->sparePart = $sparePart;
    }

    public function collection()
    {
        $query = SummaryReport::query();

        if ($this->terminal) {
            $query->where('terminal_id', 'like', "%{$this->terminal}%");
        }

        if ($this->sparePart) {
            $query->where(function ($q) {
                $q->where('spare_part_1', 'like', "%{$this->sparePart}%")
                    ->orWhere('spare_part_2', 'like', "%{$this->sparePart}%")
                    ->orWhere('spare_part_3', 'like', "%{$this->sparePart}%");
            });
        }

        return $query->get();
    }
}
