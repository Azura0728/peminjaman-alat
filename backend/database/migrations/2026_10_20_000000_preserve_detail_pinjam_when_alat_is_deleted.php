<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_pinjam', function (Blueprint $table) {
            $table->dropForeign(['alat_id']);
            $table->unsignedBigInteger('alat_id')->nullable()->change();
            $table->foreign('alat_id')->references('id')->on('alat')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('detail_pinjam', function (Blueprint $table) {
            $table->dropForeign(['alat_id']);
            $table->foreign('alat_id')->references('id')->on('alat')->cascadeOnDelete();
        });
    }
};
