<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentReport extends Model
{
    use HasFactory;

    // Supaya bisa simpan data massal
    protected $guarded = ['id'];

    /**
     * Relasi balik ke Agenda (Penugasan)
     */
    public function agenda()
    {
        return $this->belongsTo(Agenda::class, 'agenda_id');
    }

    /**
     * Relasi ke User (Pegawai yang lapor)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}