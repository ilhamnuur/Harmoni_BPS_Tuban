<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            // Cek dulu, kalau belum ada kolom location baru buat
            if (!Schema::hasColumn('agendas', 'location')) {
                $table->string('location')->nullable();
            }
            
            // Cek dulu, kalau belum ada kolom yth baru buat
            if (!Schema::hasColumn('agendas', 'yth')) {
                $table->string('yth')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            // Gunakan array untuk drop sekaligus, tapi cek satu-satu agar tidak error saat rollback
            if (Schema::hasColumn('agendas', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('agendas', 'yth')) {
                $table->dropColumn('yth');
            }
        });
    }
};