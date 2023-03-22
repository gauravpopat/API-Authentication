<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserJobController extends Controller
{
    public function list()
    {
        //List of all jobs with company
        $jobs = Job::all()->load('company');
        return ok('List of Jobs', $jobs);
    }

    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'job_id'    => 'required|exists:jobs,id',
            'resume'    => 'required|file'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors());

        $resume = $request->file('resume');
        $resumeName = 'resume' . now() . $resume->getClientOriginalName();
        if ($resume->move(storage_path('app/public/'), $resumeName)) {
            JobUser::create($request->only(['job_id']) + [
                'user_id'   => auth()->user()->id,
                'resume'    => 'app/public/' . $resumeName
            ]);
            return ok('Your job application sent! We will contact soon.');
        } else {
            return error('Resume upload error!');
        }
    }
}
