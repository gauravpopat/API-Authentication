<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Carbon;

class JobApplicationController extends Controller
{
    public function list()
    {
        $applications = JobApplication::all();
        return ok('applications', $applications);
    }

    public function approve(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id'    => 'required|exists:job_applications,id'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors());

        $application = JobApplication::find($request->id)->load('jobs');
        $user = User::find($application->user_id);

        $application->update([
            'is_confirm'    =>  true
        ]);

        // $user->update([
        //     'role' => 'employee'
        // ]);

        $employee = Employee::create([
            'first_name'    =>  $user->first_name,
            'last_name'     =>  $user->last_name,
            'email'         =>  $user->email,
            'phone'         =>  $user->phone,
            'company'       =>  $application->jobs->company_id,
            'phone'         =>  $user->phone,
            'joining_date'  =>  Carbon::now()->addMonth(1) //joining date 1 month after selection.
        ]);

        return ok('Application is Approved', $employee);
    }
}
