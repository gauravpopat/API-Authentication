<?php

namespace App\Http\Controllers;

use App\Exports\EmployeesExport;
use App\Imports\EmployeesImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Employee;

class ImportExportController extends Controller
{
    public function export(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'start_date'    => 'date',
            'end_date'      => 'date|after:start_date'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $employee = Employee::whereBetween('joining_date', [$request->start_date, $request->end_date])->get();

        return Excel::download(new EmployeesExport($employee), 'employees.csv');
    }

    public function import(Request $request)
    {
        $validation = Validator::make([
            'file'      => $request->file,
            'extension' => strtolower($request->file->getClientOriginalExtension()),
        ], [
            'file'  => 'required',
            'extension' => 'required|in:csv'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        Excel::import(new EmployeesImport, $request->file('file'));
        return ok('File Imported');
    }
}
