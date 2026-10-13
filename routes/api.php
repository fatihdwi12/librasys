<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\MemberController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::put('/change-password', [
        AuthController::class,
        'changePassword',
    ])->middleware('throttle:5,1');

    // Akses Baca (Admin & Member)
    Route::get('books', [BookController::class, 'index']);
    Route::get('books/{book}', [BookController::class, 'show']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    Route::get('my-loans', [LoanController::class, 'myLoans']);

    // Akses Kelola (Hanya Admin)
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
        Route::apiResource('books', BookController::class)->except(['index', 'show']);
        Route::apiResource('members', MemberController::class);

        Route::put('members/{member}/password', [
            MemberController::class,
            'resetPassword',
        ])->middleware('throttle:5,1');

        Route::post('members/{member}/account', [
            MemberController::class,
            'createAccount',
        ])->middleware('throttle:5,1');

        // Endpoint Khusus Pengembalian Buku
        Route::put('/loans/{loan}/return', [LoanController::class, 'returnBook']);

        Route::apiResource('loans', LoanController::class);
    });
});