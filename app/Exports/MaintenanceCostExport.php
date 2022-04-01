<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
class MaintenanceCostExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $maintenanceCost;
    public function __construct($maintenanceCost)
    {
        $this->maintenanceCost = $maintenanceCost;
    }

    public function collection()
    {
        return new Collection($this->maintenanceCost);
    }
}
