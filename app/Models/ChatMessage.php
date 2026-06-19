<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $guarded = [];

    // Relasi: Pesan ini milik siapa?
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
