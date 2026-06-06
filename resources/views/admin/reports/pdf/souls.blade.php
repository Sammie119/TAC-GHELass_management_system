<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body  { font-family: Arial, sans-serif; font-size: 11px; color: #111827; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2563eb; padding-bottom: 14px; }
        h1    { font-size: 20px; color: #2563eb; margin: 0 0 4px; }
        .sub  { font-size: 12px; color: #6b7280; }
        .summary { display: table; width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .sum-cell { display: table-cell; padding: 12px; border-radius: 8px; text-align: center; border: 1px solid #e5e7eb; }
        h2    { font-size: 13px; font-weight: 700; margin: 16px 0 8px; color: #374151;
            border-left: 3px solid #2563eb; padding-left: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th    { padding: 7px 10px; text-align: left; font-size: 10px; font-weight: 600;
            background: #2563eb; color: white; }
        td    { padding: 6px 10px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
        tr:nth-child(even) td { background: #f9fafb; }
        .badge { padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 600; }
        .footer { margin-top: 20px; font-size: 10px; color: #9ca3af; text-align: right;
            border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>

<div class="header">
    <h1>New Souls Report</h1>
    <p class="sub">Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
    <p class="sub">Generated: {{ now()->format('d M Y, h:i A') }}</p>
</div>

{{-- Summary --}}
<table style="margin-bottom:20px;border-collapse:collapse;">
    <tr>
        <td style="padding:10px 14px;background:#dbeafe;border-radius:8px;text-align:center;border:1px solid #bfdbfe;width:16%;">
            <div style="font-size:11px;color:#2563eb;margin-bottom:4px;">Total</div>
            <div style="font-size:22px;font-weight:800;color:#2563eb;">{{ $stats['total'] }}</div>
        </td>
        <td style="width:2%;"></td>
        <td style="padding:10px 14px;background:#dcfce7;border-radius:8px;text-align:center;border:1px solid #bbf7d0;width:16%;">
            <div style="font-size:11px;color:#16a34a;margin-bottom:4px;">Converted</div>
            <div style="font-size:22px;font-weight:800;color:#16a34a;">{{ $stats['converted'] }}</div>
        </td>
        <td style="width:2%;"></td>
        <td style="padding:10px 14px;background:#ede9fe;border-radius:8px;text-align:center;border:1px solid #ddd6fe;width:16%;">
            <div style="font-size:11px;color:#7c3aed;margin-bottom:4px;">Attending</div>
            <div style="font-size:22px;font-weight:800;color:#7c3aed;">{{ $stats['attending'] }}</div>
        </td>
        <td style="width:2%;"></td>
        <td style="padding:10px 14px;background:#fef3c7;border-radius:8px;text-align:center;border:1px solid #fde68a;width:16%;">
            <div style="font-size:11px;color:#d97706;margin-bottom:4px;">Contacted</div>
            <div style="font-size:22px;font-weight:800;color:#d97706;">{{ $stats['contacted'] }}</div>
        </td>
        <td style="width:2%;"></td>
        <td style="padding:10px 14px;background:#fee2e2;border-radius:8px;text-align:center;border:1px solid #fecaca;width:16%;">
            <div style="font-size:11px;color:#dc2626;margin-bottom:4px;">Backslidden</div>
            <div style="font-size:22px;font-weight:800;color:#dc2626;">{{ $stats['backslidden'] }}</div>
        </td>
    </tr>
</table>

{{-- Top soul winners --}}
@if(count($byWinner) > 0)
    <h2>Top Soul Winners</h2>
    <table>
        <thead><tr><th>#</th><th>Member Name</th><th>Souls Won</th></tr></thead>
        <tbody>
        @foreach($byWinner as $i => $w)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $w['name'] }}</td>
                <td style="font-weight:700;color:#2563eb;">{{ $w['count'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

{{-- Full list --}}
<h2>All Souls ({{ $stats['total'] }})</h2>
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Area</th>
        <th>Date Won</th>
        <th>Won By</th>
        <th>Status</th>
        <th>Follow-ups</th>
    </tr>
    </thead>
    <tbody>
    @foreach($souls as $i => $soul)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $soul->full_name }}</td>
            <td>{{ $soul->phone ?? '—' }}</td>
            <td>{{ $soul->area ?? '—' }}</td>
            <td>{{ $soul->date_won->format('d M Y') }}</td>
            <td>{{ $soul->wonBy?->full_name ?? '—' }}</td>
            <td>{{ ucfirst($soul->status) }}</td>
            <td style="text-align:center;">{{ $soul->followups->count() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="footer">
    Church Attendance System · New Souls Report · Confidential
</div>

</body>
</html>
