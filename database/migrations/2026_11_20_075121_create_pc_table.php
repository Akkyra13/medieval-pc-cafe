<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pc', function (Blueprint $table) {
            $table->char('id_pc', 5)->primary();
            $table->foreignId('id_zona')->constrained('zona', 'id_zona')->restrictOnDelete();
            $table->string('ip_address', 15)->unique();
            $table->char('status_pc', 1)->default('A');
        });

        // 1. Buat Function pembangkit id_pc otomatis per zona
        DB::unprepared("
            CREATE OR REPLACE FUNCTION fn_generate_id_pc()
            RETURNS TRIGGER AS \$\$
            DECLARE
                v_prefix VARCHAR(2);
                v_last_num INT;
                v_next_num INT;
            BEGIN
                -- Kunci transaksi per zona agar tidak bentrok jika ada insert bersamaan
                PERFORM pg_advisory_xact_lock(NEW.id_zona);

                -- Mapping kode prefix berdasarkan id_zona
                v_prefix := CASE NEW.id_zona
                    WHEN 1 THEN 'CM'
                    WHEN 2 THEN 'NB'
                    WHEN 3 THEN 'RY'
                    ELSE 'PC'
                END;

                -- Generate otomatis hanya jika id_pc tidak diisi manual
                IF NEW.id_pc IS NULL OR TRIM(NEW.id_pc) = '' THEN
                    SELECT COALESCE(MAX(SUBSTRING(id_pc FROM 4 FOR 2)::INT), 0)
                    INTO v_last_num
                    FROM pc
                    WHERE id_zona = NEW.id_zona;

                    v_next_num := v_last_num + 1;

                    -- Guardrail karena char(5) maksimal menampung 2 digit (XX-99)
                    IF v_next_num > 99 THEN
                        RAISE EXCEPTION 'Kapasitas PC untuk zona % sudah penuh (maksimal %-99)!', NEW.id_zona, v_prefix;
                    END IF;

                    NEW.id_pc := v_prefix || '-' || LPAD(v_next_num::TEXT, 2, '0');
                END IF;

                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        // 2. Pasang Trigger BEFORE INSERT pada tabel pc
        DB::unprepared("
            CREATE TRIGGER trg_before_insert_pc
            BEFORE INSERT ON pc
            FOR EACH ROW
            EXECUTE FUNCTION fn_generate_id_pc();
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_before_insert_pc ON pc;');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_generate_id_pc();');
        Schema::dropIfExists('pc');
    }
};
