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
            // Tambahkan kolom baru di sini
            $table->string('print_mode')->default('perorang')->after('mode_surat')->nullable();
            $table->text('menimbang')->after('print_mode')->nullable();
            $table->text('mengingat')->after('menimbang')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn(['print_mode', 'menimbang', 'mengingat']);
        });
    }
};