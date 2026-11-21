<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/logout', function () {
    return redirect()->route('login');
})->name('logout');

Route::get('/denah-pc', function () {
    return view('user.denah-pc');
})->name('user.denah');

Route::get('/beli-billing', function () {
    return view('user.beli-billing');
})->name('user.billing');

Route::get('/profile', function () {
    return view('user.profile');
})->name('user.profile');

Route::get('/admin/manajemen-pc', function () {
    return view('admin.manajemen-pc');
})->name('admin.pc');

Route::get('/admin/manajemen-akun', function () {
    return view('admin.manajemen-akun');
})->name('admin.akun');

Route::get('/admin/manajemen-billing', function () {
    return view('admin.manajemen-billing');
})->name('admin.billing');