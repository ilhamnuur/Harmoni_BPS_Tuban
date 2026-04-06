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
        Schema::table('agendas', function (Blueprint $table) {
            // 1. Fitur Multi-Laporan (Target berapa kali lapor/translok)
            $table->integer('report_target')->default(1)->after('status_laporan');

            // 2. Fitur Generator Surat
            $table->text('content_surat')->nullable()->after('description'); 
            $table->enum('mode_surat', ['generate', 'upload'])->default('upload')->after('content_surat');
            
            // 3. Alur Approval Dasar
            $table->enum('status_approval', ['Draft', 'Pending', 'Approved', 'Rejected'])->default('Approved')->after('mode_surat');
            
            // 4. Fitur Single/Multiple Approval (TAMBAHAN DISINI)
            $table->enum('approval_type', ['single', 'multiple'])->default('single')->after('status_approval');
            
            // Kolom untuk Pemeriksa (Reviewer/Katim)
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->after('approval_type');
            $table->timestamp('reviewed_at')->nullable()->after('reviewer_id');

            // Kolom untuk Penandatangan (Approver/Kepala)
            $table->foreignId('approver_id')->nullable()->constrained('users')->after('reviewed_at');
            $table->timestamp('approved_at')->nullable()->after('approver_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropColumn([
                'report_target', 
                'content_surat', 
                'mode_surat', 
                'status_approval', 
                'approval_type',
                'reviewer_id',
                'reviewed_at',
                'approver_id', 
                'approved_at'
            ]);
        });
    }
};