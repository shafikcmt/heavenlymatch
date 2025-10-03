<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'gender'            => 'required|in:male,female',
            'email'             => 'required|email|unique:registrations,email',
            'country_code'      => 'required|string|max:10',
            'mobile_number'     => 'required|string|max:20|unique:registrations,mobile_number',
            'password'          => 'required|confirmed|min:6',
        ]);

        Registration::create([
            'name'          => $request->name,
            'gender'        => $request->gender,
            'email'         => $request->email,
            'country_code'  => $request->country_code,
            'mobile_number' => $request->mobile_number,
            'password'      => Hash::make($request->password),
        ]);

        return redirect()->route('dashboard')->with('success', 'Account created successfully!');
    }
}
