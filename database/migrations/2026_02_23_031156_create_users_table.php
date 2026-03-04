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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap', 100);
            $table->string('nip', 20)->nullable(); // Menambahkan NIP (Nullable jika ada mitra yang tidak punya NIP)
            $table->string('username', 50)->unique(); 
            $table->string('password');
            
            // Role user untuk pembagian hak akses
            $table->enum('role', ['Admin', 'Katim', 'Pegawai'])->default('Pegawai');
            
            // Relasi ke tabel teams
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null');
            
            $table->rememberToken(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};