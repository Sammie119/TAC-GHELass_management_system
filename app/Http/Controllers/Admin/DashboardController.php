<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Member;
use App\Models\Visitor;
use App\Models\AbsenteeFlag;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Summary stats ──────────────────────────────────
        $stats = [
            'total_members'       => Member::count(),
            'active_members'      => Member::where('status', 'active')->count(),
            'total_events'        => Event::count(),
            'total_checkins'      => Attendance::count(),
            'checkins_today'      => Attendance::whereDate('checked_in_at', today())->count(),
            'checkins_this_week'  => Attendance::whereBetween('checked_in_at', [
                now()->startOfWeek(), now()->endOfWeek()
            ])->count(),
            'checkins_this_month' => Attendance::whereMonth('checked_in_at', now()->month)
                ->whereYear('checked_in_at', now()->year)->count(),
            'total_visitors'      => Visitor::count(),
            'visitors_this_month' => Visitor::whereMonth('visited_at', now()->month)
                ->whereYear('visited_at', now()->year)->count(),
            'flagged_absentees'   => AbsenteeFlag::where('status', 'flagged')->count(),
            'active_event'        => Event::where('status', 'active')->latest()->first(),
        ];

        // ── Monthly attendance trend (last 12 months) ──────
        $monthlyAttendance = collect(range(11, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'month' => $date->format('M Y'),
                'count' => Attendance::whereMonth('checked_in_at', $date->month)
                    ->whereYear('checked_in_at', $date->year)->count(),
            ];
        });

        // ── Monthly new members (last 12 months) ───────────
        $monthlyMembers = collect(range(11, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'month' => $date->format('M Y'),
                'count' => Member::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)->count(),
            ];
        });

        // ── Monthly visitors (last 12 months) ──────────────
        $monthlyVisitors = collect(range(11, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'month' => $date->format('M Y'),
                'count' => Visitor::whereMonth('visited_at', $date->month)
                    ->whereYear('visited_at', $date->year)->count(),
            ];
        });

        // ── Check-in methods breakdown ─────────────────────
        $checkinMethods = Attendance::selectRaw('checkin_method, COUNT(*) as total')
            ->groupBy('checkin_method')
            ->get()
            ->map(fn($r) => [
                'label' => ucwords(str_replace('_', ' ', $r->checkin_method)),
                'count' => $r->total,
            ]);

        // ── Event type breakdown ───────────────────────────
        $eventTypes = Event::selectRaw('type, COUNT(*) as total')
            ->groupBy('type')->get()
            ->map(fn($r) => [
                'label' => ucfirst($r->type),
                'count' => $r->total,
            ]);

        // ── Attendance by event type ───────────────────────
        $attendanceByType = Attendance::join('events', 'attendance.event_id', '=', 'events.id')
            ->selectRaw('events.type, COUNT(attendance.id) as total')
            ->groupBy('events.type')
            ->get()
            ->map(fn($r) => [
                'label' => ucfirst($r->type),
                'count' => $r->total,
            ]);

        // ── Top 5 events by attendance ─────────────────────
        $topEvents = Event::withCount('attendance')
            ->orderBy('attendance_count', 'desc')
            ->take(5)->get();

        // ── Top 5 members by attendance ────────────────────
        $topMembers = Member::withCount('attendance')
            ->where('status', 'active')
            ->orderBy('attendance_count', 'desc')
            ->take(5)->get();

        // ── Gender breakdown ───────────────────────────────
        $genderBreakdown = Member::where('status', 'active')
            ->selectRaw('gender, COUNT(*) as total')
            ->groupBy('gender')->get()
            ->map(fn($r) => [
                'label' => ucfirst($r->gender ?? 'Unknown'),
                'count' => $r->total,
            ]);

        // ── Weekly attendance heatmap (last 8 weeks) ───────
        $weeklyHeatmap = collect(range(7, 0))->map(function ($i) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end   = now()->subWeeks($i)->endOfWeek();
            return [
                'week'  => $start->format('d M'),
                'count' => Attendance::whereBetween('checked_in_at', [$start, $end])->count(),
            ];
        });

        // ── Recent check-ins ───────────────────────────────
        $recentCheckins = Attendance::with(['member', 'event'])
            ->latest('checked_in_at')->take(6)->get();

        // ── Upcoming events ────────────────────────────────
        $nextEvents = Event::where('event_date', '>=', today())
            ->orderBy('event_date')->take(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'monthlyAttendance',
            'monthlyMembers',
            'monthlyVisitors',
            'checkinMethods',
            'eventTypes',
            'attendanceByType',
            'topEvents',
            'topMembers',
            'genderBreakdown',
            'weeklyHeatmap',
            'recentCheckins',
            'nextEvents'
        ));
    }
}
