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

    // Admin dan anggota: hanya baca
    Route::get('books', [BookController::class, 'index']);
    Route::get('books/{book}', [BookController::class, 'show']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    Route::get('my-loans', [LoanController::class, 'myLoans']);

    // Hanya admin: pengelolaan data
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
        Route::apiResource('books', BookController::class)->except(['index', 'show']);
        Route::apiResource('members', MemberController::class);

        Route::put('loans/{loan}/return', [LoanController::class, 'returnBook']);
        Route::apiResource('loans', LoanController::class);
    });
});