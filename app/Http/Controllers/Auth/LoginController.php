<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Akun;

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

    public function login(Request $req)
    {
        $data = $this->validated($req);

        if (Auth::attempt($data)) {
            $req->session()->regenerate();
            $user = Akun::where('id_akun', Auth::id())->first();
            session(['akses' => $user?->id_akses ]);

            if ($user->id_akses === 1) {
                return redirect()->intended(route('admin.pc'))
                    ->with('success', 'Login Berhasil');
            } else {
                return redirect()->intended($this->redirectTo)
                    ->with('success', 'Login Berhasil');
            }
        }

        return back()->withErrors(['username' => 'Login gagal']);
    }

    public function logout(Request $req)
    {
        Auth::logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();
        return redirect('/login');
    }
}
