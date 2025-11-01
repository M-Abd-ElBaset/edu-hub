<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'student',
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'error' => 'invalid_credentials',
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user = auth()->user();

        return response()->json([
            'message' => 'Successfully logged in',
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ]);
    }

    public function logout(): JsonResponse
    {
        try {
            // Invalidate the token
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'message' => 'Successfully logged out'
            ]);

        } catch (JWTException $e) {
            return response()->json([
                'error' => 'token_invalidation_failed',
                'message' => 'Failed to logout, please try again'
            ], 500);
        }
    }

    public function refresh(): JsonResponse
    {
        try {
            $token = JWTAuth::refresh();
            $user = JWTAuth::setToken($token)->authenticate();

            return response()->json([
                'message' => 'Token refreshed',
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'token_refresh_failed',
                'message' => 'Could not refresh token'
            ], 401);
        }
    }

    public function me(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'user' => $user,
            'permissions' => $this->getUserPermissions($user)
        ]);
    }

    private function getUserPermissions(User $user): array
    {
        $permissions = ['access_courses', 'enroll_courses'];

        if ($user->isInstructor()) {
            array_push($permissions, 'create_courses', 'manage_own_courses');
        }

        if ($user->isAdmin()) {
            array_push($permissions, 'manage_all_courses', 'manage_users');
        }

        return $permissions;
    }
}