<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pc extends Model
{
    protected $table = 'pc';
    protected $primaryKey = 'id_pc';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_pc',
        'id_zona',
        'ip_address',
        'status_pc',
    ];

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'id_zona', 'id_zona');
    }

    public function sesiBermain()
    {
        return $this->hasMany(SesiBermain::class, 'id_pc', 'id_pc');
    }
}
