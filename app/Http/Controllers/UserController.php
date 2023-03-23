<?php

namespace App\Http\Controllers;

use App\Models\EmployeeTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\JobApplication;
use App\Models\Task;

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
            'resume'    => 'required|file'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors());

        $resume = $request->file('resume');
        $resumeName = 'resume' . now() . $resume->getClientOriginalName();

        if ($resume->move(storage_path('app/public/'), $resumeName)) {
            JobApplication::create($request->only(['job_id']) + [
                'user_id'   => auth()->user()->id,
                'resume'    => 'app/public/' . $resumeName
            ]);
            return ok('Your job application sent! We will contact soon.');
        } else {
            return error('Resume upload error!');
        }
    }

    //Delete Logged In User.
    public function delete()
    {
        auth()->user()->delete();
        return ok('User Deleted Successfully');
    }
}
