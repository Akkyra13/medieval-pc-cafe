<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Pc;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Hak Akses
        DB::table('hak_akses')->insert([
            ['nama_hak_akses' => 'Admin'],
            ['nama_hak_akses' => 'Kasir'],
            ['nama_hak_akses' => 'Pelanggan'],
        ]);

        // Zona (Commoner, Noble, Royalty)
        DB::table('zona')->insert([
            [
                'nama_zona'     => 'Commoner',
                'spek_cpu'      => 'Intel Core i5-12400F',
                'spek_gpu'      => 'NVIDIA GTX 1660 Super',
                'spek_ram'      => '16GB DDR4',
                'spek_monitor'  => '24" IPS 144Hz',
            ],
            [
                'nama_zona'     => 'Noble',
                'spek_cpu'      => 'Intel Core i5-13400F',
                'spek_gpu'      => 'NVIDIA RTX 4060',
                'spek_ram'      => '16GB DDR5',
                'spek_monitor'  => '27" IPS 165Hz',
            ],
            [
                'nama_zona'     => 'Royalty',
                'spek_cpu'      => 'Intel Core i7-14700F',
                'spek_gpu'      => 'NVIDIA RTX 4070 Super',
                'spek_ram'      => '32GB DDR5',
                'spek_monitor'  => '27" OLED 240Hz',
            ],
        ]);

        // Akun (Semua password default: 123456)
        $passwordDefault = Hash::make('123456');

        DB::table('akun')->insert([
            [
                'id_akses' => 1,
                'username' => 'admin_fahri',
                'password' => $passwordDefault,
                'nama' => 'OP Fahri (Admin)',
                'notelp' => '081200001111',
                'saldo_deposit' => 0,
            ],
            [
                'id_akses' => 2,
                'username' => 'kasir_renata',
                'password' => $passwordDefault,
                'nama' => 'OP Renata (Shift Pagi)',
                'notelp' => '081200002222',
                'saldo_deposit' => 0,
            ],
            [
                'id_akses' => 3,
                'username' => 'capuri',
                'password' => $passwordDefault,
                'nama' => 'Oguri "Devour" Cap',
                'notelp' => '081299881122',
                'saldo_deposit' => 45000,
            ],
            [
                'id_akses' => 3,
                'username' => 'viper987',
                'password' => $passwordDefault,
                'nama' => 'Alex "Viper" Pratama',
                'notelp' => '081234567890',
                'saldo_deposit' => 15000,
            ],
            [
                'id_akses' => 3,
                'username' => 'waylay1023',
                'password' => $passwordDefault,
                'nama' => 'Waylay Pro',
                'notelp' => '084823484583',
                'saldo_deposit' => 120000,
            ],
        ]);

        // Pc
        $daftarPc = [
            ['id_zona' => 1, 'status_pc' => 'A'],
            ['id_zona' => 1, 'status_pc' => 'O'],
            ['id_zona' => 1, 'status_pc' => 'A'],
            ['id_zona' => 2, 'status_pc' => 'A'],
            ['id_zona' => 2, 'status_pc' => 'O'],
            ['id_zona' => 2, 'status_pc' => 'M'],
            ['id_zona' => 3, 'status_pc' => 'A'],
            ['id_zona' => 3, 'status_pc' => 'O'],
        ];

        foreach ($daftarPc as $pc) {
            Pc::create($pc);
        }

        // Paket Billing
        DB::table('paket_billing')->insert([
            ['id_zona' => 1, 'nama_paket' => 'Commoner Reguler 1 Jam', 'durasi_jam' => 1, 'harga_paket' => 5000, 'status_aktif' => true],
            ['id_zona' => 1, 'nama_paket' => 'Commoner Hemat 3 Jam', 'durasi_jam' => 3, 'harga_paket' => 13000, 'status_aktif' => true],
            ['id_zona' => 1, 'nama_paket' => 'Commoner Puas 5 Jam',  'durasi_jam' => 5, 'harga_paket' => 20000, 'status_aktif' => true],
            ['id_zona' => 2, 'nama_paket' => 'Noble Reguler 1 Jam', 'durasi_jam' => 1, 'harga_paket' => 8000, 'status_aktif' => true],
            ['id_zona' => 2, 'nama_paket' => 'Noble Hemat 3 Jam',    'durasi_jam' => 3, 'harga_paket' => 21000, 'status_aktif' => true],
            ['id_zona' => 2, 'nama_paket' => 'Noble Puas 5 Jam',     'durasi_jam' => 5, 'harga_paket' => 34000, 'status_aktif' => true],
            ['id_zona' => 3, 'nama_paket' => 'Royalty Reguler 1 Jam', 'durasi_jam' => 1, 'harga_paket' => 12000, 'status_aktif' => true],
            ['id_zona' => 3, 'nama_paket' => 'Royalty Hemat 3 Jam',  'durasi_jam' => 3, 'harga_paket' => 32000, 'status_aktif' => true],
            ['id_zona' => 3, 'nama_paket' => 'Royalty Puas 5 Jam',   'durasi_jam' => 5, 'harga_paket' => 50000, 'status_aktif' => true],
        ]);

        // Saldo Waktu
        DB::table('saldo_waktu')->insert([
            ['id_akun' => 3, 'id_zona' => 1, 'sisa_waktu_menit' => 275],
            ['id_akun' => 3, 'id_zona' => 2, 'sisa_waktu_menit' => 130],
            ['id_akun' => 3, 'id_zona' => 3, 'sisa_waktu_menit' => 0],
            ['id_akun' => 4, 'id_zona' => 1, 'sisa_waktu_menit' => 150],
            ['id_akun' => 4, 'id_zona' => 2, 'sisa_waktu_menit' => 10],
            ['id_akun' => 5, 'id_zona' => 3, 'sisa_waktu_menit' => 350],
        ]);

        // Transaksi
        $trx = DB::selectOne("
            INSERT INTO transaksi (
                id_akun_pelanggan, id_akun_kasir, waktu_transaksi,
                total_bayar, metode_pembayaran, status_pembayaran, kode_referensi
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
            RETURNING id_transaksi
        ", [3, 2, now(), 30000, 'Tunai', 'Lunas', null]);

        // Detail Transaksi
        DB::table('detail_transaksi')->insert([
            [
                'id_transaksi'      => $trx->id_transaksi,
                'id_paket'          => 1,
                'qty'               => 2,
                'durasi_beli_menit' => 120,
                'subtotal_harga'    => 10000,
            ],
            [
                'id_transaksi'      => $trx->id_transaksi,
                'id_paket'          => 3,
                'qty'               => 1,
                'durasi_beli_menit' => 300,
                'subtotal_harga'    => 20000,
            ],
        ]);

        // Sesi Bermain
        DB::table('sesi_bermain')->insert([
            [
                'id_pc' => 'CM-01',
                'id_akun' => 3,
                'waktu_login' => '2026-11-17 15:00:00',
                'waktu_logout' => '2026-11-17 17:00:00',
                'durasi_terpakai_menit' => 120,
            ],
            [
                'id_pc' => 'NB-02',
                'id_akun' => 3,
                'waktu_login' => '2026-11-18 11:35:00',
                'waktu_logout' => null,
                'durasi_terpakai_menit' => null,
            ],
            [
                'id_pc' => 'CM-02',
                'id_akun' => 4,
                'waktu_login' => '2026-11-18 12:00:00',
                'waktu_logout' => null,
                'durasi_terpakai_menit' => null,
            ],
            [
                'id_pc' => 'RY-02',
                'id_akun' => 5,
                'waktu_login' => '2026-11-18 10:15:00',
                'waktu_logout' => null,
                'durasi_terpakai_menit' => null,
            ],
        ]);
    }
}
