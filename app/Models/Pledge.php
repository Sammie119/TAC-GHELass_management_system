<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pledge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id', 'category', 'description',
        'pledged_amount', 'paid_amount', 'currency',
        'pledge_date', 'due_date', 'status',
        'notes', 'recorded_by',
    ];

    protected $casts = [
        'pledge_date'    => 'date',
        'due_date'       => 'date',
        'pledged_amount' => 'decimal:2',
        'paid_amount'    => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function payments()
    {
        return $this->hasMany(PledgePayment::class)->latest();
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getRemainingAttribute(): float
    {
        return max(0, $this->pledged_amount - $this->paid_amount);
    }

    public function getProgressAttribute(): int
    {
        if ($this->pledged_amount <= 0) return 0;
        return min(100, round(($this->paid_amount / $this->pledged_amount) * 100));
    }
}
