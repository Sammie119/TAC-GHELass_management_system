<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #111827; margin: 0; padding: 20px; }
        h1   { font-size: 18px; color: #16a34a; margin-bottom: 4px; }
        .sub { font-size: 12px; color: #6b7280; margin-bottom: 20px; }
        table{ width: 100%; border-collapse: collapse; }
        th   { background: #16a34a; color: white; padding: 8px 10px; text-align: left; font-size: 11px; }
        td   { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        tr:nth-child(even) td { background: #f9fafb; }
        .footer { margin-top: 20px; font-size: 10px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>

<h1>Members Report</h1>
<p class="sub">All active members · Generated: {{ now()->format('d M Y, h:i A') }}</p>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Member ID</th>
        <th>Full Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Gender</th>
        <th>Attendance</th>
        <th>Joined</th>
    </tr>
    </thead>
    <tbody>
    @foreach($members as $i => $member)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td style="font-family:monospace;">{{ $member->member_id_card }}</td>
            <td>{{ $member->full_name }}</td>
            <td>{{ $member->phone ?? '—' }}</td>
            <td>{{ $member->email ?? '—' }}</td>
            <td>{{ ucfirst($member->gender ?? '—') }}</td>
            <td style="text-align:center;font-weight:700;color:#2563eb;">{{ $member->attendance_count }}</td>
            <td>{{ $member->created_at->format('d M Y') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p class="footer">Total: {{ count($members) }} members · {{ config('app.name', 'Church Management System') }}</p>

</body>
</html>
