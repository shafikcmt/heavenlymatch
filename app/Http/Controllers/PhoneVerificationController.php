<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Session;

class PhoneVerificationController extends Controller
{
    public function sendCode(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string',
            'mobile_number' => 'required|string'
        ]);

        $fullNumber = $request->country_code . $request->mobile_number;

        try {
            $otp = rand(100000, 999999);

            $client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
            $client->messages->create(
                $fullNumber,
                [
                    'from' => env('TWILIO_PHONE'),
                    'body' => "Your verification code is: $otp"
                ]
            );

            session(['mobile_otp' => $otp]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        if ($request->code == session('mobile_otp')) {
            session()->forget('mobile_otp');
            return response()->json(['verified' => true]);
        }

        return response()->json(['verified' => false]);
    }

    
}