<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::view('/login', 'login')->name('login');
Route::view('/register', 'register')->name('register');
Route::view('/dashboard', 'dashboard');
Route::view('/categories', 'categories')->name('categories');
Route::view('/books', 'books')->name('books');
Route::view('/accounts/password', 'accounts.password')
    ->name('account.password');
Route::view('/loans', 'loans')->name('loans');
Route::view('/returns', 'returns')->name('returns'); // <-- Rute Baru Halaman Pengembalian
Route::view('/members', 'members')->name('members');

// Rute Member Panel
Route::view('/member', 'member.dashboard');
Route::view('/member/books', 'member.books');
Route::view('/member/loans', 'member.loans');