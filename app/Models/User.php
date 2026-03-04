<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama_lengkap',
        'nip',
        'username',
        'password',
        'role',
        'team_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relasi ke Tim
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * Relasi ke Absensi / Data Cuti
     * Ini penting untuk mengecek apakah user sedang cuti atau tidak
     */
    public function absences()
    {
        return $this->hasMany(Absensi::class, 'user_id');
    }

    /**
     * Agenda yang Diterima (oleh Pegawai)
     * Alias 'agendas' sering dipakai untuk timeline
     */
    public function agendas()
    {
        return $this->hasMany(Agenda::class, 'assigned_to');
    }

    /**
     * Agenda yang Dibuat (oleh Admin/Katim)
     */
    public function createdAgendas()
    {
        return $this->hasMany(Agenda::class, 'user_id');
    }
}