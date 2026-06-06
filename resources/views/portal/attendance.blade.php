<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance History — Member Portal</title>

    {{-- Favicons --}}
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="shortcut icon" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/favicon.svg">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f0f9ff; min-height: 100vh; }
        .nav {
            background: white; border-bottom: 1px solid #e5e7eb;
            padding: 0 1rem; height: 56px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 10;
        }
        .nav-back { display: flex; align-items: center; gap: 8px; text-decoration: none; color: #374151; font-size: 14px; font-weight: 500; }
        .nav-title { font-size: 15px; font-weight: 600; color: #111827; }
        .content { max-width: 640px; margin: 0 auto; padding: 1.5rem 1rem; }
        .card { background: white; border-radius: 14px; border: 1px solid #e5e7eb; overflow: hidden; }
        .card-header {
            padding: 14px 16px; border-bottom: 1px solid #f3f4f6;
            display: flex; justify-content: space-between; align-items: center;
        }
        .card-title { font-size: 14px; font-weight: 600; color: #111827; }
        .row {
            padding: 13px 16px; display: flex; align-items: center;
            justify-content: space-between; border-bottom: 1px solid #f9fafb;
        }
        .row:last-child { border-bottom: none; }
        .type-badge {
            width: 34px; height: 34px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; flex-shrink: 0;
        }
        .event-name { font-size: 14px; font-weight: 500; color: #111827; }
        .event-meta { font-size: 12px; color: #9ca3af; }
        .check-badge {
            font-size: 11px; padding: 3px 10px; border-radius: 20px;
            background: #dcfce7; color: #15803d; font-weight: 500;
        }
        .method-badge {
            font-size: 11px; color: #9ca3af; text-align: right; margin-top: 2px;
        }
        .pagination { margin-top: 1rem; }
    </style>
</head>
<body>

<nav class="nav">
    <a href="{{ route('portal.dashboard') }}" class="nav-back">
        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back
    </a>
    <span class="nav-title">Attendance History</span>
    <span style="width:60px;"></span>
</nav>

<div class="content">
    <div class="card">
        <div class="card-header">
            <span class="card-title">All attendance records</span>
            <span style="font-size:12px;color:#9ca3af;">{{ $attendance->total() }} total</span>
        </div>

        @forelse($attendance as $record)
            <div class="row">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div class="type-badge" style="
                    {{ $record->event->type === 'sunday'  ? 'background:#dbeafe;color:#2563eb;' : '' }}
                    {{ $record->event->type === 'midweek' ? 'background:#ede9fe;color:#7c3aed;' : '' }}
                    {{ $record->event->type === 'special' ? 'background:#fef3c7;color:#d97706;' : '' }}">
                        {{ strtoupper(substr($record->event->type,0,2)) }}
                    </div>
                    <div>
                        <div class="event-name">{{ $record->event->title }}</div>
                        <div class="event-meta">
                            {{ $record->event->event_date->format('D, d M Y') }} ·
                            {{ $record->checked_in_at->format('h:i A') }}
                        </div>
                    </div>
                </div>
                <div style="text-align:right;">
                    <span class="check-badge">✓ Attended</span>
                    <div class="method-badge">
                        {{ ucwords(str_replace('_', ' ', $record->checkin_method)) }}
                    </div>
                </div>
            </div>
        @empty
            <div style="padding:40px 16px;text-align:center;color:#9ca3af;font-size:14px;">
                No attendance records yet.
            </div>
        @endforelse
    </div>

    <div class="pagination">
        {{ $attendance->links() }}
    </div>
</div>
</body>
</html>
