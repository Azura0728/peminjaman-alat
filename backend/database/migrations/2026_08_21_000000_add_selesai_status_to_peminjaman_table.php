<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("
            ALTER TABLE peminjaman
            MODIFY status ENUM(
                'diajukan',
                'dipinjam',
                'dikembalikan',
                'selesai',
                'telat'
            ) NOT NULL DEFAULT 'diajukan'
        ");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("
            ALTER TABLE peminjaman
            MODIFY status ENUM(
                'diajukan',
                'dipinjam',
                'dikembalikan',
                'telat'
            ) NOT NULL DEFAULT 'diajukan'
        ");
    }
};
