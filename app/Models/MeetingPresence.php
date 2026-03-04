<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingPresence extends Model
{
    protected $fillable = [
        'agenda_id',
        'user_id',
        'signature_base64',
        'signed_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }
}