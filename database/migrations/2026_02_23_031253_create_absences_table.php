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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            // Pegawai yang sedang cuti
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('Cuti');
            $table->text('keterangan')->nullable();
            
            // Siapa yang input (Subbag Umum). 
            // Kita hubungkan ke tabel users juga agar datanya konsisten.
            $table->foreignId('input_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};