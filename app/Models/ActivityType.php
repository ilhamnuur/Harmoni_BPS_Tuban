<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara massal.
     * Sesuai dengan isian di Seeder tadi.
     */
    protected $fillable = ['name', 'description'];

    /**
     * Relasi ke Agenda.
     * Satu tipe aktivitas (misal: 'Survei') bisa memiliki banyak agenda.
     */
    public function agendas()
    {
        return $this->hasMany(Agenda::class, 'activity_type_id');
    }
}