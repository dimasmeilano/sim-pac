<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Galeri extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Ke Atas: File ini ada di Folder mana?
    public function folder()
    {
        return $this->belongsTo(WorkspaceFolder::class, 'workspace_folder_id');
    }

    // Ke Atas: Siapa yang upload? (Bisa null jika upload dari link public)
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
