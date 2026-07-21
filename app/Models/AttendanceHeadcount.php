<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceHeadcount extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'male', 'female', 'children', 'youth', 'visitors', 'recorded_by',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getTotalAttribute(): int
    {
        return $this->male + $this->female + $this->children + $this->youth + $this->visitors;
    }
}
