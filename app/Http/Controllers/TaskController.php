<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function list()
    {
        $tasks = Task::with('employee')->get();
        return ok('Tasks', $tasks);
    }

    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title'         => 'required',
            'due_date'      => 'required|date_format:Y-m-d|after:today',
            'employee_id'   => 'required|exists:employees,id'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $task = Task::create($request->only(['title', 'due_date', 'employee_id']) + [
            'assign_date'   => date('Y-m-d', time())
        ]);
        return ok('Task created successfully', $task);
    }

    public function update(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id'            => 'required|exists:tasks,id',
            'due_date'      => 'date_format:Y-m-d|after:assign_date',
            'employee_id'   => 'exists:employees,id'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'Validation');

        if (count($request->all()) > 1) {
            $task = Task::where('id', $request->id)->first();
            $task->update($request->all());
            return ok('Task Updated Successfully');
        }
        return error('No Data Passed');
    }

    public function delete($id)
    {
        Task::find($id)->delete();
        return ok('Task Deleted Successfully');
    }

    public function show($id)
    {
        $task = Task::find($id);
        return ok('Task Detail', $task);
    }
}
