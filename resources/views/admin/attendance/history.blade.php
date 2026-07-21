@extends('layouts.admin')
@section('page-title', 'Attendance History')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Attendance History</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Digital check-ins and manual headcount by event over time</p>
        </div>

        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <a href="{{ route('admin.attendance.history.export.excel', ['from' => $from, 'to' => $to]) }}"
               style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:8px 14px;border-radius:8px;font-size:13px;text-decoration:none;">
                📥 Excel
            </a>
            <a href="{{ route('admin.attendance.history.export.pdf', ['from' => $from, 'to' => $to]) }}"
               style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:8px 14px;border-radius:8px;font-size:13px;text-decoration:none;">
                📄 PDF
            </a>
            <a href="{{ route('admin.attendance.index') }}"
               style="border:1px solid #d1d5db;color:#374151;padding:8px 14px;border-radius:8px;font-size:13px;text-decoration:none;">
                ← Back to Attendance
            </a>
        </div>
    </div>

    {{-- Date range filter --}}
    <form method="GET" style="display:flex;gap:10px;margin-bottom:1.5rem;flex-wrap:wrap;align-items:center;">
        <label style="font-size:13px;color:#374151;">From</label>
        <input type="date" name="from" value="{{ $from }}"
               style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
        <label style="font-size:13px;color:#374151;">To</label>
        <input type="date" name="to" value="{{ $to }}"
               style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
        <button type="submit"
                style="background:#f3f4f6;border:1px solid #d1d5db;padding:8px 16px;border-radius:8px;font-size:13px;cursor:pointer;">
            Filter
        </button>
    </form>

    {{-- Events table --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Event</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Date</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Digital check-ins</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Manual headcount</th>
            </tr>
            </thead>
            <tbody>
            @forelse($events as $event)
                <tr style="border-top:1px solid #f3f4f6;">
                    <td style="padding:12px 16px;font-weight:500;color:#111827;">{{ $event->title }}</td>
                    <td style="padding:12px 16px;color:#6b7280;">{{ $event->event_date->format('D, d M Y') }}</td>
                    <td style="padding:12px 16px;color:#374151;">{{ number_format($event->digital_count) }}</td>
                    <td style="padding:12px 16px;color:#374151;">{{ number_format($event->manual_count) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding:48px;text-align:center;color:#9ca3af;font-size:14px;">
                        No events in this date range.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $events->links() }}</div>

@endsection
