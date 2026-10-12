<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use DomainException;
use Illuminate\Support\Facades\DB;

class LoanService
{
    public function borrow(array $data): Loan
    {
        return DB::transaction(function () use ($data) {
            $member = Member::findOrFail($data['member_id']);
            $book = Book::lockForUpdate()->findOrFail($data['book_id']);

            if ($member->status !== 'active') {
                throw new DomainException('Member is not active');
            }

            if ($book->stock < 1) {
                throw new DomainException('Book is out of stock');
            }

            $loan = Loan::create([
                'member_id' => $member->id,
                'book_id' => $book->id,
                'borrowed_at' => now(),
                'due_at' => $data['due_at'],
                'status' => 'borrowed',
            ]);

            $book->decrement('stock');

            return $loan;
        });
    }

    public function returnBook(Loan $loan): Loan
    {
        return DB::transaction(function () use ($loan) {
            $loan = Loan::lockForUpdate()->findOrFail($loan->id);

            if ($loan->status !== 'borrowed') {
                throw new DomainException('Loan has already been returned');
            }

            $loan->update([
                'returned_at' => now(),
                'status' => 'returned',
            ]);

            $loan->book()->increment('stock');

            return $loan;
        });
    }
}