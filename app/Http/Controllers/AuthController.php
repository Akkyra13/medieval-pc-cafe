<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function userAuth(Request $req)
    {
        $creds = $req->validate([
            'username' => ['required', 'username'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($creds)) {
            $req->session()->regenerate();

            $route = $req->user()->role === 'admin' ? 'admin.dashboard' : 'dashboard';
            return redirect()->intended(route($route));
        }

        return back()->withErrors(['username' => 'Invalid Credentials']);
    }
}
