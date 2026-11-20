<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiBermain extends Model
{
    protected $table = 'sesi_bermain';
    protected $primaryKey = 'id_sesi';
    public $timestamps = false;

    protected $fillable = [
        'id_pc',
        'id_akun',
        'waktu_login',
        'waktu_logout',
        'durasi_terpakai_menit',
    ];

    public function pc()
    {
        return $this->belongsTo(Pc::class, 'id_pc', 'id_pc');
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun', 'id_akun');
    }
}
