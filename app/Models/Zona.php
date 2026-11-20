<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $table = 'zona';
    protected $primaryKey = 'id_zona';
    public $timestamps = false;

    protected $fillable = [
        'nama_zona',
        'spek_cpu',
        'spek_gpu',
        'spek_ram',
        'spek_monitor',
    ];

    public function pc()
    {
        return $this->hasMany(Pc::class, 'id_zona', 'id_zona');
    }

    public function paketBilling()
    {
        return $this->hasMany(PaketBilling::class, 'id_zona', 'id_zona');
    }

    public function saldoWaktu()
    {
        return $this->hasMany(SaldoWaktu::class, 'id_zona', 'id_zona');
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_zona', 'id_zona');
    }
}
