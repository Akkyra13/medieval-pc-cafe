<?php
use Illuminate\Foundation\Auth\User as Authenticatable;

class Akun extends Authenticatable
{
    protected $table = 'akun';
    protected $primaryKey = 'id_akun';
    public $timestamps = false;

    protected $fillable = [
        'id_akses',
        'username',
        'password',
        'nama',
        'notelp',
        'saldo_deposit',
    ];

    protected $hidden = [
        'password',
    ];

    public function hakAkses()
    {
        return $this->belongsTo(HakAkses::class, 'id_akses', 'id_akses');
    }

    public function saldoWaktu()
    {
        return $this->hasMany(SaldoWaktu::class, 'id_akun', 'id_akun');
    }

    public function transaksiPelanggan()
    {
        return $this->hasMany(Transaksi::class, 'id_akun_pelanggan', 'id_akun');
    }

    public function transaksiKasir()
    {
        return $this->hasMany(Transaksi::class, 'id_akun_kasir', 'id_akun');
    }

    public function sesiBermain()
    {
        return $this->hasMany(SesiBermain::class, 'id_akun', 'id_akun');
    }
}
