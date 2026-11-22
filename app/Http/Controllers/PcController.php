<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pc;
use App\Models\Zona;
use Illuminate\Support\Facades\Auth;

class PcController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected function validated(Request $d, bool $isCreate = false): array
    {
        $data = [
            'id_zona' => 'required|exists:zona,id_zona',
            'ip_address' => 'required|ip|unique:pc,ip_address',
            'status_pc' => 'nullable|string|max:20',
        ];

        if ($isCreate) {
            $data['id_pc'] = 'required|string|max:5|unique:pc,id_pc';
        }

        return $d->validate($data);
    }


    public function index()
    {
        $pcs = Pc::orderBy('id_zona', 'asc')
            ->orderBy('id_pc', 'asc')
            ->get();
        $zones = Zona::all();

        if (Auth::user()->id_akses === 1) {
            $output = view('admin.manajemen-pc', compact(['pcs', 'zones']));
        } else {
            $output = view('user.denah-pc', compact(['pcs', 'zones']));
        }
        return $output;
    }

    // public function index2()
    // {
    //     $data = Pc::all();
    //     $output = view('user.denah-pc', compact('data'));
    //     return $output;
    // }
    //
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
        $data = $this->validated($request, true);

        Pc::create($data);

        return redirect()->route('admin.pc')->with('success', 'PC berhasil ditambahkan');
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
        Pc::findOrFail($id)->delete()
            ->redirect()->route('admin.pc')->with('success', 'PC dihabus');
    }
}
