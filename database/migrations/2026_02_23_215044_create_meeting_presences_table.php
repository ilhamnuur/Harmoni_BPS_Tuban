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
        Schema::create('meeting_presences', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel agendas (Rapat yang mana?)
            $table->foreignId('agenda_id')->constrained('agendas')->onDelete('cascade');
            
            // Relasi ke tabel users (Siapa yang tanda tangan?)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Menyimpan data gambar Tanda Tangan dalam format Base64 (LongText)
            $table->longText('signature_base64');
            
            // Mencatat waktu persis kapan dia tanda tangan (absen)
            $table->timestamp('signed_at');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_presences');
    }
};