<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Akun;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/profile';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function validated(Request $data): array
    {
        $output = $data->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        return $output;
    }

    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $req)
    {
        $data = $this->validated($req);
        $key = Str::lower($data['username']) . '|' . $req->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $cd = RateLimiter::availableIn($key);
            return back()->withErrors([
                'username' => "Login dikunci untuk {$cd} detik",
            ])->onlyInput('username');
        }

        if (Auth::attempt($data)) {
            RateLimiter::clear($key);
            $req->session()->regenerate();
            $user = Akun::where('id_akun', Auth::id())->first();
            session(['akses' => $user?->id_akses ]);

            return redirect()->route($user->homeRoute())
                ->with('success', 'Login Berhasil');
        }
        RateLimiter::hit($key, 60);

        return back()->withErrors(['username' => 'Login gagal'])->onlyInput('username');
    }

    public function logout(Request $req)
    {
        Auth::logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();
        return redirect('/login');
    }
}
