<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ResetMemberPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class MemberController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $members = Member::orderBy('id')->get();

        return $this->success(
            'Members retrieved successfully',
            MemberResource::collection($members)
        );
    }

    public function store(StoreMemberRequest $request): JsonResponse
    {
        $member = Member::create($request->validated());

        return $this->success('Member created successfully', new MemberResource($member), 201);
    }

    public function show(Member $member): JsonResponse
    {
        return $this->success('Member retrieved successfully', new MemberResource($member));
    }

    public function update(UpdateMemberRequest $request, Member $member): JsonResponse
    {
        $member->update($request->validated());

        return $this->success('Member updated successfully', new MemberResource($member));
    }

    public function destroy(Member $member): JsonResponse
    {
        if ($member->loans()->exists()) {
            return $this->error('Member cannot be deleted because they have loan records', 409);
        }

        $member->delete();

        return $this->success('Member deleted successfully');
    }

    public function resetPassword(
    ResetMemberPasswordRequest $request,
    Member $member
): JsonResponse {
    if (! $member->user_id) {
        return $this->error(
            'Anggota ini belum memiliki akun login.',
            409
        );
    }

    $user = User::find($member->user_id);

    if (! $user) {
        return $this->error(
            'Akun login anggota tidak ditemukan.',
            409
        );
    }

    if ($user->role !== 'member') {
        return $this->error(
            'Endpoint ini hanya untuk akun dengan peran member.',
            403
        );
    }

    $password = $request->validated()['password'];

    if (Hash::check($password, $user->password)) {
        return $this->error(
            'Kata sandi baru harus berbeda dari kata sandi anggota saat ini.',
            422
        );
    }

    $user->password = $password;
    $user->save();

    return $this->success(
        'Kata sandi akun anggota berhasil diubah.'
    );
}
}