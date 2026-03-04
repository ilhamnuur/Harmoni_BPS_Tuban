<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            
            // Relasi Utama
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Pembuat (Admin/Katim)
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // Petugas yang ditunjuk
            $table->foreignId('activity_type_id')->nullable()->constrained('activity_types')->onDelete('set null');
            
            // Informasi Plotting (Diisi oleh Katim/Admin)
            $table->string('title');
            $table->text('description')->nullable(); 
            $table->string('location', 100)->nullable();
            $table->string('nomor_surat_tugas', 100)->nullable(); 
            $table->date('event_date'); 
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable(); // Berguna jika ini adalah Rapat
            
            // Kolom Laporan Pengawasan (Hanya diisi jika Activity Type = TUGAS LAPANGAN)
            $table->date('tanggal_pelaksanaan')->nullable(); 
            $table->string('responden')->nullable(); 
            $table->text('aktivitas')->nullable();
            $table->text('permasalahan')->nullable();
            $table->text('solusi_antisipasi')->nullable();
            
            // Kolom Khusus Rapat (Hanya diisi jika Activity Type = RAPAT)
            $table->foreignId('notulis_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('materi_path')->nullable(); // Path PDF/PPT Materi Rapat
            $table->string('dokumentasi_path')->nullable(); // Foto suasana rapat
            $table->longText('notulensi_hasil')->nullable(); // Hasil keputusan rapat (diisi notulis)
            
            // File & Status
            $table->string('surat_tugas_path')->nullable(); // File ST Utama
            $table->enum('status_laporan', ['Pending', 'Selesai'])->default('Pending');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};