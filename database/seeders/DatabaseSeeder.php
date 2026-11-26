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
                'harga_per_jam' => 5000,
                'spek_cpu'      => 'Intel Core i5-12400F',
                'spek_gpu'      => 'NVIDIA GTX 1660 Super',
                'spek_ram'      => '16GB DDR4',
                'spek_monitor'  => '24" IPS 144Hz',
            ],
            [
                'nama_zona'     => 'Noble',
                'harga_per_jam' => 8000,
                'spek_cpu'      => 'Intel Core i5-13400F',
                'spek_gpu'      => 'NVIDIA RTX 4060',
                'spek_ram'      => '16GB DDR5',
                'spek_monitor'  => '27" IPS 165Hz',
            ],
            [
                'nama_zona'     => 'Royalty',
                'harga_per_jam' => 12000,
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
            ['id_zona' => 1, 'status_pc' => 'A'], // otomatis jadi CM-01 & 192.168.1.1
            ['id_zona' => 1, 'status_pc' => 'O'], // otomatis jadi CM-02 & 192.168.1.2
            ['id_zona' => 1, 'status_pc' => 'A'], // otomatis jadi CM-03 & 192.168.1.3
            ['id_zona' => 2, 'status_pc' => 'A'], // otomatis jadi NB-01 & 192.168.2.1
            ['id_zona' => 2, 'status_pc' => 'O'], // otomatis jadi NB-02 & 192.168.2.2
            ['id_zona' => 2, 'status_pc' => 'M'], // otomatis jadi NB-03 & 192.168.2.3
            ['id_zona' => 3, 'status_pc' => 'A'], // otomatis jadi RY-01 & 192.168.3.1
            ['id_zona' => 3, 'status_pc' => 'O'], // otomatis jadi RY-02 & 192.168.3.2
        ];

        foreach ($daftarPc as $pc) {
            Pc::create($pc);
        }

        // 5. PAKET BILLING (Murni paket bundling/hemat)
        DB::table('paket_billing')->insert([
            ['id_zona' => 1, 'nama_paket' => 'Commoner Hemat 3 Jam', 'durasi_jam' => 3, 'harga_paket' => 13000, 'status_aktif' => true],
            ['id_zona' => 1, 'nama_paket' => 'Commoner Puas 5 Jam',  'durasi_jam' => 5, 'harga_paket' => 20000, 'status_aktif' => true],
            ['id_zona' => 2, 'nama_paket' => 'Noble Hemat 3 Jam',    'durasi_jam' => 3, 'harga_paket' => 21000, 'status_aktif' => true],
            ['id_zona' => 2, 'nama_paket' => 'Noble Puas 5 Jam',     'durasi_jam' => 5, 'harga_paket' => 34000, 'status_aktif' => true],
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
        DB::table('transaksi')->insert([
            [
                'id_transaksi' => 'NX-9938210',
                'id_akun_pelanggan' => 3,
                'id_akun_kasir' => null,
                'waktu_transaksi' => '2026-11-18 10:24:00',
                'total_bayar' => 24000,
                'metode_pembayaran' => 'QRIS Dinamis',
                'status_pembayaran' => 'Sukses',
                'kode_referensi' => 'MID-QRIS-882910A',
            ],
            [
                'id_transaksi' => 'NX-9934112',
                'id_akun_pelanggan' => 3,
                'id_akun_kasir' => null,
                'waktu_transaksi' => '2026-11-15 14:10:00',
                'total_bayar' => 30000,
                'metode_pembayaran' => 'BCA Virtual Account',
                'status_pembayaran' => 'Sukses',
                'kode_referensi' => 'MID-VA-771293B',
            ],
            [
                'id_transaksi' => 'NX-9921094',
                'id_akun_pelanggan' => 4,
                'id_akun_kasir' => 2,
                'waktu_transaksi' => '2026-11-16 20:05:00',
                'total_bayar' => 14000,
                'metode_pembayaran' => 'Tunai Kasir',
                'status_pembayaran' => 'Sukses',
                'kode_referensi' => 'CSH-20261116-01',
            ],
            [
                'id_transaksi' => 'NX-9908842',
                'id_akun_pelanggan' => 5,
                'id_akun_kasir' => null,
                'waktu_transaksi' => '2026-11-18 12:55:00',
                'total_bayar' => 75000,
                'metode_pembayaran' => 'Mandiri VA',
                'status_pembayaran' => 'Pending',
                'kode_referensi' => 'MID-VA-990128C',
            ],
        ]);

        // Detail Transaksi
        DB::table('detail_transaksi')->insert([
            ['id_transaksi' => 'NX-9938210', 'id_zona' => 2, 'durasi_beli_menit' => 120, 'subtotal_harga' => 24000],
            ['id_transaksi' => 'NX-9934112', 'id_zona' => 1, 'durasi_beli_menit' => 300, 'subtotal_harga' => 30000],
            ['id_transaksi' => 'NX-9921094', 'id_zona' => 1, 'durasi_beli_menit' => 120, 'subtotal_harga' => 14000],
            ['id_transaksi' => 'NX-9908842', 'id_zona' => 3, 'durasi_beli_menit' => 180, 'subtotal_harga' => 75000],
        ]);

        // Sesi Bermain
        DB::table('sesi_bermain')->insert([
            [
                'id_pc' => 'PC-01',
                'id_akun' => 3,
                'waktu_login' => '2026-11-17 15:00:00',
                'waktu_logout' => '2026-11-17 17:00:00',
                'durasi_terpakai_menit' => 120,
            ],
            [
                'id_pc' => 'NB-04',
                'id_akun' => 3,
                'waktu_login' => '2026-11-18 11:35:00',
                'waktu_logout' => null,
                'durasi_terpakai_menit' => null,
            ],
            [
                'id_pc' => 'PC-02',
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
