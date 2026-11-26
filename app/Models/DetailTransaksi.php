<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksi';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_transaksi',
        'id_paket',
        'qty',
        'durasi_beli_menit',
        'subtotal_harga',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }

    public function paketbilling()
    {
        return $this->belongsTo(PaketBilling::class, 'id_paket', 'id_paket');
    }
}
