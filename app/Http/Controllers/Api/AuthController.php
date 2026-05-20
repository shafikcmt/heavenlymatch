<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:150',
            'email'         => 'required|email|unique:registrations,email',
            'password'      => ['required', 'confirmed', Password::min(8)],
            'gender'        => 'required|in:male,female',
            'platform_mode' => 'required|in:GENERAL,ISLAMIC',
            'mobile_number' => 'nullable|string|max:20',
            'country_code'  => 'nullable|string|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $reg = Registration::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'gender'        => $request->gender,
            'platform_mode' => $request->platform_mode,
            'mobile_number' => $request->mobile_number,
            'country_code'  => $request->country_code ?? '+880',
            'account_status'=> 'active',
            'role'          => 'user',
        ]);

        $token = $reg->createToken('api')->plainTextToken;

        return response()->json([
            'message'          => 'Registration successful.',
            'registration_id'  => $reg->registration_id,
            'token'            => $token,
        ], 201);
    }

    /**
     * POST /api/auth/login
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $reg = Registration::where('email', $request->email)->first();

        if (! $reg || ! Hash::check($request->password, $reg->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if ($reg->account_status === 'banned') {
            return response()->json(['message' => 'Account has been suspended.'], 403);
        }

        $reg->update(['last_login_at' => now()]);
        $token = $reg->createToken('api')->plainTextToken;

        return response()->json([
            'token'           => $token,
            'registration_id' => $reg->registration_id,
            'name'            => $reg->name,
            'gender'          => $reg->gender,
            'platform_mode'   => $reg->platform_mode,
            'membership'      => [
                'plan'    => $reg->membership_plan_name,
                'status'  => $reg->membership_status,
                'expires' => $reg->membership_expires_at,
            ],
        ]);
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    /**
     * GET /api/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('biodata');

        return response()->json([
            'registration_id'  => $user->registration_id,
            'name'             => $user->name,
            'email'            => $user->email,
            'gender'           => $user->gender,
            'platform_mode'    => $user->platform_mode,
            'photo_visibility' => $user->photo_visibility,
            'account_status'   => $user->account_status,
            'membership'       => [
                'plan'    => $user->membership_plan_name,
                'status'  => $user->membership_status,
                'expires' => $user->membership_expires_at,
            ],
            'biodata_complete' => (bool) ($user->biodata?->is_completed),
            'biodata_status'   => $user->biodata?->status,
        ]);
    }
}
