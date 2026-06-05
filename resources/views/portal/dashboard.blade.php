<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Portal — {{ $member->full_name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f9ff; min-height: 100vh; color: #111827;
        }

        /* Nav */
        .nav {
            background: white; border-bottom: 1px solid #e5e7eb;
            padding: 0 1rem; height: 56px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 10;
        }
        .nav-brand { display: flex; align-items: center; gap: 10px; }
        .nav-logo {
            width: 32px; height: 32px; background: #2563eb; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .nav-title { font-size: 15px; font-weight: 600; color: #111827; }
        .nav-links { display: flex; gap: 4px; }
        .nav-link {
            padding: 6px 12px; border-radius: 8px; font-size: 13px;
            text-decoration: none; color: #6b7280; font-weight: 500;
            transition: background 0.15s;
        }
        .nav-link:hover { background: #f3f4f6; color: #111827; }
        .nav-link.active { background: #eff6ff; color: #2563eb; }
        .nav-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: #2563eb;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 12px; font-weight: 700; cursor: pointer;
        }

        /* Content */
        .content { max-width: 640px; margin: 0 auto; padding: 1.5rem 1rem; }

        /* Welcome banner */
        .welcome {
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            border-radius: 16px; padding: 1.5rem;
            color: white; margin-bottom: 1.25rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .welcome-name { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .welcome-id   { font-size: 13px; opacity: 0.8; font-family: monospace; }
        .welcome-avatar {
            width: 52px; height: 52px; border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 700; flex-shrink: 0;
            border: 2px solid rgba(255,255,255,0.3);
        }

        /* Stats grid */
        .stats {
            display: grid; grid-template-columns: repeat(3,1fr);
            gap: 12px; margin-bottom: 1.25rem;
        }
        .stat-card {
            background: white; border-radius: 12px;
            border: 1px solid #e5e7eb; padding: 14px; text-align: center;
        }
        .stat-value { font-size: 26px; font-weight: 800; color: #111827; }
        .stat-label { font-size: 11px; color: #9ca3af; margin-top: 3px; }

        /* Cards */
        .section-card {
            background: white; border-radius: 14px;
            border: 1px solid #e5e7eb; margin-bottom: 1.25rem;
            overflow: hidden;
        }
        .section-header {
            padding: 14px 16px; border-bottom: 1px solid #f3f4f6;
            display: flex; justify-content: space-between; align-items: center;
        }
        .section-title { font-size: 14px; font-weight: 600; color: #111827; }
        .section-link  { font-size: 13px; color: #2563eb; text-decoration: none; }

        /* Attendance row */
        .att-row {
            padding: 12px 16px; display: flex; align-items: center;
            justify-content: space-between; border-bottom: 1px solid #f9fafb;
        }
        .att-row:last-child { border-bottom: none; }
        .att-type {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; flex-shrink: 0;
        }
        .att-name  { font-size: 14px; font-weight: 500; color: #111827; }
        .att-date  { font-size: 12px; color: #9ca3af; }
        .att-time  { font-size: 12px; color: #9ca3af; text-align: right; }
        .att-badge {
            font-size: 11px; padding: 2px 10px; border-radius: 20px;
            background: #dcfce7; color: #15803d; font-weight: 500;
        }

        /* Event row */
        .event-row {
            padding: 12px 16px; display: flex; align-items: center;
            justify-content: space-between; border-bottom: 1px solid #f9fafb;
        }
        .event-row:last-child { border-bottom: none; }
        .event-date-box {
            width: 40px; text-align: center; flex-shrink: 0;
        }
        .event-date-day   { font-size: 18px; font-weight: 800; color: #2563eb; line-height: 1; }
        .event-date-month { font-size: 10px; color: #9ca3af; text-transform: uppercase; }

        /* QR section */
        .qr-card {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 1px solid #bbf7d0; border-radius: 14px;
            padding: 1.5rem; text-align: center; margin-bottom: 1.25rem;
        }

        .qr-title { font-size: 15px; font-weight: 600; color: #15803d; margin-bottom: 6px; }
        .qr-sub   { font-size: 13px; color: #6b7280; margin-bottom: 1rem; }

        .otp-card {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border: 1px solid #fca5a5;
            border-radius: 14px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1.25rem;
        }

        .otp-title {
            font-size: 15px;
            font-weight: 600;
            color: #b91c1c;
            margin-bottom: 6px;
        }

        .otp-sub {
            font-size: 13px;
            color: #7f1d1d;
            margin-bottom: 1rem;
        }

        .qr-btn {
            display: inline-flex; align-items: center; gap: 8px;
            background: #16a34a; color: white; padding: 10px 20px;
            border-radius: 10px; font-size: 14px; font-weight: 600;
            text-decoration: none;
        }

        .otp-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #dc2626;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .otp-btn:hover {
            background: #b91c1c;
        }

        /* Streak badge */
        .streak {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fef3c7; border: 1px solid #fde68a;
            border-radius: 20px; padding: 4px 14px; font-size: 13px;
            color: #d97706; font-weight: 600; margin-bottom: 1.25rem;
        }

        /* Logout */
        .logout-form { text-align: center; margin-top: 1rem; }
        .logout-btn {
            background: none; border: none; color: #9ca3af; font-size: 13px;
            cursor: pointer; text-decoration: underline; padding: 8px;
        }
    </style>
</head>
<body>

{{-- Navigation --}}
<nav class="nav">
    <div class="nav-brand">
        <div class="nav-logo">
            <svg style="width:18px;height:18px;" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <span class="nav-title">My Portal</span>
    </div>
    <div class="nav-links">
        <a href="{{ route('portal.dashboard') }}"  class="nav-link active">Home</a>
        <a href="{{ route('portal.attendance') }}" class="nav-link">History</a>
        <a href="{{ route('portal.payments') }}"   class="nav-link">Payments</a>
        <a href="{{ route('portal.profile') }}"    class="nav-link">Profile</a>
    </div>
    <div class="nav-avatar">
        {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
    </div>
</nav>

<div class="content">

    {{-- Welcome banner --}}
    <div class="welcome">
        <div>
            <div class="welcome-name">Welcome, {{ $member->first_name }}!</div>
            <div class="welcome-id">{{ $member->member_id_card }}</div>
        </div>
        <div class="welcome-avatar">
            {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
        </div>
    </div>

    {{-- Streak --}}
    @if($streak > 0)
        <div class="streak">
            🔥 {{ $streak }} service streak — keep it up!
        </div>
    @endif

    {{-- Stats --}}
    <div class="stats" style="grid-template-columns:repeat(2,1fr);">
        <div class="stat-card">
            <div class="stat-value">{{ $totalAttendance }}</div>
            <div class="stat-label">Total services</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $thisMonthCount }}</div>
            <div class="stat-label">This month</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="font-size:18px;color:#16a34a;">
                GH₵ {{ number_format($thisMonthPaid, 2) }}
            </div>
            <div class="stat-label">Paid this month</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="font-size:18px;color:#2563eb;">
                GH₵ {{ number_format($totalTithe, 2) }}
            </div>
            <div class="stat-label">Total tithes</div>
        </div>
    </div>

{{--    --}}{{-- QR download --}}
{{--    <div class="qr-card">--}}
{{--        <div class="qr-title">Your Check-in QR Code</div>--}}
{{--        <div class="qr-sub">Show this at the entrance or download it to your phone</div>--}}
{{--        <a href="{{ route('portal.qr-download') }}" class="qr-btn">--}}
{{--            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"--}}
{{--                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>--}}
{{--            </svg>--}}
{{--            Download QR Code--}}
{{--        </a>--}}
{{--    </div>--}}


    {{-- Change OTP --}}
    @if($member_session->otp == $member->otp)
        <div class="otp-card">
            <div class="otp-title">Change your OTP</div>
            <div class="otp-sub">Change the OTP to a 6-digit figure you can easily remember!!</div>
            <a href="{{ route('portal.profile') }}" class="otp-btn">
                Profile →
            </a>
        </div>
    @endif

    {{-- Pay tithe online --}}
    <div style="background:linear-gradient(135deg,#16a34a,#22c55e);border-radius:16px;padding:1.25rem;text-align:center;margin-bottom:1.25rem;">
        <p style="font-size:15px;font-weight:700;color:white;margin-bottom:4px;">Pay Tithe or Offering Online</p>
        <p style="font-size:13px;color:rgba(255,255,255,0.8);margin-bottom:1rem;">Submit your payment securely from anywhere</p>
        <a href="{{ route('portal.pay') }}"
           style="display:inline-block;background:white;color:#16a34a;padding:10px 24px;border-radius:10px;font-size:14px;font-weight:700;text-decoration:none;">
            Pay Now →
        </a>
    </div>

    {{-- Payment history --}}
    <div class="section-card" style="margin-bottom:1.25rem;">
        <div class="section-header">
            <span class="section-title">Recent payments</span>
            <a href="{{ route('portal.payments') }}" class="section-link">View all →</a>
        </div>

        @forelse($recentPayments as $payment)
            <div style="padding:12px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f9fafb;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;
                {{ $payment->category === 'tithe'        ? 'background:#dbeafe;' : '' }}
                {{ $payment->category === 'offering'     ? 'background:#dcfce7;' : '' }}
                {{ $payment->category === 'thanksgiving' ? 'background:#fef3c7;' : '' }}
                {{ $payment->category === 'pledge'       ? 'background:#ede9fe;' : '' }}
                {{ $payment->category === 'welfare'      ? 'background:#fee2e2;' : '' }}
                {{ !in_array($payment->category, ['tithe','offering','thanksgiving','pledge','welfare']) ? 'background:#f3f4f6;' : '' }}">
                        @switch($payment->category)
                            @case('tithe')        🙏 @break
                            @case('offering')     💝 @break
                            @case('thanksgiving') 🎉 @break
                            @case('pledge')       📋 @break
                            @case('welfare')      ❤️ @break
                            @default              💰
                        @endswitch
                    </div>
                    <div>
                        <p style="font-size:14px;font-weight:500;color:#111827;">
                            {{ ucfirst($payment->category) }}
                        </p>
                        <p style="font-size:12px;color:#9ca3af;">
                            {{ $payment->payment_date->format('d M Y') }} ·
                            {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                        </p>
                    </div>
                </div>
                <div style="text-align:right;">
                    <p style="font-size:14px;font-weight:700;color:#16a34a;">
                        {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                    </p>
                    @if($payment->currency !== 'GHS')
                        <p style="font-size:11px;color:#9ca3af;">
                            GH₵ {{ number_format($payment->amount_ghs, 2) }}
                        </p>
                    @endif
                </div>
            </div>
        @empty
            <div style="padding:32px 16px;text-align:center;color:#9ca3af;font-size:14px;">
                No payment records yet.
                <a href="{{ route('portal.pay') }}" style="color:#16a34a;text-decoration:none;display:block;margin-top:8px;">
                    Make your first payment →
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pending online payments --}}
    @if(count($pendingPayments) > 0)
        <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:14px;padding:16px;margin-bottom:1.25rem;">
            <p style="font-size:14px;font-weight:600;color:#d97706;margin-bottom:10px;">
                ⏳ {{ count($pendingPayments) }} pending payment(s)
            </p>
            @foreach($pendingPayments as $pending)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #fde68a;">
                    <div>
                        <p style="font-size:13px;font-weight:500;color:#111827;">{{ ucfirst($pending->category) }}</p>
                        <p style="font-size:11px;color:#9ca3af;font-family:monospace;">Ref: {{ $pending->reference }}</p>
                    </div>
                    <p style="font-size:13px;font-weight:700;color:#d97706;">
                        {{ $pending->currency }} {{ number_format($pending->amount, 2) }}
                    </p>
                </div>
            @endforeach
            <p style="font-size:12px;color:#9ca3af;margin-top:8px;">
                These will be confirmed by our finance team shortly.
            </p>
        </div>
    @endif

    {{-- Recent attendance --}}
    <div class="section-card">
        <div class="section-header">
            <span class="section-title">Recent attendance</span>
            <a href="{{ route('portal.attendance') }}" class="section-link">View all →</a>
        </div>
        @forelse($recentAttendance as $record)
            <div class="att-row">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div class="att-type" style="
                    {{ $record->event->type === 'sunday'  ? 'background:#dbeafe;color:#2563eb;' : '' }}
                    {{ $record->event->type === 'midweek' ? 'background:#ede9fe;color:#7c3aed;' : '' }}
                    {{ $record->event->type === 'special' ? 'background:#fef3c7;color:#d97706;' : '' }}">
                        {{ strtoupper(substr($record->event->type,0,2)) }}
                    </div>
                    <div>
                        <div class="att-name">{{ $record->event->title }}</div>
                        <div class="att-date">{{ $record->event->event_date->format('D, d M Y') }}</div>
                    </div>
                </div>
                <div>
                    <span class="att-badge">✓ Attended</span>
                    <div class="att-time">{{ $record->checked_in_at->format('h:i A') }}</div>
                </div>
            </div>
        @empty
            <div style="padding:32px 16px;text-align:center;color:#9ca3af;font-size:14px;">
                No attendance records yet.
            </div>
        @endforelse
    </div>

    {{-- Upcoming events --}}
    @if(count($upcomingEvents))
        <div class="section-card">
            <div class="section-header">
                <span class="section-title">Upcoming events</span>
            </div>
            @foreach($upcomingEvents as $event)
                <div class="event-row">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="event-date-box">
                            <div class="event-date-day">{{ $event->event_date->format('d') }}</div>
                            <div class="event-date-month">{{ $event->event_date->format('M') }}</div>
                        </div>
                        <div>
                            <div style="font-size:14px;font-weight:500;color:#111827;">{{ $event->title }}</div>
                            <div style="font-size:12px;color:#9ca3af;">
                                {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}
                            </div>
                        </div>
                    </div>
                    <span style="font-size:12px;padding:3px 10px;border-radius:20px;font-weight:500;
                {{ $event->status === 'active'   ? 'background:#dcfce7;color:#15803d;' : 'background:#fef3c7;color:#d97706;' }}">
                {{ ucfirst($event->status) }}
            </span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Logout --}}
    <div class="logout-form">
        <form method="POST" action="{{ route('portal.logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Sign out</button>
        </form>
    </div>

</div>
</body>
</html>
