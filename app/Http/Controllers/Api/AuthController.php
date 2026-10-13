<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Member;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\ChangePasswordRequest;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request): JsonResponse
    {
        // Register selalu membuat akun anggota. Peran tidak diambil dari request.
        $user = DB::transaction(function () use ($request) {
            $user = User::create($request->validated());

            Member::create([
                'user_id' => $user->id,
                'member_code' => 'MBR-'.Str::upper(Str::random(6)),
                'name' => $user->name,
                'email' => $user->email,
                'status' => 'active',
            ]);

            return $user;
        });

        $user->refresh()->load('member');
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
        $user = auth('api')->user()->load('member');

        return $this->success('User profile retrieved successfully', $user);
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return $this->success('Logged out successfully');
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
{

    if (! config('jwt.blacklist_enabled')) {
        return $this->error(
            'Perubahan kata sandi belum tersedia karena blacklist JWT belum aktif.',
            503
        );
    }

    $user = auth('api')->user();

    $user->password = $request->validated()['password'];
    $user->save();

    // Mencabut token yang digunakan pada request ini.
    auth('api')->logout();

    return $this->success(
        'Kata sandi berhasil diubah. Silakan login kembali.'
    );
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