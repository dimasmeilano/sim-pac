<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TtdDigital extends Model
{
    use HasFactory;

    protected $table = 'ttd_digital';

    protected $fillable = [
        'user_id',
        'file_ttd',
        'file_stempel',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
