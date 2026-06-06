<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CellGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'area', 'description',
        'leader_id', 'assistant_leader_id',
        'meeting_day', 'meeting_time',
        'meeting_venue', 'status',
    ];

    public function leader()
    {
        return $this->belongsTo(Member::class, 'leader_id');
    }

    public function assistantLeader()
    {
        return $this->belongsTo(Member::class, 'assistant_leader_id');
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'cell_group_members')
            ->withPivot('joined_date', 'is_leader')
            ->withTimestamps();
    }
}
