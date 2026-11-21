<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Akun;

class AkunController extends Controller
{

    protected function validated(Request $r, bool $isCreate = false): array
    {
        $cons = $r->validate([
            'username' => 'required|string|max:20|min:5|unique:akun, username',
            'password' => 'required|min:8',
            'nama' => 'required|string|min:5',
            'notelp' => 'nullable|string|max:20|min:5|unique:akun, username',
        ]);

        return $cons;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Akun::orderBy('id_akun')->get();
        return view('admin.manajemen-akun', compact('data'));
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
        $data = $this->validate($request);

        Akun::create($data);

        return redirect()->route('admin.akun')->with('success', 'Akun Ditambahkan');
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
