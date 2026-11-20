<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

// Route::view('/login', 'halaman.login')->name('login');
// Route::post('/login', [AuthController::class, 'userAuth']);

Auth::routes();

Route::get('/profile', [ProfileController::class, 'index'])->name('profile');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
