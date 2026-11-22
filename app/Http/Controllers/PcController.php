<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pc;
use App\Models\Zona;
use App\Models\SesiBermain;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class PcController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected function validated(Request $d, ?string $id = null): array
    {
        $data = [
            'id_zona' => 'required|exists:zona,id_zona',
            'ip_address' => ['required', 'ipv4', Rule::unique('pc', 'ip_address')->ignore($id, 'id_pc')],
            'status_pc' => 'nullable|string|max:20',
        ];

        if ($id === null) {
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
        $sessions = SesiBermain::whereNull('waktu_logout')
            ->whereNotNull('waktu_login')->get();

        if (Auth::user()->id_akses === 1) {
            $output = view('admin.manajemen-pc', compact(['pcs', 'zones', 'sessions']));
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
        $data = $this->validated($request);

        Pc::create([
            'id_pc' => $data['id_pc'],
            'id_zona' => $data['id_zona'],
            'status_pc' => $data['status_pc'],
        ]);

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
        $d = $this->validated($request, $id);
        Pc::where('id_pc', $id)->update([
            'status_pc' => $d['status'],
            'ip_address' => $d['ip_address'],
            'id_zona' => $d['id_zona'],
        ]);
        return redirect()->route('admin.pc')->with('success', 'Informasi diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pc = Pc::findOrFail($id);

        if ($pc->status_pc === 'Digunakan') {
            return redirect()->route('admin.pc')->with('error', 'pc sedang digunakan');
        }
        $pc->delete();
        return redirect()->route('admin.pc')->with('success', 'PC dihapus');
    }
}
