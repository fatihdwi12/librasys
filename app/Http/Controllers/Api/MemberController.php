<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateMemberAccountRequest;
use DomainException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
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

public function createAccount(
    CreateMemberAccountRequest $request,
    Member $member
): JsonResponse {
    $data = $request->validated();

    try {
        $member = DB::transaction(function () use ($member, $data) {
            $member = Member::lockForUpdate()->findOrFail($member->id);

            if ($member->user_id !== null) {
                throw new DomainException(
                    'Anggota ini sudah memiliki akun login.'
                );
            }

            if ($member->status !== 'active') {
                throw new DomainException(
                    'Akun login hanya dapat dibuat untuk anggota aktif.'
                );
            }

            $email = strtolower(trim((string) $member->email));

            if (
                $email === '' ||
                ! filter_var($email, FILTER_VALIDATE_EMAIL)
            ) {
                throw new DomainException(
                    'Anggota harus memiliki email yang valid terlebih dahulu.'
                );
            }

            if (User::where('email', $email)->exists()) {
                throw new DomainException(
                    'Email anggota sudah digunakan oleh akun lain. ' .
                    'Periksa hubungan akun; jangan membuat akun kedua.'
                );
            }

            $user = new User();
            $user->name = $member->name;
            $user->email = $email;
            $user->password = $data['password'];
            $user->role = 'member';
            $user->save();

            $member->update([
                'user_id' => $user->id,
                'email' => $email,
            ]);

            return $member;
        });
    } catch (DomainException $e) {
        return $this->error($e->getMessage(), 409);
    } catch (UniqueConstraintViolationException $e) {
        return $this->error(
            'Email atau hubungan akun sudah digunakan. ' .
            'Muat ulang data anggota dan periksa kembali.',
            409
        );
    }

    $member->load('user');

    return $this->success(
        'Akun login anggota berhasil dibuat.',
        new MemberResource($member),
        201
    );
}
}