<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class TerminalParkingExport implements FromCollection
{
    protected $terminals;

    /**
     * Create a new export instance with the filtered data.
     *
     * @param \Illuminate\Support\Collection $terminals
     */
    public function __construct(Collection $terminals)
    {
        $this->terminals = $terminals;
    }

    /**
     * Return the collection for export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->terminals;
    }
}
