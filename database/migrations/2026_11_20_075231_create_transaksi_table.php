<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->string('id_transaksi', 10)->primary();
            $table->foreignId('id_akun_pelanggan')->constrained('akun', 'id_akun')->restrictOnDelete();
            $table->foreignId('id_akun_kasir')->nullable()->constrained('akun', 'id_akun')->restrictOnDelete();
            $table->dateTime('waktu_transaksi');
            $table->integer('total_bayar');
            $table->string('metode_pembayaran', 20);
            $table->string('status_pembayaran', 15);
            $table->string('kode_referensi', 50)->nullable();
        });

        // 1. Function pembangkit id_transaksi otomatis (TRX-000001)
        DB::unprepared("
            CREATE OR REPLACE FUNCTION fn_generate_id_transaksi()
            RETURNS TRIGGER AS \$\$
            DECLARE
                v_last_num INT;
                v_next_num INT;
            BEGIN
                -- Kunci transaksi sementara agar tidak bentrok jika ada checkout bersamaan
                PERFORM pg_advisory_xact_lock(hashtext('transaksi_id_lock'));

                IF NEW.id_transaksi IS NULL OR TRIM(NEW.id_transaksi) = '' THEN
                    SELECT COALESCE(MAX(SUBSTRING(id_transaksi FROM 5 FOR 6)::INT), 0)
                    INTO v_last_num
                    FROM transaksi
                    WHERE id_transaksi ~ '^TRX-[0-9]{6}$';

                    v_next_num := v_last_num + 1;

                    IF v_next_num > 999999 THEN
                        RAISE EXCEPTION 'Kapasitas ID Transaksi sudah mencapai batas maksimal (TRX-999999)!';
                    END IF;

                    NEW.id_transaksi := 'TRX-' || LPAD(v_next_num::TEXT, 6, '0');
                END IF;

                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        // 2. Trigger BEFORE INSERT pada tabel transaksi
        DB::unprepared("
            CREATE TRIGGER trg_before_insert_transaksi
            BEFORE INSERT ON transaksi
            FOR EACH ROW
            EXECUTE FUNCTION fn_generate_id_transaksi();
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_before_insert_transaksi ON transaksi;');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_generate_id_transaksi();');
        Schema::dropIfExists('transaksi');
    }
};
