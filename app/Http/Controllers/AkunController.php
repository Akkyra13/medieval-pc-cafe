<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Akun;
use App\Models\Zona;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
{

    protected function validated(Request $r, bool $isCreate = false): array
    {
        $cons = $r->validate([
            'username' => 'required|string|max:20|min:5|unique:akun,username',
            'password' => 'required|min:8',
            'nama' => 'required|string|min:5',
            'notelp' => 'nullable|string|max:20|min:10',
        ]);

        return $cons;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = Akun::orderBy('id_akun')->get();
        $zones = Zona::all();
        return view('admin.manajemen-akun', compact(['users', 'zones']));
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
        $d = $this->validated($request);

        Akun::create([
            'id_akses' => $d['id_akses'],
            'username' => $d['username'],
            'password' => Hash::make($d['password']),
            'nama' => $d['name'],
            'notelp' => $d['phone'],
        ]);

        return redirect()->route('admin.akun')->with('success', 'akun ditambahkan');
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

        // return view('admin.manajemen-edit-akun', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $this->validated($request);

        Akun::where('id_akun', $id)->update([
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'nama' => $data['name'],
            'notelp' => $data['phone'],
        ]);

        return redirect()->route('admin.akun')->with('success', 'perubahan disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Akun::findOrFail($id)->destroy();
        return redirect()->route('admin.akun')->with('success', 'akun dihapus');
    }
}
