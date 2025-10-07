<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;

class RegistrationController extends Controller
{
    public function showForm()
    {
        return view('auth.registration');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'gender'        => 'required|in:male,female',
            'email'         => 'required|email|unique:registrations,email',
            'country_code'  => 'required|string|max:10',
            'mobile_number' => 'required|string|max:20|unique:registrations,mobile_number',
            'password'      => 'required|confirmed|min:6',
        ]);

        // Create user
        $user = Registration::create([
            'name'              => $request->name,
            'gender'            => $request->gender,
            'email'             => $request->email,
            'country_code'      => $request->country_code,
            'mobile_number'     => $request->mobile_number,
            'password'          => Hash::make($request->password),
            'is_email_verified' => 0,
            'is_mobile_verified'=> 0,
        ]);

        // Generate email verification code
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->email_verification_code = $code;
        $user->save();

        // Send email
        try {
            Mail::to($user->email)->send(new EmailVerificationMail($code));
        } catch (\Exception $e) {
            \Log::error('Email Verification Error: '.$e->getMessage());
        }

        // Redirect to verification page
        return redirect()->route('email.verify.notice')->with('email', $user->email);
    }
}
