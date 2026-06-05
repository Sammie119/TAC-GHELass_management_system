<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body  { font-family: sans-serif; font-size: 12px; color: #111827; margin: 0; padding: 20px; }
        h1    { font-size: 18px; color: #d97706; margin-bottom: 4px; }
        .sub  { font-size: 12px; color: #6b7280; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th    { background: #d97706; color: white; padding: 8px 10px; text-align: left; font-size: 11px; }
        td    { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        tr:nth-child(even) td { background: #fefce8; }
        .footer { margin-top: 20px; font-size: 10px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>

<h1>Visitors Report</h1>
<p class="sub">
    @if($event)
        Event: {{ $event->title }} — {{ $event->event_date->format('d M Y') }}
    @else
        All events
    @endif
    · Generated: {{ now()->format('d M Y, h:i A') }}
</p>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Full Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Event</th>
        <th>Event Date</th>
        <th>Visited At</th>
        <th>Recorded By</th>
        <th>Notes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($visitors as $i => $visitor)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $visitor->full_name }}</td>
            <td>{{ $visitor->phone ?? '—' }}</td>
            <td>{{ $visitor->email ?? '—' }}</td>
            <td>{{ $visitor->event->title ?? '—' }}</td>
            <td>{{ $visitor->event->event_date->format('d M Y') ?? '—' }}</td>
            <td>{{ $visitor->visited_at->format('d M Y h:i A') }}</td>
            <td>{{ $visitor->recordedBy->name ?? 'System' }}</td>
            <td>{{ $visitor->notes ?? '—' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p class="footer">Total: {{ count($visitors) }} visitors · {{ config('app.name', 'Church Management System') }}</p>

</body>
</html>
