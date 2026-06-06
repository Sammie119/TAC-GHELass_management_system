<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SoulFollowup extends Model
{
    use HasFactory;

    protected $fillable = [
        'soul_id', 'user_id', 'method', 'outcome',
        'notes', 'followup_date', 'next_followup_date',
    ];

    protected $casts = [
        'followup_date'      => 'date',
        'next_followup_date' => 'date',
    ];

    public function soul()
    {
        return $this->belongsTo(NewSoul::class, 'soul_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
