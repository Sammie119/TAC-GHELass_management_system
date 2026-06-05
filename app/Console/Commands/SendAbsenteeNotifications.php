<?php

namespace App\Console\Commands;

use App\Models\AbsenteeFlag;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendAbsenteeNotifications extends Command
{
    protected $signature   = 'notifications:absentees';
    protected $description = 'Send follow-up notifications to flagged absentee members';

    public function handle(NotificationService $service): void
    {
        $flags = AbsenteeFlag::with('member')
            ->where('status', 'flagged')
            ->get();

        if ($flags->isEmpty()) {
            $this->info('No flagged absentees to notify.');
            return;
        }

        $this->info("Notifying {$flags->count()} absentee(s)...");

        foreach ($flags as $flag) {
            $member = $flag->member;

            if (!$member->phone && !$member->email) {
                $this->line("  ⚠ {$member->full_name} — no contact info");
                continue;
            }

            $service->sendAbsenteeFollowup($member, $flag->consecutive_absences);
            $flag->update(['status' => 'contacted']);
            $this->line("  ✓ {$member->full_name}");
        }

        $this->info('Done!');
    }
}
