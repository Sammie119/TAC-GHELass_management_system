@extends('layouts.admin')
@section('page-title', 'Notifications')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Notifications</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Send SMS and email notifications to members</p>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:1.5rem;">
        <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:18px;text-align:center;">
            <p style="font-size:26px;font-weight:800;color:#111827;">{{ number_format($stats['total']) }}</p>
            <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Total sent</p>
        </div>
        <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:18px;text-align:center;">
            <p style="font-size:26px;font-weight:800;color:#2563eb;">{{ number_format($stats['sms']) }}</p>
            <p style="font-size:12px;color:#9ca3af;margin-top:4px;">SMS sent</p>
        </div>
        <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:18px;text-align:center;">
            <p style="font-size:26px;font-weight:800;color:#16a34a;">{{ number_format($stats['email']) }}</p>
            <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Emails sent</p>
        </div>
        <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:18px;text-align:center;">
            <p style="font-size:26px;font-weight:800;color:#d97706;">{{ number_format($stats['today']) }}</p>
            <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Sent today</p>
        </div>
    </div>

    <div style="display:grid;gap:24px;">
        <style>@media(min-width:1024px){.notif-grid{grid-template-columns:1fr 1fr !important;}}</style>
        <div style="display:grid;gap:24px;" class="notif-grid">

            {{-- Send manual notification --}}
            <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:24px;">
                <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:20px;">Send notification</h3>

                @if(session('success'))
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.notifications.send') }}">
                    @csrf

                    {{-- Type --}}
                    <div style="margin-bottom:14px;">
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                            Notification type
                        </label>
                        <select name="type" id="notif-type" onchange="toggleFields()"
                                style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;">
                            <option value="welcome">👋 Welcome message</option>
                            <option value="event_reminder">📅 Event reminder</option>
                            <option value="absentee_followup">💙 Absentee follow-up</option>
                            <option value="birthday">🎂 Birthday message</option>
                            <option value="custom">✏️ Custom message</option>
                            <option value="visitor_message">🙋 Message today's visitors</option>
                        </select>
                    </div>

                    {{-- Event selector (for reminders) --}}
                    <div id="event-field" style="margin-bottom:14px;display:none;">
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                            Select event
                        </label>
                        <select name="event_id"
                                style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;">
                            <option value="">— Choose event —</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">
                                    {{ $event->title }} — {{ $event->event_date->format('d M Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Custom message --}}
                    <div id="custom-field" style="margin-bottom:14px;display:none;">
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                            Message <span style="color:#9ca3af;font-weight:400;">(max 160 characters)</span>
                        </label>
                        <textarea name="message" rows="3" maxlength="160" id="custom-msg"
                                  placeholder="Type your message here..."
                                  style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;resize:none;box-sizing:border-box;"
                                  oninput="updateCount(this)"></textarea>
                        <p style="font-size:12px;color:#9ca3af;margin-top:4px;" id="char-count">0 / 160</p>
                    </div>

                    {{-- Visitor audience note --}}
                    <div id="visitor-note" style="margin-bottom:14px;display:none;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 14px;">
                        <p style="font-size:13px;color:#1d4ed8;">
                            {{ $visitorsToday }} visitor(s) checked in today will receive this message.
                        </p>
                    </div>

                    {{-- Members --}}
                    <div id="members-field" style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                            Send to
                        </label>
                        <div style="display:flex;gap:8px;margin-bottom:8px;">
                            <button type="button" onclick="selectAllMembers(true)"
                                    style="font-size:12px;color:#2563eb;background:#eff6ff;border:none;padding:4px 10px;border-radius:6px;cursor:pointer;">
                                Select all
                            </button>
                            <button type="button" onclick="selectAllMembers(false)"
                                    style="font-size:12px;color:#6b7280;background:#f3f4f6;border:none;padding:4px 10px;border-radius:6px;cursor:pointer;">
                                Clear
                            </button>
                        </div>
                        <div style="border:1px solid #d1d5db;border-radius:8px;max-height:180px;overflow-y:auto;padding:8px;">
                            @foreach($members as $member)
                                <label style="display:flex;align-items:center;gap:8px;padding:5px 8px;border-radius:6px;cursor:pointer;"
                                       onmouseenter="this.style.background='#f9fafb'"
                                       onmouseleave="this.style.background=''">
                                    <input type="checkbox" name="member_ids[]" value="{{ $member->id }}"
                                           class="member-cb"
                                           style="accent-color:#2563eb;width:15px;height:15px;">
                                    <span style="font-size:13px;color:#111827;">{{ $member->full_name }}</span>
                                    <span style="font-size:11px;color:#9ca3af;margin-left:auto;">
                            {{ $member->phone ? '📱' : '' }}
                                        {{ $member->email ? '✉️' : '' }}
                        </span>
                                </label>
                            @endforeach
                        </div>
                        <p style="font-size:12px;color:#9ca3af;margin-top:4px;">
                            📱 = has phone · ✉️ = has email
                        </p>
                    </div>

                    <button type="submit"
                            style="width:100%;background:#2563eb;color:white;padding:11px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                        Send notification
                    </button>
                </form>
            </div>

            {{-- Quick commands + log --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                {{-- Automated commands --}}
                <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:24px;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:16px;">Run automated notifications</h3>

                    <div style="display:flex;flex-direction:column;gap:10px;">

                        <form method="POST" action="{{ route('admin.notifications.run-command') }}"
                              style="display:flex;align-items:center;justify-content:space-between;padding:12px;background:#f9fafb;border-radius:8px;border:1px solid #f3f4f6;">
                            @csrf
                            <input type="hidden" name="command" value="notifications:birthdays">
                            <div>
                                <p style="font-size:13px;font-weight:500;color:#111827;">🎂 Birthday messages</p>
                                <p style="font-size:12px;color:#9ca3af;">Sends to today's birthdays</p>
                            </div>
                            <button type="submit"
                                    style="background:#d97706;color:white;padding:6px 14px;border-radius:6px;font-size:12px;border:none;cursor:pointer;">
                                Run now
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.notifications.run-command') }}"
                              style="display:flex;align-items:center;justify-content:space-between;padding:12px;background:#f9fafb;border-radius:8px;border:1px solid #f3f4f6;">
                            @csrf
                            <input type="hidden" name="command" value="notifications:event-reminders">
                            <div>
                                <p style="font-size:13px;font-weight:500;color:#111827;">📅 Event reminders</p>
                                <p style="font-size:12px;color:#9ca3af;">Reminds members of tomorrow's events</p>
                            </div>
                            <button type="submit"
                                    style="background:#2563eb;color:white;padding:6px 14px;border-radius:6px;font-size:12px;border:none;cursor:pointer;">
                                Run now
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.notifications.run-command') }}"
                              style="display:flex;align-items:center;justify-content:space-between;padding:12px;background:#f9fafb;border-radius:8px;border:1px solid #f3f4f6;">
                            @csrf
                            <input type="hidden" name="command" value="notifications:absentees">
                            <div>
                                <p style="font-size:13px;font-weight:500;color:#111827;">💙 Absentee follow-ups</p>
                                <p style="font-size:12px;color:#9ca3af;">Contacts all flagged absentees</p>
                            </div>
                            <button type="submit"
                                    style="background:#dc2626;color:white;padding:6px 14px;border-radius:6px;font-size:12px;border:none;cursor:pointer;">
                                Run now
                            </button>
                        </form>

                    </div>
                </div>

                {{-- Recent log --}}
                <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;flex:1;">
                    <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;">
                        <h3 style="font-size:14px;font-weight:600;color:#111827;">Recent notifications</h3>
                    </div>
                    <div style="max-height:320px;overflow-y:auto;">
                        @forelse($logs as $log)
                            <div style="padding:10px 16px;border-bottom:1px solid #f9fafb;display:flex;align-items:center;gap:10px;"
                                 onmouseenter="this.style.background='#f9fafb'"
                                 onmouseleave="this.style.background=''">
                                <div style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;background:#f3f4f6;">
                                    @switch($log->type)
                                        @case('welcome')           🙋 @break
                                        @case('birthday')          🎂 @break
                                        @case('event_reminder')    📅 @break
                                        @case('absentee_followup') 💙 @break
                                        @case('checkin_confirmation') ✓ @break
                                        @case('visitor_message')   🛎️ @break
                                        @default                   ✉️
                                    @endswitch
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <p style="font-size:13px;font-weight:500;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $log->member->full_name ?? ($log->visitor->full_name ?? '—') }}
                                    </p>
                                    <p style="font-size:11px;color:#9ca3af;">
                                        {{ ucwords(str_replace('_', ' ', $log->type)) }} ·
                                        {{ $log->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div style="display:flex;gap:4px;flex-shrink:0;">
                                    @if($log->sms_sent)
                                        <span style="font-size:10px;background:#dbeafe;color:#2563eb;padding:2px 6px;border-radius:4px;">SMS</span>
                                    @endif
                                    @if($log->email_sent)
                                        <span style="font-size:10px;background:#dcfce7;color:#15803d;padding:2px 6px;border-radius:4px;">Email</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div style="padding:32px;text-align:center;color:#9ca3af;font-size:14px;">
                                No notifications sent yet.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        function toggleFields() {
            const type = document.getElementById('notif-type').value;
            document.getElementById('event-field').style.display   = type === 'event_reminder' ? 'block' : 'none';
            document.getElementById('custom-field').style.display  = (type === 'custom' || type === 'visitor_message') ? 'block' : 'none';
            document.getElementById('visitor-note').style.display  = type === 'visitor_message' ? 'block' : 'none';
            document.getElementById('members-field').style.display = type === 'visitor_message' ? 'none' : 'block';
        }

        function selectAllMembers(checked) {
            document.querySelectorAll('.member-cb').forEach(cb => cb.checked = checked);
        }

        function updateCount(el) {
            document.getElementById('char-count').textContent = el.value.length + ' / 160';
        }
    </script>

@endsection
