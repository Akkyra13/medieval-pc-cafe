<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Akun;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{
    protected function validated(Request $r): array
    {
        $const = $r->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
            ],
            [
            'current_password.required' => 'wajib diisi',
            'passowrd.required' => 'wajib diisi',
            'password.min' => 'minimal 8 karakter',
            ]
        );

        return $const;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user()->load([
            'saldoWaktu',
            'sesiBermain' => fn ($r) => $r->whereNull('waktu_logout'),
        ]);

        $transaksi = Transaksi::with('detailTransaksi')
            ->where('id_akun_pelanggan', Auth::id())
            ->orderByDesc('waktu_transaksi')
            ->get();

        $billing = $user()->saldoWaktu->pluck('sisa_waktu_menit', 'id_zona');
        // $user = Akun::with('saldoWaktu.zona', 'sesiBermain', 'transaksiPelanggan')->where('id_akun', Auth::id())->get();
        // $transaksi = Transaksi::whereHas('detailTransaksi', fn ($r) => $r->where('id_akun_pelanggan', Auth::id())->orderByDesc('waktu_transaksi'))->get();
        // $billing = SaldoWaktu::where('id_akun', Auth::id());
        // $transactions = Transaksi::all();
        return view('user.profile', compact('user', 'transaksi', 'billing'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $this->validated($request);

        $request->user()->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('user.profile')
            ->with('success', 'Password berhasil di perbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
