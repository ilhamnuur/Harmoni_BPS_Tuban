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
        Schema::create('term_index', function (Blueprint $table) {
            $table->id();
            // Menghubungkan kata/term ke agenda tertentu
            $table->foreignId('agenda_id')->constrained('agendas')->onDelete('cascade');
            
            // Kolom untuk menyimpan kata (term)
            $table->string('term', 50);
            
            // Kolom untuk menyimpan nilai bobot TF-IDF
            $table->float('tfidf_weight');
            
            // Timestamps opsional, tapi baik untuk tracking
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('term_index');
    }
};