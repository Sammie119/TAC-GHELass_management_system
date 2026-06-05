<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Member;

class AttendanceService
{
    public function checkIn(Member $member, Event $event, string $method, ?int $usherId = null): array
    {
        if ($event->status !== 'active') {
            return ['success' => false, 'message' => 'Event is not active.'];
        }

        if (Attendance::where('member_id', $member->id)->where('event_id', $event->id)->exists()) {
            return ['success' => false, 'message' => "{$member->full_name} is already checked in.", 'duplicate' => true];
        }

        Attendance::create([
            'member_id'      => $member->id,
            'event_id'       => $event->id,
            'checkin_method' => $method,
            'checked_in_by'  => $usherId,
            'checked_in_at'  => now(),
        ]);

        return ['success' => true, 'message' => "{$member->full_name} checked in successfully!"];
    }
}
