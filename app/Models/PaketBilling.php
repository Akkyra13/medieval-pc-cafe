<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketBilling extends Model
{
    protected $table = 'paket_billing';
    protected $primaryKey = 'id_paket';
    public $timestamps = false;

    protected $fillable = [
        'id_zona',
        'nama_paket',
        'durasi_jam',
        'harga_paket',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'id_zona', 'id_zona');
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_paket', 'id_paket');
    }
}
