<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaketBilling;
use App\Models\Zona;
use App\Models\Transaksi;

class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eceran = PaketBilling::with('zona')->where('durasi_jam', 1)->get();
        $paket = PaketBilling::with('zona')->whereNot('durasi_jam', 1)->get();
        $zones = Zona::all();
        if (in_array(Auth::user()->id_akses, [1,2])) {
            $transactions = Transaksi::with('detailTransaksi', 'pelanggan')->latest('waktu_transaksi')->get();
            return view('admin.manajemen-billing', compact('paket', 'eceran', 'zones', 'transactions'));
        } else {
            return view('user.beli-billing', compact('paket', 'eceran', 'zones'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
