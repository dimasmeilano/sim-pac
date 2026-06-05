<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTugas extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'sub_tugas';

    protected $fillable = [
        'progja_id',
        'nama',
        'assignee_id',
        'status',
        'deadline',
        'urutan'
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function programKerja()
    {
        return $this->belongsTo(ProgramKerja::class, 'progja_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'todo' => 'To Do',
            'progress' => 'Progress',
            'done' => 'Done',
            'revisi' => 'Revisi',
        ];
        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'todo' => 'secondary',
            'progress' => 'primary',
            'done' => 'success',
            'revisi' => 'warning',
        ];
        return $colors[$this->status] ?? 'secondary';
    }
}
