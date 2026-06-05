<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Member;
use App\Models\NotificationLog;
use App\Services\NotificationService;
use App\Services\SMSOnlineGhService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $logs = NotificationLog::with('member')
            ->latest()->paginate(20);

        $stats = [
            'total'    => NotificationLog::count(),
            'sms'      => NotificationLog::where('sms_sent', true)->count(),
            'email'    => NotificationLog::where('email_sent', true)->count(),
            'today'    => NotificationLog::whereDate('created_at', today())->count(),
        ];

        $events  = Event::orderBy('event_date', 'desc')->take(30)->get();
        $members = Member::where('status', 'active')->orderBy('first_name')->get();

        return view('admin.notifications.index', compact('logs', 'stats', 'events', 'members'));
    }

    public function sendManual(Request $request, NotificationService $service)
    {
        $request->validate([
            'type'       => 'required|in:welcome,event_reminder,absentee_followup,birthday,custom',
            'member_ids' => 'required|array|min:1',
            'event_id'   => 'required_if:type,event_reminder|nullable|exists:events,id',
            'message'    => 'required_if:type,custom|nullable|string|max:160',
        ]);

        $members = Member::whereIn('id', $request->member_ids)->get();
        $event   = $request->event_id ? Event::find($request->event_id) : null;
        $sent    = 0;

        foreach ($members as $member) {
            match($request->type) {
                'welcome'           => $service->sendWelcome($member),
                'event_reminder'    => $event ? $service->sendEventReminder($member, $event) : null,
                'absentee_followup' => $service->sendAbsenteeFollowup($member, 3),
                'birthday'          => $service->sendBirthdayMessage($member),
                'custom'            => $this->sendCustom($member, $request->message, $service),
            };
            $sent++;
        }

        return back()->with('success', "Notification sent to {$sent} member(s) successfully.");
    }

    private function sendCustom(Member $member, string $message, NotificationService $service): void
    {
        if(!is_null($member->phone)){
            //            app(\App\Services\MnotifyService::class)->send($member->phone, $message);
            SMSOnlineGhService::sendSMS($member->phone, $message);
        }

//        if ($member->email) {
//            \Mail::html(
//                "<p style='font-family:sans-serif;font-size:15px;color:#374151;line-height:1.7;'>{$message}</p>",
//                fn($m) => $m->to($member->email, $member->full_name)
//                    ->subject('Message from The Apostolic Church-Ghana, East Legon Assembly')
//            );
//        }

        \App\Models\NotificationLog::create([
            'member_id'  => $member->id,
            'type'       => 'custom',
            'channel'    => 'both',
            'sms_sent'   => (bool) $member->phone,
            'email_sent' => (bool) $member->email,
            'message'    => $message,
        ]);
    }

    public function runCommand(Request $request)
    {
        $command = $request->command;

        $allowed = [
            'notifications:birthdays',
            'notifications:event-reminders',
            'notifications:absentees',
        ];

        if (!in_array($command, $allowed)) {
            return back()->with('error', 'Invalid command.');
        }

        \Artisan::call($command);
        $output = trim(\Artisan::output());

        return back()->with('success', "Command ran: {$output}");
    }
}
