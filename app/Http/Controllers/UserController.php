<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

    //Delete Logged In User.
    public function delete()
    {
        auth()->user()->delete();
        return ok('User Deleted Successfully');
    }
}
