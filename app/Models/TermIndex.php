<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermIndex extends Model
{
    // Tabel ini biasanya tidak butuh timestamps
    public $timestamps = false;
    protected $table = 'term_index';

    protected $fillable = ['agenda_id', 'term', 'tfidf_weight'];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class, 'agenda_id');
    }
}