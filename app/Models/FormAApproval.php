<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormAApproval extends Model
{
    protected $fillable = [
        'year', 'month',
        'pastor_approved_at', 'pastor_approved_by',
        'finance_approved_at', 'finance_approved_by',
    ];

    protected $casts = [
        'pastor_approved_at' => 'datetime',
        'finance_approved_at' => 'datetime',
    ];

    public function pastorApprovedBy()
    {
        return $this->belongsTo(User::class, 'pastor_approved_by');
    }

    public function financeApprovedBy()
    {
        return $this->belongsTo(User::class, 'finance_approved_by');
    }

    public function isFullyApproved(): bool
    {
        return (bool) ($this->pastor_approved_at && $this->finance_approved_at);
    }
}
