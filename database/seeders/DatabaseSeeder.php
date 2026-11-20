<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Hak Akses
        DB::table('hak_akses')->insert([
            ['nama_hak_akses' => 'Admin'],
            ['nama_hak_akses' => 'Kasir'],
            ['nama_hak_akses' => 'Pelanggan'],
        ]);

        // 2. Zona (Commoner, Noble, Royalty)
        DB::table('zona')->insert([
            [
                'nama_zona' => 'Commoner',
                'spek_cpu' => 'Intel Core i5-12400F',
                'spek_gpu' => 'NVIDIA RTX 4060 8GB',
                'spek_ram' => '16GB DDR4 3200MHz',
                'spek_monitor' => '24" 240Hz Gaming IPS',
            ],
            [
                'nama_zona' => 'Noble',
                'spek_cpu' => 'Intel Core i7-13700F',
                'spek_gpu' => 'NVIDIA RTX 4070 Ti 12GB',
                'spek_ram' => '32GB DDR5 5600MHz',
                'spek_monitor' => '27" 2K 240Hz Fast IPS',
            ],
            [
                'nama_zona' => 'Royalty',
                'spek_cpu' => 'AMD Ryzen 9 7900X',
                'spek_gpu' => 'NVIDIA RTX 4090 24GB (Dual PC)',
                'spek_ram' => '64GB DDR5 6000MHz',
                'spek_monitor' => '27" OLED 360Hz + Shure SM7B',
            ],
        ]);

        // 3. Akun (Semua password default: 123456)
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

        // 4. PC
        DB::table('pc')->insert([
            ['id_pc' => 'PC-01', 'id_zona' => 1, 'ip_address' => '192.168.1.101', 'status_pc' => 'Tersedia'],
            ['id_pc' => 'PC-02', 'id_zona' => 1, 'ip_address' => '192.168.1.102', 'status_pc' => 'Digunakan'],
            ['id_pc' => 'PC-03', 'id_zona' => 1, 'ip_address' => '192.168.1.103', 'status_pc' => 'Tersedia'],
            ['id_pc' => 'NB-01', 'id_zona' => 2, 'ip_address' => '192.168.1.201', 'status_pc' => 'Tersedia'],
            ['id_pc' => 'NB-04', 'id_zona' => 2, 'ip_address' => '192.168.1.204', 'status_pc' => 'Digunakan'],
            ['id_pc' => 'NB-05', 'id_zona' => 2, 'ip_address' => '192.168.1.205', 'status_pc' => 'Maintenance'],
            ['id_pc' => 'RY-01', 'id_zona' => 3, 'ip_address' => '192.168.1.301', 'status_pc' => 'Tersedia'],
            ['id_pc' => 'RY-02', 'id_zona' => 3, 'ip_address' => '192.168.1.302', 'status_pc' => 'Digunakan'],
        ]);

        // 5. Paket Billing
        DB::table('paket_billing')->insert([
            ['id_zona' => 1, 'nama_paket' => 'Paket Santai Commoner', 'durasi_jam' => 2, 'harga_paket' => 14000, 'status_aktif' => true],
            ['id_zona' => 1, 'nama_paket' => 'Paket Mabar Commoner', 'durasi_jam' => 5, 'harga_paket' => 30000, 'status_aktif' => true],
            ['id_zona' => 1, 'nama_paket' => 'Paket Begadang Commoner', 'durasi_jam' => 8, 'harga_paket' => 45000, 'status_aktif' => true],
            ['id_zona' => 2, 'nama_paket' => 'Noble Pass 2 Jam', 'durasi_jam' => 2, 'harga_paket' => 24000, 'status_aktif' => true],
            ['id_zona' => 2, 'nama_paket' => 'Noble Push Rank 5 Jam', 'durasi_jam' => 5, 'harga_paket' => 55000, 'status_aktif' => true],
            ['id_zona' => 3, 'nama_paket' => 'Royal Stream Pass 3 Jam', 'durasi_jam' => 3, 'harga_paket' => 75000, 'status_aktif' => true],
            ['id_zona' => 3, 'nama_paket' => 'Paket Promo Lama (Nonaktif)', 'durasi_jam' => 4, 'harga_paket' => 80000, 'status_aktif' => false],
        ]);

        // 6. Saldo Waktu
        DB::table('saldo_waktu')->insert([
            ['id_akun' => 3, 'id_zona' => 1, 'sisa_waktu_menit' => 275],
            ['id_akun' => 3, 'id_zona' => 2, 'sisa_waktu_menit' => 130],
            ['id_akun' => 3, 'id_zona' => 3, 'sisa_waktu_menit' => 0],
            ['id_akun' => 4, 'id_zona' => 1, 'sisa_waktu_menit' => 150],
            ['id_akun' => 4, 'id_zona' => 2, 'sisa_waktu_menit' => 10],
            ['id_akun' => 5, 'id_zona' => 3, 'sisa_waktu_menit' => 350],
        ]);

        // 7. Transaksi
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

        // 8. Detail Transaksi
        DB::table('detail_transaksi')->insert([
            ['id_transaksi' => 'NX-9938210', 'id_zona' => 2, 'durasi_beli_menit' => 120, 'subtotal_harga' => 24000],
            ['id_transaksi' => 'NX-9934112', 'id_zona' => 1, 'durasi_beli_menit' => 300, 'subtotal_harga' => 30000],
            ['id_transaksi' => 'NX-9921094', 'id_zona' => 1, 'durasi_beli_menit' => 120, 'subtotal_harga' => 14000],
            ['id_transaksi' => 'NX-9908842', 'id_zona' => 3, 'durasi_beli_menit' => 180, 'subtotal_harga' => 75000],
        ]);

        // 9. Sesi Bermain
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
