<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #111827; margin: 0; padding: 20px; }
        h1   { font-size: 18px; color: #1d4ed8; margin-bottom: 4px; }
        .sub { font-size: 12px; color: #6b7280; margin-bottom: 20px; }
        table{ width: 100%; border-collapse: collapse; margin-top: 10px; }
        th   { background: #1d4ed8; color: white; padding: 8px 10px; text-align: left; font-size: 11px; }
        td   { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        tr:nth-child(even) td { background: #f9fafb; }
        .badge { padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; }
        .footer { margin-top: 20px; font-size: 10px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>

<h1>Attendance Report</h1>
<p class="sub">
    @if($event) Event: {{ $event->title }} — {{ $event->event_date->format('d M Y') }}
    @else All events @endif
    · Generated: {{ now()->format('d M Y, h:i A') }}
</p>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Member Name</th>
        <th>Member ID</th>
        <th>TACMS No.</th>
        <th>Event</th>
        <th>Date</th>
        <th>Time</th>
        <th>Method</th>
    </tr>
    </thead>
    <tbody>
    @foreach($records as $i => $record)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $record->member->full_name ?? '—' }}</td>
            <td style="font-family:monospace;">{{ $record->member->member_id_card ?? '—' }}</td>
            <td style="font-family:monospace;">{{ $record->member->tacms_number ?? '—' }}</td>
            <td>{{ $record->event->title ?? '—' }}</td>
            <td>{{ $record->event->event_date->format('d M Y') ?? '—' }}</td>
            <td>{{ $record->checked_in_at->format('h:i A') }}</td>
            <td>{{ ucwords(str_replace('_', ' ', $record->checkin_method)) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p class="footer">Total records: {{ count($records) }} · {{ config('app.name', 'Church Management System') }}</p>

</body>
</html>
