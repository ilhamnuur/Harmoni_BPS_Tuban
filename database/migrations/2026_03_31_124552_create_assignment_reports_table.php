<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('assignment_reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('agenda_id')->constrained('agendas')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users'); // Pegawai yang melapor
        $table->string('lokasi_tujuan'); // Lokasi translok ke-1, ke-2, dst
        $table->date('tanggal_lapor');
        $table->text('isi_laporan');
        $table->string('file_dokumentasi')->nullable();
        $table->enum('status_verifikasi', ['Pending', 'Verified'])->default('Pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_reports');
    }
};
