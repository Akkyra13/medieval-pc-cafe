<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_transaksi',
        'id_akun_pelanggan',
        'id_akun_kasir',
        'waktu_transaksi',
        'total_bayar',
        'metode_pembayaran',
        'status_pembayaran',
        'kode_referensi',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Akun::class, 'id_akun_pelanggan', 'id_akun');
    }

    public function kasir()
    {
        return $this->belongsTo(Akun::class, 'id_akun_kasir', 'id_akun');
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }
}
