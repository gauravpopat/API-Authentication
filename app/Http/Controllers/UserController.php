<?php

namespace App\Http\Controllers;

use App\Models\EmployeeTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\JobApplication;
use App\Models\Task;
use App\Models\Job;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\File;
class UserController extends Controller
{
    //show the logged in user
    public function show()
    {
        $user = auth()->user();
        return ok('User', $user);
    }

    public function changePassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'old_password'          => 'required',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);
        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $user = User::where('id', auth()->user()->id)->first();
        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password'  => Hash::make($request->password)
            ]);
            return ok('Password Changed Successfully.');
        } else {
            return error('Old Password Not Matched');
        }
    }

    //logout the user by revoking the token
    public function logout()
    {
        auth()->user()->token()->revoke();
        return ok('You have been logged out.');
    }

    //List of all jobs with company
    public function list()
    {
        $jobs = Job::all()->load('company');
        return ok('List of Jobs', $jobs);
    }

    //Apply for job
    public function applyJob(Request $request)
    {
        //Admin can not apply
        if (auth()->user()->role == 'admin') {
            return error('You are admin.');
        }

        //for user.
        $validation = Validator::make($request->all(), [
            'job_id'    => 'required|exists:jobs,id',
            //'resume'    => 'required|mimes:doc,docx,pdf'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors());
        
        $job = Job::with('company')->where('id',$request->job_id)->first();
        if($job->company->is_active == false){
            return error('Currently the company is inactive');
        }
        
        //Store resume file $resume 
        $resume = $request->file('resume');
        //Generate new resume file name
        $resumeName = 'resume' . time() . $resume->getClientOriginalName();

        //Create application entry
        $jobApplication = JobApplication::create($request->only(['job_id']) + [
            'user_id'   => auth()->user()->id,
            'resume'    => $resumeName
        ]);

        //Move user resume into storage/app/public/user/resume/{user_id}/...
        $path = storage_path('app/public/user/resume/').$jobApplication->user_id.'/';
        $resume->move($path, $resumeName);
        
        return ok('Your job application sent! We will contact soon.');
    }

    //Delete Logged In User.
    public function delete()
    {
        $user = auth()->user();

        //delete the data of user
        $userFile = storage_path('app/public/user/resume/').$user->id;
        File::deleteDirectory($userFile);
        return ok('User Deleted Successfully');
    }
}
