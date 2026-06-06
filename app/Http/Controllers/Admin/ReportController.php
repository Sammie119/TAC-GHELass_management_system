<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\AttendanceExport;
use App\Exports\MembersExport;
use App\Exports\VisitorsExport;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Member;
use App\Models\Visitor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\NewSoul;
use App\Models\SoulFollowup;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::orderBy('event_date', 'desc')->take(50)->get();

        // Summary stats
        $stats = [
            'total_members'      => Member::where('status', 'active')->count(),
            'total_events'       => Event::count(),
            'total_checkins'     => Attendance::count(),
            'total_visitors'     => Visitor::count(),
            'this_month_checkins'=> Attendance::whereMonth('checked_in_at', now()->month)
                ->whereYear('checked_in_at', now()->year)->count(),
            'this_month_visitors'=> Visitor::whereMonth('visited_at', now()->month)
                ->whereYear('visited_at', now()->year)->count(),
        ];

        // Attendance per event (last 10 events)
        $eventAttendance = Event::withCount('attendance')
            ->orderBy('event_date', 'desc')
            ->take(10)->get();

        // Monthly attendance trend (last 6 months)
        $monthlyTrend = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'month' => $date->format('M Y'),
                'count' => Attendance::whereMonth('checked_in_at', $date->month)
                    ->whereYear('checked_in_at', $date->year)->count(),
            ];
        });

        // Top attending members
        $topMembers = Member::withCount('attendance')
            ->where('status', 'active')
            ->orderBy('attendance_count', 'desc')
            ->take(10)->get();

        // Check-in methods breakdown
        $methods = Attendance::selectRaw('checkin_method, COUNT(*) as total')
            ->groupBy('checkin_method')->get();

        return view('admin.reports.index', compact(
            'stats', 'events', 'eventAttendance',
            'monthlyTrend', 'topMembers', 'methods'
        ));
    }

    // ── Excel Exports ──────────────────────────────────────

    public function exportAttendanceExcel(Request $request)
    {
        $filename = 'attendance-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new AttendanceExport(
                $request->event_id,
                $request->from,
                $request->to
            ),
            $filename
        );
    }

    public function exportMembersExcel()
    {
        return Excel::download(
            new MembersExport(),
            'members-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportVisitorsExcel(Request $request)
    {
        return Excel::download(
            new VisitorsExport(
                $request->event_id,
                $request->from,
                $request->to
            ),
            'visitors-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    // ── PDF Exports ────────────────────────────────────────

    public function exportAttendancePdf(Request $request)
    {
        $query = Attendance::with(['member', 'event']);

        if ($request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->from) {
            $query->whereDate('checked_in_at', '>=', $request->from);
        }

        if ($request->to) {
            $query->whereDate('checked_in_at', '<=', $request->to);
        }

        $records  = $query->latest('checked_in_at')->get();
        $event    = $request->event_id ? Event::find($request->event_id) : null;

        $pdf = Pdf::loadView('admin.reports.pdf.attendance', compact('records', 'event'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('attendance-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportMembersPdf()
    {
        $members = Member::withCount('attendance')
            ->where('status', 'active')
            ->orderBy('last_name')->get();

        $pdf = Pdf::loadView('admin.reports.pdf.members', compact('members'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('members-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportEventPdf(Event $event)
    {
        $attendance = $event->attendance()->with('member')->latest('checked_in_at')->get();

        $pdf = Pdf::loadView('admin.reports.pdf.event', compact('event', 'attendance'))
            ->setPaper('a4', 'portrait');

        return $pdf->download(
            str_replace(' ', '-', strtolower($event->title)) . '-attendance.pdf'
        );
    }

    public function exportVisitorsPdf(Request $request)
    {
        $query = Visitor::with(['event', 'recordedBy']);

        if ($request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->from) {
            $query->whereDate('visited_at', '>=', $request->from);
        }

        if ($request->to) {
            $query->whereDate('visited_at', '<=', $request->to);
        }

        $visitors = $query->latest('visited_at')->get();
        $event    = $request->event_id ? Event::find($request->event_id) : null;

        $pdf = Pdf::loadView('admin.reports.pdf.visitors', compact('visitors', 'event'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('visitors-' . now()->format('Y-m-d') . '.pdf');
    }

    public function absentees(Request $request)
    {
        $events = Event::orderBy('event_date', 'desc')->take(50)->get();
        $selectedEvent = $request->event_id ? Event::find($request->event_id) : null;

        $absentees = collect();
        $totalMembers = Member::where('status', 'active')->count();

        if ($selectedEvent) {
            // Get member IDs who attended this event
            $attendedIds = Attendance::where('event_id', $selectedEvent->id)
                ->pluck('member_id');

            // Get members who did NOT attend
            $absentees = Member::where('status', 'active')
                ->whereNotIn('id', $attendedIds)
                ->orderBy('last_name')
                ->paginate(20)
                ->withQueryString();
        }

        return view('admin.reports.absentees', compact(
            'events', 'selectedEvent', 'absentees', 'totalMembers'
        ));
    }

    public function exportAbsenteesExcel(Request $request)
    {
        $request->validate(['event_id' => 'required|exists:events,id']);

        $event       = Event::findOrFail($request->event_id);
        $attendedIds = Attendance::where('event_id', $event->id)->pluck('member_id');
        $absentees   = Member::where('status', 'active')
            ->whereNotIn('id', $attendedIds)
            ->orderBy('last_name')->get();

        return Excel::download(
            new \App\Exports\AbsenteesExport($absentees, $event),
            'absentees-' . $event->event_date->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportAbsenteesPdf(Request $request)
    {
        $request->validate(['event_id' => 'required|exists:events,id']);

        $event       = Event::findOrFail($request->event_id);
        $attendedIds = Attendance::where('event_id', $event->id)->pluck('member_id');
        $absentees   = Member::where('status', 'active')
            ->whereNotIn('id', $attendedIds)
            ->orderBy('last_name')->get();

        $pdf = Pdf::loadView('admin.reports.pdf.absentees', compact('absentees', 'event'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('absentees-' . $event->event_date->format('Y-m-d') . '.pdf');
    }

    // ── New Souls Report ──────────────────────────────────────
    public function soulsReport(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $souls = NewSoul::with(['wonBy', 'assignedTo', 'followups'])
            ->whereBetween('date_won', [$from, $to])
            ->latest('date_won')
            ->get();

        $stats = [
            'total'       => $souls->count(),
            'new'         => $souls->where('status', 'new')->count(),
            'contacted'   => $souls->where('status', 'contacted')->count(),
            'attending'   => $souls->where('status', 'attending')->count(),
            'baptised'    => $souls->where('status', 'baptised')->count(),
            'converted'   => $souls->where('status', 'converted')->count(),
            'backslidden' => $souls->where('status', 'backslidden')->count(),
            'followups'   => $souls->sum(fn($s) => $s->followups->count()),
        ];

        // Group by won_by member
        $byWinner = $souls->whereNotNull('won_by')
            ->groupBy('won_by')
            ->map(fn($group) => [
                'name'  => $group->first()->wonBy?->full_name ?? 'Unknown',
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->values();

        // Group by area
        $byArea = $souls->whereNotNull('area')
            ->groupBy('area')
            ->map(fn($group) => [
                'area'  => $group->first()->area,
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->values();

        return view('admin.reports.souls', compact(
            'souls', 'stats', 'byWinner', 'byArea', 'from', 'to'
        ));
    }

    // ── Export Souls PDF ──────────────────────────────────────
    public function exportSoulsPdf(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $souls = NewSoul::with(['wonBy', 'assignedTo', 'followups'])
            ->whereBetween('date_won', [$from, $to])
            ->latest('date_won')->get();

        $stats = [
            'total'       => $souls->count(),
            'new'         => $souls->where('status', 'new')->count(),
            'contacted'   => $souls->where('status', 'contacted')->count(),
            'attending'   => $souls->where('status', 'attending')->count(),
            'baptised'    => $souls->where('status', 'baptised')->count(),
            'converted'   => $souls->where('status', 'converted')->count(),
            'backslidden' => $souls->where('status', 'backslidden')->count(),
        ];

        $byWinner = $souls->whereNotNull('won_by')
            ->groupBy('won_by')
            ->map(fn($g) => [
                'name'  => $g->first()->wonBy?->full_name ?? 'Unknown',
                'count' => $g->count(),
            ])
            ->sortByDesc('count')->values();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'admin.reports.pdf.souls',
            compact('souls', 'stats', 'byWinner', 'from', 'to')
        )->setPaper('a4', 'portrait');

        return $pdf->download("souls-report-{$from}-to-{$to}.pdf");
    }

    // ── Export Souls Excel ────────────────────────────────────
    public function exportSoulsExcel(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SoulsExport($from, $to),
            "souls-report-{$from}-to-{$to}.xlsx"
        );
    }
}
