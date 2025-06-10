<?php

namespace App\Exports;

use App\Models\BatteryReplacement;
use Maatwebsite\Excel\Concerns\FromCollection;

class BatteryExport implements FromCollection
{
    public function collection()
    {
        return BatteryReplacement::all();
    }
}
