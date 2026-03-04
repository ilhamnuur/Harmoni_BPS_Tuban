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
        Schema::create('agenda_photos', function (Blueprint $table) {
            $table->id();
            // Menghubungkan foto ke agenda tertentu
            // onDelete('cascade') artinya jika agenda dihapus, foto-fotonya otomatis ikut terhapus
            $table->foreignId('agenda_id')->constrained('agendas')->onDelete('cascade');
            
            // Kolom untuk menyimpan path/lokasi file foto di storage
            $table->string('photo_path');
            
            // Keterangan singkat untuk foto tersebut (opsional)
            $table->text('description')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_photos');
    }
};