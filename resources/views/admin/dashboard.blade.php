@extends('layouts.admin')
@section('page-title', 'Dashboard')
@section('content')
    <style>
        @media(min-width:640px){
            .stats-top{
                grid-template-columns:repeat(4,1fr) !important;
            }
        }
        @keyframes pulse{
            0%,100%{opacity:1}50%{opacity:0.5}
        }
    </style>

    {{-- ── Active event banner ─────────────────────────────── --}}
    @if($stats['active_event'])
        <div style="background:linear-gradient(135deg,#16a34a,#22c55e);border-radius:12px;padding:14px 20px;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="width:10px;height:10px;background:white;border-radius:50%;display:inline-block;animation:pulse 2s infinite;"></span>
                <div>
                    <p style="font-size:14px;font-weight:600;color:white;">{{ $stats['active_event']->title }} is live</p>
                    <p style="font-size:12px;color:rgba(255,255,255,0.8);">
                        {{ $stats['active_event']->event_date->format('D, d M Y') }} ·
                        {{ \Carbon\Carbon::parse($stats['active_event']->start_time)->format('h:i A') }}
                    </p>
                </div>
            </div>
            <div style="display:flex;gap:8px;">
                <a href="{{ route('admin.checkin.index') }}"
                   style="background:white;color:#16a34a;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                    Open Check-in
                </a>
                <a href="{{ route('admin.events.show', $stats['active_event']) }}"
                   style="background:rgba(255,255,255,0.2);color:white;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">
                    View Event
                </a>
            </div>
        </div>
    @endif

    {{-- ── Top stats ────────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:1.5rem;">

        <div class="stats-top" style="display:contents;">

            <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:18px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                    <p style="font-size:13px;color:#6b7280;">Active members</p>
                    <div style="width:34px;height:34px;background:#dbeafe;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:16px;height:16px;color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                <p style="font-size:30px;font-weight:800;color:#111827;">{{ number_format($stats['active_members']) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">{{ $stats['total_members'] }} total registered</p>
            </div>

            <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:18px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                    <p style="font-size:13px;color:#6b7280;">Check-ins this month</p>
                    <div style="width:34px;height:34px;background:#dcfce7;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:16px;height:16px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p style="font-size:30px;font-weight:800;color:#111827;">{{ number_format($stats['checkins_this_month']) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">{{ $stats['checkins_today'] }} today · {{ $stats['checkins_this_week'] }} this week</p>
            </div>

            <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:18px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                    <p style="font-size:13px;color:#6b7280;">Visitors this month</p>
                    <div style="width:34px;height:34px;background:#fef3c7;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:16px;height:16px;color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                </div>
                <p style="font-size:30px;font-weight:800;color:#111827;">{{ number_format($stats['visitors_this_month']) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">{{ $stats['total_visitors'] }} all time</p>
            </div>

            <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:18px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                    <p style="font-size:13px;color:#6b7280;">Flagged absentees</p>
                    <div style="width:34px;height:34px;background:#fee2e2;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:16px;height:16px;color:#dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
                <p style="font-size:30px;font-weight:800;color:#111827;">{{ number_format($stats['flagged_absentees']) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">
                    <a href="{{ route('admin.absentees.index') }}" style="color:#dc2626;text-decoration:none;">View follow-up list →</a>
                </p>
            </div>

        </div>
    </div>

    {{-- ── Main chart: Attendance trend ───────────────────────── --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;margin-bottom:1.5rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
            <div>
                <h3 style="font-size:15px;font-weight:600;color:#111827;">Attendance & Growth Trend</h3>
                <p style="font-size:12px;color:#9ca3af;margin-top:2px;">Last 12 months</p>
            </div>
            <div style="display:flex;gap:16px;font-size:12px;">
            <span style="display:flex;align-items:center;gap:5px;">
                <span style="width:12px;height:3px;background:#2563eb;border-radius:2px;display:inline-block;"></span>
                Attendance
            </span>
                <span style="display:flex;align-items:center;gap:5px;">
                <span style="width:12px;height:3px;background:#16a34a;border-radius:2px;display:inline-block;"></span>
                New members
            </span>
                <span style="display:flex;align-items:center;gap:5px;">
                <span style="width:12px;height:3px;background:#d97706;border-radius:2px;display:inline-block;"></span>
                Visitors
            </span>
            </div>
        </div>
        <div style="position:relative;height:280px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    {{-- ── Row 2: Pie charts ───────────────────────────────────── --}}
    <div style="display:grid;gap:16px;margin-bottom:1.5rem;">
        <style>@media(min-width:768px){.pie-row{grid-template-columns:repeat(3,1fr) !important;}}</style>
        <div style="display:grid;gap:16px;" class="pie-row">

            {{-- Check-in methods --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;">
                <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:16px;">Check-in methods</h3>
                <div style="position:relative;height:180px;">
                    <canvas id="methodsChart"></canvas>
                </div>
            </div>

            {{-- Gender breakdown --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;">
                <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:16px;">Gender breakdown</h3>
                <div style="position:relative;height:180px;">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>

            {{-- Attendance by event type --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;">
                <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:16px;">Attendance by event type</h3>
                <div style="position:relative;height:180px;">
                    <canvas id="eventTypeChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Row 3: Weekly heatmap bar chart ─────────────────────── --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;margin-bottom:1.5rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <div>
                <h3 style="font-size:15px;font-weight:600;color:#111827;">Weekly attendance</h3>
                <p style="font-size:12px;color:#9ca3af;margin-top:2px;">Last 8 weeks</p>
            </div>
        </div>
        <div style="position:relative;height:200px;">
            <canvas id="weeklyChart"></canvas>
        </div>
    </div>

    {{-- ── Row 4: Tables ───────────────────────────────────────── --}}
    <div style="display:grid;gap:16px;margin-bottom:1.5rem;">
        <style>@media(min-width:1024px){.table-row{grid-template-columns:1fr 1fr !important;}}</style>
        <div style="display:grid;gap:16px;" class="table-row">

            {{-- Top events --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;">Top events by attendance</h3>
                </div>
                @foreach($topEvents as $i => $event)
                    <div style="padding:12px 16px;display:flex;align-items:center;gap:12px;border-bottom:1px solid #f9fafb;"
                         onmouseenter="this.style.background='#f9fafb'"
                         onmouseleave="this.style.background=''">
            <span style="width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;
                background:{{ $i === 0 ? '#fef3c7' : ($i === 1 ? '#f3f4f6' : ($i === 2 ? '#fef3c7' : '#f9fafb')) }};
                color:{{ $i === 0 ? '#d97706' : '#6b7280' }};">
                {{ $i + 1 }}
            </span>
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:13px;font-weight:500;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $event->title }}
                            </p>
                            <p style="font-size:11px;color:#9ca3af;">{{ $event->event_date->format('d M Y') }}</p>
                        </div>
                        <div style="text-align:right;flex-shrink:0;">
                            <span style="font-size:18px;font-weight:800;color:#2563eb;">{{ $event->attendance_count }}</span>
                            <p style="font-size:11px;color:#9ca3af;">checked in</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Top members --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;">Most faithful members</h3>
                </div>
                @foreach($topMembers as $i => $member)
                    <div style="padding:12px 16px;display:flex;align-items:center;gap:12px;border-bottom:1px solid #f9fafb;"
                         onmouseenter="this.style.background='#f9fafb'"
                         onmouseleave="this.style.background=''">
            <span style="width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;
                background:{{ $i === 0 ? '#fef3c7' : '#f3f4f6' }};
                color:{{ $i === 0 ? '#d97706' : '#6b7280' }};">
                {{ $i + 1 }}
            </span>
                        <div style="width:30px;height:30px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:11px;font-weight:600;flex-shrink:0;">
                            {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:13px;font-weight:500;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $member->full_name }}
                            </p>
                            <p style="font-size:11px;color:#9ca3af;">{{ $member->member_id_card }}</p>
                        </div>
                        <div style="text-align:right;flex-shrink:0;">
                            <span style="font-size:18px;font-weight:800;color:#2563eb;">{{ $member->attendance_count }}</span>
                            <p style="font-size:11px;color:#9ca3af;">services</p>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    {{-- ── Row 5: Recent check-ins + Upcoming events ───────────── --}}
    <div style="display:grid;gap:16px;">
        <style>@media(min-width:1024px){.bottom-row{grid-template-columns:1fr 1fr !important;}}</style>
        <div style="display:grid;gap:16px;" class="bottom-row">

            {{-- Recent check-ins --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;">Recent check-ins</h3>
                    <a href="{{ route('admin.checkin.index') }}" style="font-size:12px;color:#2563eb;text-decoration:none;">Go to check-in →</a>
                </div>
                @forelse($recentCheckins as $checkin)
                    <div style="padding:11px 16px;display:flex;align-items:center;gap:10px;border-bottom:1px solid #f9fafb;"
                         onmouseenter="this.style.background='#f9fafb'"
                         onmouseleave="this.style.background=''">
                        <div style="width:32px;height:32px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:11px;font-weight:600;flex-shrink:0;">
                            {{ strtoupper(substr($checkin->member->first_name,0,1).substr($checkin->member->last_name,0,1)) }}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:13px;font-weight:500;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $checkin->member->full_name }}
                            </p>
                            <p style="font-size:11px;color:#9ca3af;">{{ $checkin->event->title }}</p>
                        </div>
                        <p style="font-size:11px;color:#9ca3af;flex-shrink:0;">{{ $checkin->checked_in_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <div style="padding:32px;text-align:center;color:#9ca3af;font-size:14px;">No check-ins yet today.</div>
                @endforelse
            </div>

            {{-- Upcoming events --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;">Upcoming events</h3>
                    <a href="{{ route('admin.events.index') }}" style="font-size:12px;color:#2563eb;text-decoration:none;">View all →</a>
                </div>
                @forelse($nextEvents as $event)
                    <div style="padding:12px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f9fafb;"
                         onmouseenter="this.style.background='#f9fafb'"
                         onmouseleave="this.style.background=''">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="text-align:center;min-width:36px;">
                                <p style="font-size:18px;font-weight:800;color:#2563eb;line-height:1;">{{ $event->event_date->format('d') }}</p>
                                <p style="font-size:10px;color:#9ca3af;text-transform:uppercase;">{{ $event->event_date->format('M') }}</p>
                            </div>
                            <div>
                                <p style="font-size:13px;font-weight:500;color:#111827;">{{ $event->title }}</p>
                                <p style="font-size:11px;color:#9ca3af;">{{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}</p>
                            </div>
                        </div>
                        <span style="font-size:11px;padding:3px 10px;border-radius:20px;font-weight:500;
                {{ $event->status === 'active'   ? 'background:#dcfce7;color:#15803d;' : 'background:#fef3c7;color:#d97706;' }}">
                {{ ucfirst($event->status) }}
            </span>
                    </div>
                @empty
                    <div style="padding:32px;text-align:center;color:#9ca3af;font-size:14px;">No upcoming events.</div>
                @endforelse
            </div>

        </div>
    </div>

    {{-- ── Chart.js ─────────────────────────────────────────────── --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const months   = @json($monthlyAttendance->pluck('month'));
        const attData  = @json($monthlyAttendance->pluck('count'));
        const memData  = @json($monthlyMembers->pluck('count'));
        const visData  = @json($monthlyVisitors->pluck('count'));

        const methodLabels = @json($checkinMethods->pluck('label'));
        const methodCounts = @json($checkinMethods->pluck('count'));

        const genderLabels = @json($genderBreakdown->pluck('label'));
        const genderCounts = @json($genderBreakdown->pluck('count'));

        const typeLabels = @json($attendanceByType->pluck('label'));
        const typeCounts = @json($attendanceByType->pluck('count'));

        const weekLabels = @json($weeklyHeatmap->pluck('week'));
        const weekCounts = @json($weeklyHeatmap->pluck('count'));

        const palette = ['#2563eb','#16a34a','#d97706','#dc2626','#7c3aed','#0891b2'];

        Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, Segoe UI, sans-serif';
        Chart.defaults.font.size   = 12;
        Chart.defaults.color       = '#6b7280';

        // ── Trend chart ──────────────────────────────────────────
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Attendance',
                        data: attData,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.06)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#2563eb',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.4,
                        fill: true,
                    },
                    {
                        label: 'New members',
                        data: memData,
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22,163,74,0.06)',
                        borderWidth: 2,
                        pointBackgroundColor: '#16a34a',
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0.4,
                        fill: true,
                    },
                    {
                        label: 'Visitors',
                        data: visData,
                        borderColor: '#d97706',
                        backgroundColor: 'rgba(217,119,6,0.06)',
                        borderWidth: 2,
                        pointBackgroundColor: '#d97706',
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0.4,
                        fill: true,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { weight: '600' },
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        border: { display: false },
                        ticks: { stepSize: 1 },
                    }
                }
            }
        });

        // ── Check-in methods doughnut ─────────────────────────────
        new Chart(document.getElementById('methodsChart'), {
            type: 'doughnut',
            data: {
                labels: methodLabels,
                datasets: [{
                    data: methodCounts,
                    backgroundColor: palette,
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 12, usePointStyle: true, pointStyleWidth: 8 }
                    },
                    tooltip: {
                        backgroundColor: '#1f2937', padding: 10, cornerRadius: 8,
                    }
                }
            }
        });

        // ── Gender doughnut ────────────────────────────────────────
        new Chart(document.getElementById('genderChart'), {
            type: 'doughnut',
            data: {
                labels: genderLabels,
                datasets: [{
                    data: genderCounts,
                    backgroundColor: ['#2563eb','#ec4899','#9ca3af'],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 12, usePointStyle: true, pointStyleWidth: 8 }
                    },
                    tooltip: {
                        backgroundColor: '#1f2937', padding: 10, cornerRadius: 8,
                    }
                }
            }
        });

        // ── Event type doughnut ────────────────────────────────────
        new Chart(document.getElementById('eventTypeChart'), {
            type: 'doughnut',
            data: {
                labels: typeLabels,
                datasets: [{
                    data: typeCounts,
                    backgroundColor: ['#2563eb','#7c3aed','#d97706'],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 12, usePointStyle: true, pointStyleWidth: 8 }
                    },
                    tooltip: {
                        backgroundColor: '#1f2937', padding: 10, cornerRadius: 8,
                    }
                }
            }
        });

        // ── Weekly bar chart ───────────────────────────────────────
        new Chart(document.getElementById('weeklyChart'), {
            type: 'bar',
            data: {
                labels: weekLabels,
                datasets: [{
                    label: 'Check-ins',
                    data: weekCounts,
                    backgroundColor: weekCounts.map((v, i) =>
                        i === weekCounts.length - 1
                            ? '#2563eb'
                            : 'rgba(37,99,235,0.15)'
                    ),
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937', padding: 10, cornerRadius: 8,
                        callbacks: {
                            title: ctx => 'Week of ' + ctx[0].label,
                            label: ctx => ctx.parsed.y + ' check-ins',
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        border: { display: false },
                        ticks: { stepSize: 1 },
                    }
                }
            }
        });
    </script>

@endsection
