<?php

namespace App\Models;

// Perbaikan pada baris di bawah ini:
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'event_date' => 'date',
        'end_date'   => 'date',
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
        return $this->hasOneThrough(
            Team::class, 
            User::class, 
            'id',          
            'id',          
            'assigned_to', 
            'team_id'      
        );
    }

    public function presensi()
    {
        // Relasi ke tabel meeting_presences (Daftar Hadir)
        // Kita gunakan hasOne karena satu baris agenda (per orang) punya satu tanda tangan
        return $this->hasOne(MeetingPresence::class, 'agenda_id', 'id')
                    ->where('user_id', $this->assigned_to);
    }

}