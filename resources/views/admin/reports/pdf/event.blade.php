<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body  { font-family: sans-serif; font-size: 12px; color: #111827; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        h1    { font-size: 20px; color: #1d4ed8; margin-bottom: 4px; }
        .meta { font-size: 12px; color: #6b7280; margin-bottom: 4px; }
        .stat { display: inline-block; background: #dbeafe; color: #1d4ed8; padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th    { background: #1d4ed8; color: white; padding: 8px 10px; text-align: left; font-size: 11px; }
        td    { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        tr:nth-child(even) td { background: #f9fafb; }
        .footer { margin-top: 20px; font-size: 10px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>

<h1>{{ $event->title }}</h1>
<p class="meta">{{ $event->event_date->format('l, d F Y') }} · {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}</p>
<p class="meta">Type: {{ ucfirst($event->type) }} · Status: {{ ucfirst($event->status) }}</p>
<span class="stat">{{ count($attendance) }} members checked in</span>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Member Name</th>
        <th>Member ID</th>
        <th>Check-in Time</th>
        <th>Method</th>
    </tr>
    </thead>
    <tbody>
    @foreach($attendance as $i => $record)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $record->member->full_name ?? '—' }}</td>
            <td style="font-family:monospace;">{{ $record->member->member_id_card ?? '—' }}</td>
            <td>{{ $record->checked_in_at->format('h:i A') }}</td>
            <td>{{ ucwords(str_replace('_', ' ', $record->checkin_method)) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p class="footer">Generated: {{ now()->format('d M Y, h:i A') }} · {{ config('app.name', 'Church Management System') }}</p>

</body>
</html>
