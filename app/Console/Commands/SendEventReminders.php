<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Member;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    protected $signature   = 'notifications:event-reminders';
    protected $description = 'Send event reminders to all active members for tomorrow\'s events';

    public function handle(NotificationService $service): void
    {
        $tomorrow = now()->addDay()->toDateString();

        $events = Event::where('event_date', $tomorrow)
            ->whereIn('status', ['upcoming', 'active'])
            ->get();

        if ($events->isEmpty()) {
            $this->info('No events tomorrow.');
            return;
        }

        $members = Member::where('status', 'active')
            ->where(function ($q) {
                $q->whereNotNull('phone')
                    ->orWhereNotNull('email');
            })->get();

        $this->info("Sending reminders to {$members->count()} members for {$events->count()} event(s)...");

        foreach ($events as $event) {
            $this->line("  Event: {$event->title}");
            foreach ($members as $member) {
                $service->sendEventReminder($member, $event);
            }
        }

        $this->info('Done!');
    }
}
