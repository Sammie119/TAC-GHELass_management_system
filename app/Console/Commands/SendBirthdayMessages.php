<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendBirthdayMessages extends Command
{
    protected $signature   = 'notifications:birthdays';
    protected $description = 'Send birthday messages to members whose birthday is today';

    public function handle(NotificationService $service): void
    {
        $today = now();

        $members = Member::where('status', 'active')
            ->whereNotNull('date_of_birth')
            ->whereMonth('date_of_birth', $today->month)
            ->whereDay('date_of_birth',   $today->day)
            ->get();

        if ($members->isEmpty()) {
            $this->info('No birthdays today.');
            return;
        }

        $this->info("Sending birthday messages to {$members->count()} member(s)...");

        foreach ($members as $member) {
            $service->sendBirthdayMessage($member);
            $this->line("  ✓ {$member->full_name}");
        }

        $this->info('Done!');
    }
}
