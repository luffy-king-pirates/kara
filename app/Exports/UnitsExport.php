<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class UnitsExport implements FromCollection, WithHeadings
{
    protected $units;

    public function __construct($units)
    {
        $this->units = $units;
    }

    public function collection()
    {
        return new Collection($this->units);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Unit Name',
            'Created At',
            'Updated At',
            'Created By',
            'Updated By',
        ];
    }
}
