<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberSession extends Model
{
    protected $fillable = [
        'member_id', 'token', 'otp',
        'otp_expires_at', 'last_active_at', 'ip_address',
    ];

    protected $casts = [
        'otp_expires_at'  => 'datetime',
        'last_active_at'  => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
