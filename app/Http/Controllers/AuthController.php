<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use App\Models\PasswordResetToken;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;
use App\Models\Job;

class AuthController extends Controller
{
    use HasApiTokens;
    public function register(Request $request)
    {
        //Validation
        $validation = Validator::make($request->all(), [
            'first_name'            => 'required|max:255',
            'last_name'             => 'required|max:255',
            'email'                 => 'required|email|max:255|unique:users,email',
            'phone'                 => 'required|regex:/[6-9][0-9]{9}/|unique:users,phone',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

        //Used Error Helper Function for Displaying the Errors.
        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        //Create User
        $user = User::create($request->only(['first_name','last_name', 'email','phone']) + [
            'password'                  => Hash::make($request->password),
            'email_verification_code'   => Str::random(40)
        ]);

        Mail::to($user->email)->send(new WelcomeMail($user));
        return ok('User Created Successfully, Mail sent for Email Verification.', $user);
    }

    public function verifyEmail($verificatonCode)
    {
        $user = User::where('email_verification_code', $verificatonCode)->first();
        if ($user) {
            $user->update([
                'is_active'                 => true,
                'email_verified_at'         => now(),
                'email_verification_code'   => null
            ]);
            return ok('Verification Successfull');
        } else {
            return error('User Not Found');
        }
    }

    public function forgotPasswordLink(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'Validation');

        $user = User::where('email', $request->email)->first();
        $token = Str::random(64);

        PasswordResetToken::updateOrCreate(
            ['email'         => $request->email],
            [
                'token'         => $token,
                'created_at'    => now(),
                'expired_at'    => Carbon::now()->addDays(2)
            ]
        );

        $user['token']  = $token;

        Mail::to($request->email)->send(new ResetPasswordMail($user));

        return ok('Mail Sent for Reset Passoword!');
    }

    public function forgotPassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email'                 => 'required|email|exists:password_reset_tokens,email|exists:users,email',
            'token'                 => 'required|exists:password_reset_tokens,token',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'Validation');

        $passwordreset = PasswordResetToken::where('email', $request->email)->first();
        if ($passwordreset->expired_at > Carbon::now()) {
            $user = User::where('email', $request->email)->first();
            $user->update([
                'password'  =>  Hash::make($request->password)
            ]);
            $passwordreset->delete();
            return ok('Password Changed Successfully');
        } else {
            return error('Token Expired');
        }
    }

    public function login(Request $request)
    {
        //Validation
        $validation = Validator::make($request->all(), [
            'email'     => 'required|email|exists:users,email',
            'password'  => 'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        //get user for checking that is active or not.
        $user = User::where('email', $request->email)->first();
        if ($user->is_active == true) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $token = auth()->user()->createToken('Api Token')->accessToken;
                return ok('Login Successful.', $token);
            } else {
                return error('Password Incorrect');
            }
        } else {
            return error('Email not verifed.');
        }
    }

    //List of all jobs with company
    public function list()
    {
        $jobs = Job::all()->load('company');
        return ok('List of Jobs', $jobs);
    }
}
