<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use App\Services\LoanService;
use App\Traits\ApiResponse;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    use ApiResponse;

    public function __construct(private LoanService $loanService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $loans = Loan::with(['member', 'book'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->query('status'));
            })
            ->orderByDesc('id')
            ->get();

        return $this->success('Loans retrieved successfully', LoanResource::collection($loans));
    }

    public function store(StoreLoanRequest $request): JsonResponse
    {
        try {
            $loan = $this->loanService->borrow($request->validated());
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), 422);
        }

        $loan->load(['member', 'book']);

        return $this->success('Book borrowed successfully', new LoanResource($loan), 201);
    }

    public function show(Loan $loan): JsonResponse
    {
        $loan->load(['member', 'book']);

        return $this->success('Loan retrieved successfully', new LoanResource($loan));
    }

    public function update(UpdateLoanRequest $request, Loan $loan): JsonResponse
    {
        if ($loan->status === 'returned') {
            return $this->error('Returned loan cannot be updated', 409);
        }

        $loan->update($request->validated());
        $loan->load(['member', 'book']);

        return $this->success('Loan updated successfully', new LoanResource($loan));
    }

    public function destroy(Loan $loan): JsonResponse
    {
        if ($loan->status === 'borrowed') {
            return $this->error('Loan cannot be deleted before the book is returned', 409);
        }

        $loan->delete();

        return $this->success('Loan deleted successfully');
    }

    public function returnBook(Loan $loan): JsonResponse
    {
        try {
            $loan = $this->loanService->returnBook($loan);
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), 409);
        }

        $loan->load(['member', 'book']);

        return $this->success('Book returned successfully', new LoanResource($loan));
    }

    public function myLoans(): JsonResponse
    {
        $member = auth('api')->user()?->member;

        if (! $member) {
            return $this->success('Loans retrieved successfully', []);
        }

        $loans = $member->loans()->with('book')->orderByDesc('id')->get();

        return $this->success('Loans retrieved successfully', LoanResource::collection($loans));
    }
}