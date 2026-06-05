<?php

namespace App\Console\Commands;

use App\Models\AbsenteeFlag;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Member;
use Illuminate\Console\Command;

class FlagAbsentees extends Command
{
    protected $signature   = 'absentees:flag {--threshold=3 : Number of consecutive absences before flagging}';
    protected $description = 'Scan recent events and flag members with consecutive absences';

    public function handle(): void
    {
        $threshold = (int) $this->option('threshold');

        // Get last N closed/active events ordered by date
        $recentEvents = Event::whereIn('status', ['closed', 'active'])
            ->orderBy('event_date', 'desc')
            ->take($threshold)
            ->get();

        if ($recentEvents->count() < $threshold) {
            $this->info("Not enough events yet (need at least {$threshold}).");
            return;
        }

        $members = Member::where('status', 'active')->get();
        $flagged = 0;
        $cleared = 0;

        foreach ($members as $member) {
            $consecutiveAbsences = 0;

            foreach ($recentEvents as $event) {
                $attended = Attendance::where('member_id', $member->id)
                    ->where('event_id', $event->id)
                    ->exists();

                if (!$attended) {
                    $consecutiveAbsences++;
                } else {
                    break; // Stop at first attendance found
                }
            }

            $lastAttendance = $member->attendance()
                ->latest('checked_in_at')->first();

            if ($consecutiveAbsences >= $threshold) {
                // Create or update flag
                AbsenteeFlag::updateOrCreate(
                    ['member_id' => $member->id],
                    [
                        'consecutive_absences' => $consecutiveAbsences,
                        'last_attended'        => $lastAttendance?->checked_in_at->toDateString(),
                        'flagged_on'           => now()->toDateString(),
                        'status'               => 'flagged',
                    ]
                );
                $flagged++;
            } else {
                // Clear flag if member has been attending
                AbsenteeFlag::where('member_id', $member->id)
                    ->where('status', 'flagged')
                    ->delete();
                $cleared++;
            }
        }

        $this->info("Done. Flagged: {$flagged} members. Cleared: {$cleared} flags.");
    }
}
