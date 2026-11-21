<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaketBilling;
use App\Models\Zona;

class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bil = PaketBilling::all();
        $zon = Zona::all();
        return view('user.beli-billing', compact(['bil', 'zon']));
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
