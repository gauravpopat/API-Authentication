<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeTask;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;

class EmployeeManageController extends Controller
{
    //list of employees with their tasks and company
    public function list()
    {
        $employees = Employee::with('company','tasks')->get();
        return ok('Employees Detail',$employees);
    }

    public function show($id)
    {
        $employee = Employee::find($id);
        return ok('Employee',$employee);
    }

    public function approveTask(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'id'   => 'required|exists:employee_tasks,id'
        ]);

        if($validation->fails())
            return error('Validation Error',$validation->errors(),'validation');

        $employeeTask = EmployeeTask::where('id',$request->id)->first();
        $task = Task::find($employeeTask->task_id);
        
        $task->update([
            'is_complete'   => true
        ]);
        return ok('Task Approved Successfully',$task);
    }

    public function delete($id)
    {
        $employee = Employee::find($id);
        $employee->tasks->delete();
        $employee->delete();
        return ok('Employee Deleted Successfully');
    }
}
