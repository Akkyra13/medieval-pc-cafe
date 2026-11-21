<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PcController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\AkunController;

// Route::get('/login', function () {
//     return view('login');
// })->name('login');

// Route::get('/login', [LoginController::class, 'login'])->name('login');
// Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route::view('/login', 'halaman.login')->name('login');
// Route::post('/login', [AuthController::class, 'userAuth']);

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// user pages
//
//
Route::middleware(['auth', 'role:3']) //checks if user is consumer
    ->group(function () {

        Route::resource('denah-pc', PcController::class)
            ->only(['index'])->name('index', 'user.denah');

        Route::resource('beli-billing', BillingController::class)
            ->only(['index'])->name('index', 'user.billing');

        // Route::get('/profile', function () {
        // })->name('user.profile');
        Route::get('/profile', [ProfileController::class, 'index'])->name('user.profile');
    });

Route::middleware(['auth', 'role:1,2']) // checks for cashier and admin users
    ->group(function () {

        Route::get('/admin/manajemen-billing', function () {
            return view('admin.manajemen-billing');
        })->name('admin.billing');
    });

// admin pages
//
//

Route::middleware(['auth', 'isAdmin']) //middleware check if user is logged in as admin
    ->group(function () {

        Route::get('/admin', function () {
            return redirect()->route('admin.pc');
        });

        Route::resource('admin/manajemen-pc', PcController::class)
            ->except('show')->names([
                'index' => 'admin.pc',
                'update' => 'admin.pc.update',
                'store' => 'admin.pc.store',
                'destroy' => 'admin.pc.destroy',
            ]);

        // Route::get('/admin/manajemen-pc', function () {
        //     return view('admin.manajemen-pc');
        // })->name('admin.pc');

        Route::resource('admin/manajemen-akun', AkunController::class)
            ->names([
                'index' => 'admin.akun',
                'show' => 'admin.akun.show',
                'update' => 'admin.akun.update',
                'store' => 'admin.akun.store',
                'destroy' => 'admin.akun.destroy',
            ]);

        // Route::get('/admin/manajemen-akun', function () {
        //     return view('admin.manajemen-akun');
        // })->name('admin.akun');

        Route::get('/admin/manajemen-billing', function () {
            return view('admin.manajemen-billing');
        })->name('admin.billing');
    });
