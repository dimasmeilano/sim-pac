<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'progja_id',
        'user_id',
        'message',
        'tagged_users',
        'attachment',
        'file_path',  // <-- Pastikan 3 baris ini ada!
        'file_name',
        'file_type',
        'reply_to_id' // <-- Pastikan ini juga ada!
    ];

    public function programKerja()
    {
        return $this->belongsTo(ProgramKerja::class, 'progja_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke user yang ditag
    public function getTaggedUsersAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function repliedMessage()
    {
        return $this->belongsTo(Message::class, 'reply_to_id'); // Ganti Message::class dengan nama Model Anda jika berbeda
    }
}
