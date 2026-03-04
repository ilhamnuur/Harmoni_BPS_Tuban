<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    /**
     * Karena di Migration tadi kita pakai $table->timestamps(),
     * maka property $timestamps tidak perlu diset false. 
     * Laravel akan otomatis mengisinya.
     */
    protected $fillable = ['nama_tim'];

    /**
     * Relasi ke User (Satu tim punya banyak pegawai)
     */
    public function users()
    {
        return $this->hasMany(User::class, 'team_id');
    }
}