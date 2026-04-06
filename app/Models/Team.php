<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['nama_tim'];

    /**
     * INI YANG PALING PENTING:
     * Menghitung tugas berdasarkan label 'team_id' di tabel AGENDAS.
     * Tidak peduli siapa yang ngerjain, angkanya masuk ke tim yang punya hajat.
     */
    public function agendas()
    {
        return $this->hasMany(Agenda::class, 'team_id');
    }

    /**
     * Relasi ke User (Daftar anggota tetap di tim ini)
     */
    public function members()
    {
        return $this->hasMany(User::class, 'team_id');
    }
}