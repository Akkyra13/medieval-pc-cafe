<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoWaktu extends Model
{
    protected $table = 'saldo_waktu';
    protected $primaryKey = 'id_akun';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_akun',
        'id_zona',
        'sisa_waktu_menit',
    ];

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun', 'id_akun');
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'id_zona', 'id_zona');
    }
}
