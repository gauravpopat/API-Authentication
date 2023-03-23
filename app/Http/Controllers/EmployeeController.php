<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\EmployeeTask;
use App\Models\Task;
use App\Traits\ListingApiTrait;

class EmployeeController extends Controller
{
    use ListingApiTrait;

    public function profile(Request $request)
    {
        $employee = Employee::with('tasks','company')->where('email',auth()->user()->email)->get();
        return ok('Employee',$employee);
    }

    //Search for tasks
    // public function list(Request $request)
    // {
    //     $employee = Employee::where('email',auth()->user()->email)->first();
    //     $this->ListingValidation();
    //     $query = Task::where('employee_id',$employee->id)->first();
    //     $searchable_fields = ['title'];
    //     $data = $this->filterSearchPagination($query, $searchable_fields);
    //     return ok('Tasks',[
    //         'tasks'=>$data['query']->get(),
    //         'count'=>$data['count']
    //     ]);
    // }


    //submit the task

    public function submitTask(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'task_id'   => 'required|exists:tasks,id',
            'document'  => 'required|file'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $task = Task::where('id', $request->task_id)->first();
        $employeeId = $task->employee->id;
        if ($task->employee_id == $employeeId) {
            $document = $request->file('document');
            $docName = auth()->user()->first_name . "document" . $document->getClientOriginalName();
            if ($document->move(storage_path('app/public/'), $docName)) {
                EmployeeTask::create($request->only(['task_id']) + [
                    'document'      => 'app/public/' . $docName,
                    'employee_id'   => $employeeId
                ]);
                return ok('Task has been submited. wait for the confirmation');
            }
            return error('Document Upload Error');
        }
        return error('Its not your task');
    }
}
