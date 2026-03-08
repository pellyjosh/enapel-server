<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'isAdmin' => $user->is_admin,
                ]
            ], 200);
        }

        return response()->json(['message' => 'Invalid email or password'], 401);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function checkAuth(Request $request)
    {
        return response()->json(['user' => $request->user()], 200);
    }

    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        return response()->json(['message' => 'Password reset link sent to email'], 200);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8',
        ]);

        return response()->json(['message' => 'Password reset successfully'], 200);
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
        ]);

        $user = $request->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }

        $user->update(['password' => Hash::make($data['new_password'])]);

        return response()->json(['message' => 'Password updated successfully'], 200);
    }

    public function userProfile(Request $request)
    {
        return response()->json(['user' => $request->user()], 200);
    }

    public function validateLicenseKey(Request $request): JsonResponse
    {
        $data = $request->validate([
            'license_key' => 'required|string|exists:applications,license_key',
        ]);

        $application = Application::where('license_key', $data['license_key'])->first();


        if ($application->status === 'active') {
            return response()->json([
                'message' => 'License key is active',
                'data' => $application
            ], 200);
        }

        if ($application->status === 'pending') {
            return response()->json(['message' => 'License key is pending activation'], 400);
        }

        if ($application->status === 'inactive') {
            return response()->json(['message' => 'License key is expired'], 400);
        }

        return response()->json(['message' => 'Invalid license key status'], 400);
    }
}
