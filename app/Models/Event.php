<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'type', 'event_date', 'start_time',
        'end_time', 'description', 'created_by', 'status', 'qr_token',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function ($event) {
            $event->qr_token = Str::uuid();
        });
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function visitors()
    {
        return $this->hasMany(Visitor::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTotalAttendanceAttribute(): int
    {
        return $this->attendance()->count();
    }
}
