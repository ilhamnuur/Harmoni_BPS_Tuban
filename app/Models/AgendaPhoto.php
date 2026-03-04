<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaPhoto extends Model
{
    use HasFactory;

    /**
     * Mengizinkan semua kolom diisi kecuali ID.
     * Cocok karena kolom kita cuma agenda_id, photo_path, dan description.
     */
    protected $guarded = ['id'];

    /**
     * Relasi balik ke Agenda.
     * Setiap foto pasti milik satu agenda tertentu.
     */
    public function agenda()
    {
        return $this->belongsTo(Agenda::class, 'agenda_id');
    }
}