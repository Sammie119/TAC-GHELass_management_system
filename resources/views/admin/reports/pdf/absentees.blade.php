<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body  { font-family: sans-serif; font-size: 12px; color: #111827; margin: 0; padding: 20px; }
        h1    { font-size: 18px; color: #dc2626; margin-bottom: 4px; }
        .sub  { font-size: 12px; color: #6b7280; margin-bottom: 6px; }
        .stat { display: inline-block; background: #fee2e2; color: #dc2626; padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th    { background: #dc2626; color: white; padding: 8px 10px; text-align: left; font-size: 11px; }
        td    { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        tr:nth-child(even) td { background: #fef2f2; }
        .footer { margin-top: 20px; font-size: 10px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>

<h1>Absentees Report</h1>
<p class="sub">Event: <strong>{{ $event->title }}</strong></p>
<p class="sub">Date: {{ $event->event_date->format('D, d F Y') }} · Type: {{ ucfirst($event->type) }}</p>
<span class="stat">{{ count($absentees) }} members absent</span>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Member ID</th>
        <th>Full Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Total Attendance</th>
        <th>Last Seen</th>
    </tr>
    </thead>
    <tbody>
    @foreach($absentees as $i => $member)
        @php
            $lastAttendance = $member->attendance()->latest('checked_in_at')->first();
        @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td style="font-family:monospace;">{{ $member->member_id_card }}</td>
            <td>{{ $member->full_name }}</td>
            <td>{{ $member->phone ?? '—' }}</td>
            <td>{{ $member->email ?? '—' }}</td>
            <td style="text-align:center;font-weight:700;">{{ $member->attendance()->count() }}</td>
            <td>
                {{ $lastAttendance
                    ? $lastAttendance->checked_in_at->format('d M Y')
                    : 'Never attended' }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<p class="footer">
    Generated: {{ now()->format('d M Y, h:i A') }} · {{ config('app.name', 'Church Management System') }}
</p>

</body>
</html>
