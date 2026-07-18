<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Resources\Admin\AdminResource;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $admin = Admin::query()
            ->where('email', $credentials['email'])
            ->first();

        if (! $admin || ! Hash::check($credentials['password'], $admin->password)) {
            return response()->json([
                'message' => 'Invalid login credentials.',
            ], 422);
        }

        if ($admin->status !== 'active') {
            return response()->json([
                'message' => 'This admin account is not active.',
            ], 403);
        }

        $admin->update([
            'last_login_at' => now(),
        ]);

        $token = $admin->createToken('admin-api-token')->plainTextToken;

        return response()->json([
            'message' => 'Admin logged in successfully.',
            'token_type' => 'Bearer',
            'access_token' => $token,
            'admin' => new AdminResource($admin->fresh()),
        ]);
    }

    public function me(Request $request): AdminResource
    {
        return new AdminResource($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Admin logged out successfully.',
        ]);
    }
}