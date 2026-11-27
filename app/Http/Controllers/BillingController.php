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
        // $zones = Zona::all();
        $transactions = Transaksi::with('detailTransaksi', 'pelanggan')->get();
        if (Auth::user()->id_akses === 1) {
            return view('admin.manajemen-billing', compact('paket', 'eceran', 'transactions'));
        } else {
            return view('user.beli-billing', compact('paket', 'eceran'));
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
