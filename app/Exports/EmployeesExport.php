<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Export;
use GuzzleHttp\Psr7\Request;
use Maatwebsite\Excel\Concerns\FromCollection;

class EmployeesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $employee;
    public function __construct($employee)
    {
        $this->employee = $employee;
    }

    public function collection()
    {
        return $this->employee;
    }
}
