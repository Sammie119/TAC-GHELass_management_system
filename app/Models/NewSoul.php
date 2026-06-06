<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewSoul extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email',
        'address', 'area', 'date_won', 'won_by',
        'assigned_to', 'status', 'church_background',
        'salvation_prayer_date', 'notes',
    ];

    protected $casts = [
        'date_won'              => 'date',
        'salvation_prayer_date' => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function wonBy()
    {
        return $this->belongsTo(Member::class, 'won_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function followups()
    {
        return $this->hasMany(SoulFollowup::class, 'soul_id')->latest();
    }
}
