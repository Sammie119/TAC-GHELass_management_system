@extends('layouts.admin')
@section('page-title', 'Absentees Report')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Absentees Report</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">
                Members who did not attend a selected event
            </p>
        </div>
        <a href="{{ route('admin.reports.index') }}"
           style="border:1px solid #d1d5db;color:#374151;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
            ← Back to reports
        </a>
    </div>

    {{-- Event selector --}}
    <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:24px;margin-bottom:1.5rem;">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:250px;">
                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                    Select event to check absentees
                </label>
                <select name="event_id"
                        style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;">
                    <option value="">— Choose an event —</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}"
                            {{ request('event_id') == $event->id ? 'selected' : '' }}>
                            {{ $event->title }} — {{ $event->event_date->format('D, d M Y') }}
                            ({{ ucfirst($event->type) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    style="background:#2563eb;color:white;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:500;border:none;cursor:pointer;">
                Check absentees
            </button>
        </form>
    </div>

    @if($selectedEvent)

        {{-- Summary banner --}}
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:20px;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
            <div>
                <p style="font-size:14px;font-weight:600;color:#dc2626;margin-bottom:4px;">
                    {{ $absentees->total() }} members absent
                </p>
                <p style="font-size:13px;color:#6b7280;">
                    from <strong>{{ $selectedEvent->title }}</strong>
                    on {{ $selectedEvent->event_date->format('D, d M Y') }}
                    · {{ $totalMembers - $absentees->total() }} of {{ $totalMembers }} attended
                    ({{ $totalMembers > 0 ? round((($totalMembers - $absentees->total()) / $totalMembers) * 100) : 0 }}%)
                </p>
            </div>

            {{-- Attendance rate circle --}}
            <div style="text-align:center;">
                @php
                    $rate = $totalMembers > 0
                        ? round((($totalMembers - $absentees->total()) / $totalMembers) * 100)
                        : 0;
                @endphp
                <p style="font-size:32px;font-weight:800;color:{{ $rate >= 70 ? '#16a34a' : ($rate >= 40 ? '#d97706' : '#dc2626') }};">
                    {{ $rate }}%
                </p>
                <p style="font-size:12px;color:#9ca3af;">attendance rate</p>
            </div>

            {{-- Export buttons --}}
            <div style="display:flex;gap:8px;">
                <a href="{{ route('admin.reports.export.absentees.excel', ['event_id' => $selectedEvent->id]) }}"
                   style="background:#16a34a;color:white;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;display:flex;align-items:center;gap:6px;">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Excel
                </a>
                <a href="{{ route('admin.reports.export.absentees.pdf', ['event_id' => $selectedEvent->id]) }}"
                   style="background:#dc2626;color:white;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;display:flex;align-items:center;gap:6px;">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    PDF
                </a>
            </div>
        </div>

        {{-- Absentees table --}}
        <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;">
            <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
                <h3 style="font-size:14px;font-weight:600;color:#111827;">Absent members</h3>
                <span style="font-size:12px;color:#9ca3af;">{{ $absentees->total() }} members</span>
            </div>

            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <thead style="background:#fef2f2;">
                <tr>
                    <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">#</th>
                    <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Member</th>
                    <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Member ID</th>
                    <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Phone</th>
                    <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Total Attendance</th>
                    <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Last Seen</th>
                </tr>
                </thead>
                <tbody>
                @forelse($absentees as $i => $member)
                    @php
                        $lastAttendance = $member->attendance()->latest('checked_in_at')->first();
                        $totalAtt       = $member->attendance()->count();
                    @endphp
                    <tr style="border-top:1px solid #f3f4f6;"
                        onmouseenter="this.style.background='#fef2f2'"
                        onmouseleave="this.style.background=''">
                        <td style="padding:12px 20px;color:#9ca3af;font-size:13px;">
                            {{ ($absentees->currentPage() - 1) * $absentees->perPage() + $i + 1 }}
                        </td>
                        <td style="padding:12px 20px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:32px;height:32px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;color:#dc2626;font-size:11px;font-weight:600;flex-shrink:0;">
                                    {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
                                </div>
                                <div>
                                    <p style="font-weight:500;color:#111827;">{{ $member->full_name }}</p>
                                    <p style="font-size:12px;color:#9ca3af;">{{ $member->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td style="padding:12px 20px;font-family:monospace;font-size:13px;color:#6b7280;">
                            {{ $member->member_id_card }}
                        </td>
                        <td style="padding:12px 20px;color:#6b7280;font-size:13px;">
                            {{ $member->phone ?? '—' }}
                        </td>
                        <td style="padding:12px 20px;">
                        <span style="font-size:16px;font-weight:700;color:{{ $totalAtt === 0 ? '#dc2626' : '#374151' }};">
                            {{ $totalAtt }}
                        </span>
                            @if($totalAtt === 0)
                                <span style="font-size:11px;color:#dc2626;margin-left:4px;">never</span>
                            @endif
                        </td>
                        <td style="padding:12px 20px;font-size:13px;color:#6b7280;">
                            @if($lastAttendance)
                                {{ $lastAttendance->checked_in_at->format('d M Y') }}
                                <span style="font-size:11px;color:#9ca3af;display:block;">
                                {{ $lastAttendance->checked_in_at->diffForHumans() }}
                            </span>
                            @else
                                <span style="color:#dc2626;">Never attended</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"
                            style="padding:48px 20px;text-align:center;font-size:14px;">
                            <div style="color:#16a34a;font-size:32px;margin-bottom:8px;">🎉</div>
                            <p style="font-weight:600;color:#15803d;margin-bottom:4px;">100% attendance!</p>
                            <p style="color:#9ca3af;">All active members attended this event.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($absentees->hasPages())
                <div style="padding:16px 20px;border-top:1px solid #f3f4f6;">
                    {{ $absentees->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    @else

        {{-- Empty state --}}
        <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:48px 20px;text-align:center;">
            <svg style="width:48px;height:48px;color:#d1d5db;margin:0 auto 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p style="font-size:15px;font-weight:500;color:#374151;margin-bottom:6px;">Select an event to view absentees</p>
            <p style="font-size:13px;color:#9ca3af;">Choose an event from the dropdown above to see which members were absent.</p>
        </div>

    @endif

@endsection
