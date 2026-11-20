<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;

// Route::get('/login', function () {
//     return view('login');
// })->name('login');

// Route::get('/login', [LoginController::class, 'login'])->name('login');
// Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// user pages
//
//
Route::middleware(['auth', 'role:2,3']) //checks if user is cashier or consumer
    ->group(function () {

        Route::get('/denah-pc', function () {
            return view('user.denah-pc');
        })->name('user.denah');

        Route::get('/beli-billing', function () {
            return view('user.beli-billing');
        })->name('user.billing');

        // Route::get('/profile', function () {
        // })->name('user.profile');
        Route::get('/profile', [ProfileController::class, 'index'])->name('user.profile');
    });

// admin pages
//
//

Route::middleware(['auth', 'isAdmin']) //middleware check if user is logged in as admin
    ->group(function () {

        Route::get('/admin', function () {
            return redirect()->route('admin.pc');
        });

        Route::get('/admin/manajemen-pc', function () {
            return view('admin.manajemen-pc');
        })->name('admin.pc');

        Route::get('/admin/manajemen-akun', function () {
            return view('admin.manajemen-akun');
        })->name('admin.akun');

        Route::get('/admin/manajemen-billing', function () {
            return view('admin.manajemen-billing');
        })->name('admin.billing');
    });
