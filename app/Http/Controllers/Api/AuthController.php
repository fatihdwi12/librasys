<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        $token = auth('api')->login($user);

        return $this->success('User registered successfully', [
            'user' => $user,
            'token' => $this->tokenData($token),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = auth('api')->attempt($request->validated());

        if (! $token) {
            return $this->error('Invalid email or password', 401);
        }

        return $this->success('Login successful', [
            'user' => auth('api')->user(),
            'token' => $this->tokenData($token),
        ]);
    }

    public function me(): JsonResponse
    {
        return $this->success('User profile retrieved successfully', auth('api')->user());
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return $this->success('Logged out successfully');
    }

    private function tokenData(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }
}