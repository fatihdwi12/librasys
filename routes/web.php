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
Route::view('/member', 'member.dashboard');
Route::view('/members', 'members')->name('members');
Route::view('/member/books', 'member.books');
Route::view('/member/loans', 'member.loans');