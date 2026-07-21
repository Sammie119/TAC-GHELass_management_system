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
        .footer { margin-top: 20px; font-size: 10px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>

<h1>Attendance Headcount Report</h1>
<p class="sub">
    {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
    · Generated: {{ now()->format('d M Y, h:i A') }}
</p>

<table>
    <thead>
    <tr>
        <th>Event</th>
        <th>Date</th>
        <th>Digital Check-ins</th>
        <th>Male</th>
        <th>Female</th>
        <th>Children</th>
        <th>Youth</th>
        <th>Visitors</th>
        <th>Manual Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($events as $event)
        <tr>
            <td>{{ $event->title }}</td>
            <td>{{ $event->event_date->format('d M Y') }}</td>
            <td>{{ $event->digital_count }}</td>
            <td>{{ $event->headcount->male ?? 0 }}</td>
            <td>{{ $event->headcount->female ?? 0 }}</td>
            <td>{{ $event->headcount->children ?? 0 }}</td>
            <td>{{ $event->headcount->youth ?? 0 }}</td>
            <td>{{ $event->headcount->visitors ?? 0 }}</td>
            <td>{{ $event->manual_count }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p class="footer">Total events: {{ $events->count() }} · {{ config('app.name', 'Church Management System') }}</p>

</body>
</html>
