<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $guarded = [];

    use HasFactory;

    // TAMBAHKAN BARIS INI AGAR BISA DISIMPAN:
    protected $fillable = ['name', 'type'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_room_users');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
