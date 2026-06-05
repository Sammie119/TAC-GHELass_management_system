<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'member_id', 'type', 'channel',
        'sms_sent', 'email_sent', 'message',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
