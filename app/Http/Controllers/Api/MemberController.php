<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

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
}