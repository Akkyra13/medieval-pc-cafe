<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login', 'halaman.login')->name('login');
Route::post('/login', [AuthController::class, 'userAuth']);

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::view('/admin', 'admin.dashboard')->name('admindashboard');
