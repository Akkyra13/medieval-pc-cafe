<?php

namespace App\Http\Controllers;

use App\Models\PaketBilling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        // $data = $this->validated($request);
        //
        // $bill = PaketBilling::findOrFail($data['id_paket']);
        //
        // DB::transaction(function () use ($bill)) {
        //
        //     DB::create(
        //         "insert into "
        //     )
        //     return redirect()->route('user.profile')->with('success', 'billing di tambah');
        //     }
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
