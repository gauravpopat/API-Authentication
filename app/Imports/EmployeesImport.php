<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;

class EmployeesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Employee([
            'first_name'    => $row[1],
            'last_name'     => $row[2],
            'email'         => $row[3],
            'phone'         => $row[4],
            'joining_date'  => $row[5],
            'company'       => $row[6],
            'created_at'    => $row[7],
            'updated_at'    => $row[8]
        ]);
    }
}
