<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Pc extends Model
{
    protected $table = 'pc';
    protected $primaryKey = 'id_pc';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public const STATUS_AVAILABLE = 'A';
    public const STATUS_OCCUPIED = 'O';
    public const STATUS_MAINTENANCE = 'M';

    protected $fillable = [
        'id_pc',
        'id_zona',
        'ip_address',
        'status_pc',
    ];

    protected static function booted(): void
    {
        static::creating(function (Pc $pc) {
            // Jika ip_address tidak diisi manual, sistem otomatis buatkan sesuai zona
            if (empty($pc->ip_address)) {
                // Ambil semua IP yang sudah ada di zona ini, lalu cari oktet ke-4 terbesar
                $lastOctet = self::where('id_zona', $pc->id_zona)
                    ->pluck('ip_address')
                    ->map(fn ($ip) => (int) explode('.', $ip)[3])
                    ->max() ?? 0;

                $nextOctet = $lastOctet + 1;

                // Limit otomatis oktet ke-4 maksimal 254
                if ($nextOctet > 254) {
                    throw new Exception("Kapasitas IP untuk Zona {$pc->id_zona} sudah penuh (maksimal .254)!");
                }

                $pc->ip_address = "192.168.{$pc->id_zona}.{$nextOctet}";
            }
        });
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_pc) {
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_OCCUPIED => 'Occupied',
            self::STATUS_MAINTENANCE => 'Maintenance',
            default => 'Unknown',
        };
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'id_zona', 'id_zona');
    }

    public function sesiBermain()
    {
        return $this->hasMany(SesiBermain::class, 'id_pc', 'id_pc');
    }
}
