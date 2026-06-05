@extends('layouts.admin')
@section('page-title', 'Reports')
@section('content')

    {{-- Summary stats --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:2rem;">
        <style>@media(max-width:768px){.stats-grid{grid-template-columns:1fr !important;}}</style>

        <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:20px;">
            <p style="font-size:13px;color:#6b7280;margin-bottom:6px;">Active members</p>
            <p style="font-size:32px;font-weight:700;color:#2563eb;">{{ number_format($stats['total_members']) }}</p>
        </div>
        <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:20px;">
            <p style="font-size:13px;color:#6b7280;margin-bottom:6px;">Total check-ins</p>
            <p style="font-size:32px;font-weight:700;color:#16a34a;">{{ number_format($stats['total_checkins']) }}</p>
            <p style="font-size:12px;color:#9ca3af;margin-top:2px;">{{ $stats['this_month_checkins'] }} this month</p>
        </div>
        <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:20px;">
            <p style="font-size:13px;color:#6b7280;margin-bottom:6px;">Total visitors</p>
            <p style="font-size:32px;font-weight:700;color:#d97706;">{{ number_format($stats['total_visitors']) }}</p>
            <p style="font-size:12px;color:#9ca3af;margin-top:2px;">{{ $stats['this_month_visitors'] }} this month</p>
        </div>
    </div>

    {{-- Export cards --}}
    <div style="display:grid;gap:24px;margin-bottom:2rem;">
        <style>@media(min-width:768px){.export-grid{grid-template-columns:repeat(3,1fr) !important;}}</style>
        <div style="display:grid;gap:16px;" class="export-grid">

            {{-- Attendance export --}}
            <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:24px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <div style="width:36px;height:36px;background:#dbeafe;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:18px;height:18px;color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:14px;font-weight:600;color:#111827;">Attendance Report</p>
                        <p style="font-size:12px;color:#9ca3af;">Filter by event or date range</p>
                    </div>
                </div>

                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">Event (optional)</label>
                    <select id="att-event"
                            style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;">
                        <option value="">All events</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->title }} — {{ $event->event_date->format('d M Y') }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">From</label>
                        <input type="date" id="att-from"
                               style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">To</label>
                        <input type="date" id="att-to"
                               style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <button onclick="exportReport('attendance', 'excel')"
                            style="background:#16a34a;color:white;padding:9px;border-radius:8px;font-size:13px;font-weight:500;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Excel
                    </button>
                    <button onclick="exportReport('attendance', 'pdf')"
                            style="background:#dc2626;color:white;padding:9px;border-radius:8px;font-size:13px;font-weight:500;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        PDF
                    </button>
                </div>
            </div>

            {{-- Members export --}}
            <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:24px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <div style="width:36px;height:36px;background:#dcfce7;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:18px;height:18px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:14px;font-weight:600;color:#111827;">Members Report</p>
                        <p style="font-size:12px;color:#9ca3af;">All active members with attendance count</p>
                    </div>
                </div>

                <p style="font-size:13px;color:#6b7280;margin-bottom:16px;line-height:1.6;">
                    Exports all active members including their contact details and total attendance records.
                </p>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:auto;">
                    <a href="{{ route('admin.reports.export.members.excel') }}"
                       style="background:#16a34a;color:white;padding:9px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:6px;">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Excel
                    </a>
                    <a href="{{ route('admin.reports.export.members.pdf') }}"
                       style="background:#dc2626;color:white;padding:9px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:6px;">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        PDF
                    </a>
                </div>
            </div>

            {{-- Visitors export --}}
            <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:24px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <div style="width:36px;height:36px;background:#fef3c7;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:18px;height:18px;color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:14px;font-weight:600;color:#111827;">Visitors Report</p>
                        <p style="font-size:12px;color:#9ca3af;">Filter by event or date range</p>
                    </div>
                </div>

                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">Event (optional)</label>
                    <select id="vis-event"
                            style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;">
                        <option value="">All events</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->title }} — {{ $event->event_date->format('d M Y') }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">From</label>
                        <input type="date" id="vis-from"
                               style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">To</label>
                        <input type="date" id="vis-to"
                               style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <button onclick="exportReport('visitors', 'excel')"
                            style="background:#16a34a;color:white;padding:9px;border-radius:8px;font-size:13px;font-weight:500;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Excel
                    </button>
                    <button onclick="exportReport('visitors', 'pdf')"
                            style="background:#dc2626;color:white;padding:9px;border-radius:8px;font-size:13px;font-weight:500;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        PDF
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- Absentees report link --}}
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:20px;display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;background:#fee2e2;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:20px;height:20px;color:#dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div>
                <p style="font-size:14px;font-weight:600;color:#991b1b;">Absentees Report</p>
                <p style="font-size:12px;color:#9ca3af;">See which members missed a specific event — export to Excel or PDF</p>
            </div>
        </div>
        <a href="{{ route('admin.reports.absentees') }}"
           style="background:#dc2626;color:white;padding:9px 20px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;white-space:nowrap;">
            View Absentees →
        </a>
    </div>

    {{-- Bottom: Event attendance table + Top members --}}
    <div style="display:grid;gap:24px;">
        <style>@media(min-width:1024px){.bottom-grid{grid-template-columns:2fr 1fr !important;}}</style>
        <div style="display:grid;gap:24px;" class="bottom-grid">

            {{-- Event attendance breakdown --}}
            <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;">
                <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;">Attendance by event</h3>
                    <span style="font-size:12px;color:#9ca3af;">Last 10 events</span>
                </div>
                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                    <thead style="background:#f9fafb;">
                    <tr>
                        <th style="padding:10px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;">Event</th>
                        <th style="padding:10px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;">Date</th>
                        <th style="padding:10px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;">Type</th>
                        <th style="padding:10px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;">Check-ins</th>
                        <th style="padding:10px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;">PDF</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($eventAttendance as $event)
                        <tr style="border-top:1px solid #f3f4f6;"
                            onmouseenter="this.style.background='#f9fafb'"
                            onmouseleave="this.style.background=''">
                            <td style="padding:12px 20px;font-weight:500;color:#111827;">{{ $event->title }}</td>
                            <td style="padding:12px 20px;color:#6b7280;font-size:13px;">{{ $event->event_date->format('d M Y') }}</td>
                            <td style="padding:12px 20px;">
                        <span style="padding:2px 10px;border-radius:20px;font-size:12px;font-weight:500;
                            {{ $event->type === 'sunday'  ? 'background:#dbeafe;color:#2563eb;' : '' }}
                            {{ $event->type === 'midweek' ? 'background:#ede9fe;color:#7c3aed;' : '' }}
                            {{ $event->type === 'special' ? 'background:#fef3c7;color:#d97706;' : '' }}">
                            {{ ucfirst($event->type) }}
                        </span>
                            </td>
                            <td style="padding:12px 20px;">
                                <span style="font-size:18px;font-weight:700;color:#111827;">{{ $event->attendance_count }}</span>
                            </td>
                            <td style="padding:12px 20px;">
                                <a href="{{ route('admin.reports.export.event.pdf', $event) }}"
                                   style="font-size:12px;color:#dc2626;text-decoration:none;">
                                    Download →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Top members --}}
            <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;">
                <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;">Most faithful members</h3>
                </div>
                <div>
                    @foreach($topMembers as $i => $member)
                        <div style="padding:12px 20px;display:flex;align-items:center;gap:12px;border-bottom:1px solid #f9fafb;"
                             onmouseenter="this.style.background='#f9fafb'"
                             onmouseleave="this.style.background=''">
                <span style="width:24px;height:24px;border-radius:50%;background:{{ $i < 3 ? '#fef3c7' : '#f3f4f6' }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:{{ $i < 3 ? '#d97706' : '#6b7280' }};flex-shrink:0;">
                    {{ $i + 1 }}
                </span>
                            <div style="width:32px;height:32px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:11px;font-weight:600;flex-shrink:0;">
                                {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:13px;font-weight:500;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $member->full_name }}
                                </p>
                            </div>
                            <span style="font-size:13px;font-weight:700;color:#2563eb;flex-shrink:0;">
                    {{ $member->attendance_count }}
                </span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <script>
        function exportReport(type, format) {
            let url = '';
            const routes = {
                attendance: {
                    excel: '{{ route("admin.reports.export.attendance.excel") }}',
                    pdf:   '{{ route("admin.reports.export.attendance.pdf") }}',
                },
                visitors: {
                    excel: '{{ route("admin.reports.export.visitors.excel") }}',
                    pdf:   '{{ route("admin.reports.export.visitors.pdf") }}',
                },
            };

            url = routes[type][format];

            const params = new URLSearchParams();

            if (type === 'attendance') {
                const eventId = document.getElementById('att-event').value;
                const from    = document.getElementById('att-from').value;
                const to      = document.getElementById('att-to').value;
                if (eventId) params.append('event_id', eventId);
                if (from)    params.append('from', from);
                if (to)      params.append('to', to);
            }

            if (type === 'visitors') {
                const eventId = document.getElementById('vis-event').value;
                const from    = document.getElementById('vis-from').value;
                const to      = document.getElementById('vis-to').value;
                if (eventId) params.append('event_id', eventId);
                if (from)    params.append('from', from);
                if (to)      params.append('to', to);
            }

            const query = params.toString();
            window.location.href = url + (query ? '?' + query : '');
        }
    </script>

@endsection
