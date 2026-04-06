<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    // Karena guarded cuma 'id', kolom baru (report_target, mode_surat, dll) otomatis aman
    protected $guarded = ['id'];

    protected $casts = [
        'event_date' => 'date',
        'end_date'   => 'date',
        'approved_at' => 'datetime', // Tambahkan cast untuk timestamp approval
    ];

    /**
     * Relasi ke User (Pembuat tugas - Admin/Katim)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke User (Petugas yang mengerjakan - Pegawai)
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relasi ke Pejabat Penandatangan (Approver)
     * Tambahkan ini untuk fitur Generator Surat
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Relasi ke Banyak Laporan (Multi-Translok)
     * Tambahkan ini karena 1 ST bisa banyak laporan
     */
    public function reports()
    {
        return $this->hasMany(AssignmentReport::class, 'agenda_id');
    }

    /**
     * Relasi ke Notulis Rapat (User)
     */
    public function notulis()
    {
        return $this->belongsTo(User::class, 'notulis_id');
    }

    /**
     * Relasi ke Daftar Hadir Rapat (One-to-Many)
     */
    public function presences()
    {
        return $this->hasMany(MeetingPresence::class, 'agenda_id');
    }

    /**
     * Relasi ke Jenis Kegiatan
     */
    public function activityType()
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id');
    }

    /**
     * Relasi ke Foto Dokumentasi
     */
    public function photos()
    {
        return $this->hasMany(AgendaPhoto::class, 'agenda_id');
    }

    public function termIndices()
    {
        return $this->hasMany(TermIndex::class, 'agenda_id');
    }

    public function team()
    {
        // Ganti hasOneThrough menjadi belongsTo
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function presensi()
    {
        return $this->hasOne(MeetingPresence::class, 'agenda_id', 'id')
                    ->where('user_id', $this->assigned_to);
    }
}