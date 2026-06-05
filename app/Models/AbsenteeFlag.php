<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsenteeFlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id', 'consecutive_absences', 'last_attended',
        'flagged_on', 'status', 'notes', 'assigned_to', 'resolved_at',
    ];

    protected $casts = [
        'last_attended' => 'date',
        'flagged_on'    => 'date',
        'resolved_at'   => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
