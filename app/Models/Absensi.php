<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    /**
     * Nama tabel sesuai dengan file Migration terbaru.
     */
    protected $table = 'absences';

    /**
     * Mengizinkan pengisian data secara massal.
     * Menggunakan fillable sudah benar agar aman.
     */
    protected $fillable = [
        'user_id', 
        'start_date', 
        'end_date', 
        'status', 
        'keterangan', 
        'input_by'
    ];

    /**
     * Otomatis mengubah string tanggal dari database menjadi object Carbon.
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * Relasi ke Pegawai yang sedang cuti/absen.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke User (Subbag Umum/Admin) yang menginputkan data ini.
     */
    public function creator()
    {
        // Saya ganti nama fungsinya jadi 'creator' agar senada dengan model Agenda
        return $this->belongsTo(User::class, 'input_by');
    }
}