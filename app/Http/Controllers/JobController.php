<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\JobUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    public function list()
    {
        $jobs = Job::all();
        return ok('Jobs', $jobs);
    }

    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'          => 'required|max:255',
            'package'       => 'required|numeric',
            'company_id'    => 'required|exists:companies,id'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $job = Job::create($request->only(['name', 'package', 'company_id']));
        return ok('Job Created Successfully', $job);
    }

    public function update(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id'            => 'required|exists:jobs,id',
            'name'          => 'max:255',
            'package'       => 'numeric',
            'company_id'    => 'exists:companies,id'
        ]);
        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $job = Job::find($request->id);
        if (count($request->all()) > 1) {
            $job->update($request->all());
            return ok('Job Updated Successfully');
        } else {
            return error('No Data Passed for Update');
        }
    }

    public function show($id)
    {
        $job = Job::find($id);
        return ok('Job Detail', $job);
    }

    public function delete($id)
    {
        Job::find($id)->delete();
        return ok('Job Deleted Successfully');
    }


    // List All Job Application
    public function listApplication()
    {
        $applications = JobUser::all();
        return ok('Job Applications', $applications);
    }

    //Approve Job Application
    public function approveApplication(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id'    => 'required|exists:job_users,id'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors());

        $jobUser = JobUser::find($request->id)->load('jobs');
        
        $jobUser->update([
            'is_confirm'    =>  true
        ]);

        $userId = $jobUser->user_id;
        $user = User::find($userId);

        $employee = Employee::create([
            'first_name'    =>  $user->first_name,
            'last_name'     =>  $user->last_name,
            'email'         =>  $user->email,
            'phone'         =>  $user->phone,
            'company'       =>  $jobUser->jobs->company_id,
            'phone'         =>  $user->phone,
            'joining_date'  =>  Carbon::now()->addMonth(1)
        ]);

        return ok('Application is Approved',$employee);
    }
}
