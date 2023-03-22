<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function list()
    {
        $employees = Employee::all();
        return ok('Employees Detail',$employees);
    }

    public function show($id)
    {
        $employee = Employee::find($id);
        return ok('Employee',$employee);
    }

    public function delete($id)
    {
        Employee::find($id)->delete();
        return ok('Employee Deleted Successfully');
    }
}
