<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceHeadcountExport;
use App\Http\Controllers\Controller;
use App\Models\AttendanceHeadcount;
use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?? today()->toDateString();

        $events = Event::with('headcount')
            ->whereDate('event_date', $date)
            ->orderBy('start_time')
            ->get()
            ->map(function ($event) {
                $event->digital_count = $event->attendance()->count();
                $event->manual_count = $event->headcount ? $event->headcount->total : 0;

                return $event;
            });

        $dayTotals = [
            'digital' => $events->sum('digital_count'),
            'manual' => $events->sum('manual_count'),
        ];

        $categoryTotals = [
            'male' => $events->sum(fn ($e) => $e->headcount->male ?? 0),
            'female' => $events->sum(fn ($e) => $e->headcount->female ?? 0),
            'children' => $events->sum(fn ($e) => $e->headcount->children ?? 0),
            'youth' => $events->sum(fn ($e) => $e->headcount->youth ?? 0),
            'visitors' => $events->sum(fn ($e) => $e->headcount->visitors ?? 0),
        ];

        $allEvents = Event::orderByDesc('event_date')->get(['id', 'title', 'event_date']);

        return view('admin.attendance.index', compact('date', 'events', 'dayTotals', 'categoryTotals', 'allEvents'));
    }

    public function history(Request $request)
    {
        [$from, $to] = $this->historyRange($request);

        $events = $this->historyQuery($from, $to)
            ->paginate(20)
            ->withQueryString()
            ->through(function ($event) {
                $event->digital_count = $event->attendance_count;
                $event->manual_count = $event->headcount ? $event->headcount->total : 0;

                return $event;
            });

        return view('admin.attendance.history', compact('events', 'from', 'to'));
    }

    public function exportHeadcountExcel(Request $request)
    {
        [$from, $to] = $this->historyRange($request);

        $rows = $this->historyQuery($from, $to)->get()->map(function ($event) {
            $h = $event->headcount;

            return [
                $event->title,
                $event->event_date->format('d M Y'),
                $event->attendance_count,
                $h->male ?? 0,
                $h->female ?? 0,
                $h->children ?? 0,
                $h->youth ?? 0,
                $h->visitors ?? 0,
                $h ? $h->total : 0,
            ];
        })->toArray();

        return Excel::download(
            new AttendanceHeadcountExport($rows),
            'attendance-headcount-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function exportHeadcountPdf(Request $request)
    {
        [$from, $to] = $this->historyRange($request);

        $events = $this->historyQuery($from, $to)->get()->map(function ($event) {
            $event->digital_count = $event->attendance_count;
            $event->manual_count = $event->headcount ? $event->headcount->total : 0;

            return $event;
        });

        $pdf = Pdf::loadView('admin.attendance.pdf.headcount-report', compact('events', 'from', 'to'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('attendance-headcount-'.now()->format('Y-m-d').'.pdf');
    }

    private function historyRange(Request $request): array
    {
        return [
            $request->from ?? now()->subDays(90)->toDateString(),
            $request->to ?? now()->toDateString(),
        ];
    }

    private function historyQuery(string $from, string $to)
    {
        return Event::with('headcount')
            ->withCount('attendance')
            ->whereDate('event_date', '>=', $from)
            ->whereDate('event_date', '<=', $to)
            ->orderByDesc('event_date');
    }

    public function storeHeadcount(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'male' => 'nullable|integer|min:0',
            'female' => 'nullable|integer|min:0',
            'children' => 'nullable|integer|min:0',
            'youth' => 'nullable|integer|min:0',
            'visitors' => 'nullable|integer|min:0',
        ]);

        AttendanceHeadcount::updateOrCreate(
            ['event_id' => $validated['event_id']],
            [
                'male' => $validated['male'] ?? 0,
                'female' => $validated['female'] ?? 0,
                'children' => $validated['children'] ?? 0,
                'youth' => $validated['youth'] ?? 0,
                'visitors' => $validated['visitors'] ?? 0,
                'recorded_by' => auth()->id(),
            ]
        );

        return back()->with('success', 'Headcount saved.');
    }
}
