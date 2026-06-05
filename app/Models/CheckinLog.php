<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CheckinLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id', 'usher_id', 'device_info', 'ip_address',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function usher()
    {
        return $this->belongsTo(User::class, 'usher_id');
    }
}
