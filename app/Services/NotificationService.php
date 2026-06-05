<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Event;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(
        protected MnotifyService $sms
    ) {}

    // ── Welcome message ────────────────────────────────────
    public function sendWelcome(Member $member): void
    {
        $smsMsg = "Welcome to {$this->churchName()}, {$member->first_name}! "
            . "Your member ID is {$member->member_id_card}. "
            . "Visit " . url('/portal') . " to access your member portal.";

        $emailSubject = "Welcome to " . $this->churchName() . ", {$member->first_name}!";
        $emailBody    = $this->welcomeEmailBody($member);

        $this->dispatch($member, 'welcome', $smsMsg, $emailSubject, $emailBody);
    }

    // ── Check-in confirmation ──────────────────────────────
    public function sendCheckinConfirmation(Member $member, Event $event): void
    {
        $smsMsg = "Hi {$member->first_name}, your check-in for "
            . "{$event->title} on {$event->event_date->format('d M Y')} "
            . "has been recorded. God bless you! - " . $this->churchName();

        $emailSubject = "Check-in confirmed — {$event->title}";
        $emailBody    = $this->checkinEmailBody($member, $event);

        $this->dispatch($member, 'checkin_confirmation', $smsMsg, $emailSubject, $emailBody);
    }

    // ── Event reminder ─────────────────────────────────────
    public function sendEventReminder(Member $member, Event $event): void
    {
        $smsMsg = "Reminder: {$event->title} is tomorrow, "
            . "{$event->event_date->format('d M Y')} at "
            . \Carbon\Carbon::parse($event->start_time)->format('h:i A')
            . ". We look forward to seeing you! - " . $this->churchName();

        $emailSubject = "Reminder: {$event->title} is tomorrow";
        $emailBody    = $this->reminderEmailBody($member, $event);

        $this->dispatch($member, 'event_reminder', $smsMsg, $emailSubject, $emailBody);
    }

    // ── Absentee follow-up ─────────────────────────────────
    public function sendAbsenteeFollowup(Member $member, int $absences): void
    {
        $smsMsg = "Hi {$member->first_name}, we noticed you've missed "
            . "our last {$absences} services. We miss you and hope all is well. "
            . "Please reach out if you need anything. - " . $this->churchName();

        $emailSubject = "We miss you, {$member->first_name}!";
        $emailBody    = $this->absenteeEmailBody($member, $absences);

        $this->dispatch($member, 'absentee_followup', $smsMsg, $emailSubject, $emailBody);
    }

    // ── Birthday message ───────────────────────────────────
    public function sendBirthdayMessage(Member $member): void
    {
        $smsMsg = "Happy Birthday, {$member->first_name}! "
            . "Wishing you a wonderful day filled with God's blessings. "
            . "With love, " . $this->churchName();

        $emailSubject = "Happy Birthday, {$member->first_name}! 🎂";
        $emailBody    = $this->birthdayEmailBody($member);

        $this->dispatch($member, 'birthday', $smsMsg, $emailSubject, $emailBody);
    }

    // ── Core dispatcher ────────────────────────────────────
    private function dispatch(
        Member $member,
        string $type,
        string $smsMsg,
        string $emailSubject,
        string $emailBody
    ): void {
        $smsSent   = false;
        $emailSent = false;

        // Send SMS
        if ($member->phone) {
//            $smsSent = $this->sms->send($member->phone, $smsMsg);
            $smsSent = SMSOnlineGhService::sendSMS($member->phone, $smsMsg);
        }

        // Send Email
//        if ($member->email) {
//            try {
//                Mail::html($emailBody, function ($mail) use ($member, $emailSubject) {
//                    $mail->to($member->email, $member->full_name)
//                        ->subject($emailSubject);
//                });
//                $emailSent = true;
//            } catch (\Exception $e) {
//                Log::error("Email failed for {$member->email}: " . $e->getMessage());
//            }
//        }

        // Log notification
        NotificationLog::create([
            'member_id'  => $member->id,
            'type'       => $type,
            'channel'    => $member->phone && $member->email ? 'both'
                : ($member->phone ? 'sms' : 'email'),
            'sms_sent'   => $smsSent,
            'email_sent' => $emailSent,
            'message'    => $smsMsg,
        ]);
    }

    private function churchName(): string
    {
        return "The Apostolic Church-Ghana, East Legon Assembly";
    }

    // ── Email templates ────────────────────────────────────
    private function emailWrapper(string $content, string $color = '#2563eb'): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        </head>
        <body style='margin:0;padding:0;background:#f0f9ff;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,sans-serif;'>
            <div style='max-width:560px;margin:32px auto;background:white;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);'>
                <div style='background:{$color};padding:24px;text-align:center;'>
                    <h1 style='color:white;margin:0;font-size:20px;font-weight:700;'>" . $this->churchName() . "</h1>
                </div>
                <div style='padding:32px 28px;'>
                    {$content}
                </div>
                <div style='background:#f9fafb;padding:16px;text-align:center;border-top:1px solid #e5e7eb;'>
                    <p style='font-size:12px;color:#9ca3af;margin:0;'>
                        This message was sent by " . $this->churchName() . " ·
                        <a href='" . url('/portal') . "' style='color:#2563eb;'>Visit Member Portal</a>
                    </p>
                </div>
            </div>
        </body>
        </html>";
    }

    private function welcomeEmailBody(Member $member): string
    {
        $content = "
            <h2 style='color:#111827;font-size:22px;margin-bottom:8px;'>Welcome, {$member->first_name}! 👋</h2>
            <p style='color:#6b7280;font-size:15px;line-height:1.7;margin-bottom:20px;'>
                We are delighted to have you as a member of our church family.
                Your membership has been registered and you can now use all member services.
            </p>
            <div style='background:#eff6ff;border-radius:12px;padding:16px;margin-bottom:24px;'>
                <p style='margin:0 0 6px;font-size:13px;color:#6b7280;'>Your Member ID</p>
                <p style='margin:0;font-size:24px;font-weight:800;color:#2563eb;font-family:monospace;letter-spacing:2px;'>
                    {$member->member_id_card}
                </p>
            </div>
            <p style='color:#374151;font-size:14px;line-height:1.7;margin-bottom:20px;'>
                You can use your Member ID, phone number, or email to:
            </p>
            <ul style='color:#374151;font-size:14px;line-height:2;padding-left:20px;margin-bottom:24px;'>
                <li>Check in at church services</li>
                <li>Access your attendance history</li>
                <li>Download your QR code</li>
                <li>Update your contact details</li>
            </ul>
            <a href='" . url('/portal') . "' style='display:inline-block;background:#2563eb;color:white;padding:12px 28px;border-radius:10px;font-size:15px;font-weight:600;text-decoration:none;'>
                Visit Member Portal →
            </a>";

        return $this->emailWrapper($content);
    }

    private function checkinEmailBody(Member $member, Event $event): string
    {
        $content = "
            <h2 style='color:#111827;font-size:20px;margin-bottom:8px;'>Check-in confirmed ✓</h2>
            <p style='color:#6b7280;font-size:15px;line-height:1.7;margin-bottom:20px;'>
                Hi {$member->first_name}, your attendance has been recorded for:
            </p>
            <div style='background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px;margin-bottom:24px;'>
                <p style='margin:0 0 4px;font-size:16px;font-weight:700;color:#15803d;'>{$event->title}</p>
                <p style='margin:0;font-size:14px;color:#6b7280;'>{$event->event_date->format('l, d F Y')}</p>
                <p style='margin:4px 0 0;font-size:13px;color:#9ca3af;'>
                    Checked in at " . now()->format('h:i A') . "
                </p>
            </div>
            <p style='color:#374151;font-size:14px;line-height:1.7;'>
                Thank you for joining us today. God bless you! 🙏
            </p>";

        return $this->emailWrapper($content, '#16a34a');
    }

    private function reminderEmailBody(Member $member, Event $event): string
    {
        $content = "
            <h2 style='color:#111827;font-size:20px;margin-bottom:8px;'>Service reminder 📅</h2>
            <p style='color:#6b7280;font-size:15px;line-height:1.7;margin-bottom:20px;'>
                Hi {$member->first_name}, just a friendly reminder about tomorrow's service:
            </p>
            <div style='background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:16px;margin-bottom:24px;'>
                <p style='margin:0 0 4px;font-size:16px;font-weight:700;color:#d97706;'>{$event->title}</p>
                <p style='margin:0;font-size:14px;color:#6b7280;'>{$event->event_date->format('l, d F Y')}</p>
                <p style='margin:4px 0 0;font-size:14px;color:#374151;font-weight:500;'>
                    ⏰ " . \Carbon\Carbon::parse($event->start_time)->format('h:i A') . "
                </p>
            </div>
            <p style='color:#374151;font-size:14px;line-height:1.7;'>
                We look forward to seeing you! Remember to bring your member ID card or use your QR code to check in.
            </p>";

        return $this->emailWrapper($content, '#d97706');
    }

    private function absenteeEmailBody(Member $member, int $absences): string
    {
        $content = "
            <h2 style='color:#111827;font-size:20px;margin-bottom:8px;'>We miss you, {$member->first_name}! 💙</h2>
            <p style='color:#6b7280;font-size:15px;line-height:1.7;margin-bottom:20px;'>
                We noticed you have missed our last {$absences} services and wanted to check in on you.
            </p>
            <div style='background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:16px;margin-bottom:24px;'>
                <p style='margin:0;font-size:14px;color:#dc2626;'>
                    We care about every member of our church family and hope everything is well with you.
                </p>
            </div>
            <p style='color:#374151;font-size:14px;line-height:1.7;margin-bottom:20px;'>
                If you need any support or have any concerns, please do not hesitate to reach out to us.
                We are always here for you. 🙏
            </p>
            <p style='color:#374151;font-size:14px;line-height:1.7;'>
                We hope to see you at our next service. God bless you!
            </p>";

        return $this->emailWrapper($content, '#dc2626');
    }

    private function birthdayEmailBody(Member $member): string
    {
        $content = "
            <div style='text-align:center;margin-bottom:24px;'>
                <div style='font-size:64px;'>🎂</div>
            </div>
            <h2 style='color:#111827;font-size:24px;margin-bottom:8px;text-align:center;'>
                Happy Birthday, {$member->first_name}!
            </h2>
            <p style='color:#6b7280;font-size:15px;line-height:1.7;margin-bottom:20px;text-align:center;'>
                On behalf of the entire church family, we wish you a wonderful birthday filled with joy, love, and God's abundant blessings.
            </p>
            <div style='background:#fef3c7;border-radius:12px;padding:20px;text-align:center;margin-bottom:24px;'>
                <p style='margin:0;font-size:16px;color:#d97706;font-style:italic;'>
                    \"For I know the plans I have for you, plans to prosper you and not to harm you,
                    plans to give you hope and a future.\" — Jeremiah 29:11
                </p>
            </div>
            <p style='color:#374151;font-size:14px;line-height:1.7;text-align:center;'>
                With much love from all of us at " . $this->churchName() . " 🙏
            </p>";

        return $this->emailWrapper($content, '#d97706');
    }
}
