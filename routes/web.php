<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::view('/login', 'login')->name('login');
Route::view('/register', 'register')->name('register');
Route::view('/dashboard', 'dashboard');

Route::view('/member', 'member.dashboard');
Route::view('/member/books', 'member.books');
Route::view('/member/loans', 'member.loans');