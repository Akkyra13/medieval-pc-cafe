<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Akun;
use App\Models\SaldoWaktu;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index(Request $req)
    {
        $user = Akun::where('id_akun', Auth::id())->first();
        $billing = SaldoWaktu::where('id_akun', Auth::id());
        return view('user.profile', compact('user', 'billing'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }
}
