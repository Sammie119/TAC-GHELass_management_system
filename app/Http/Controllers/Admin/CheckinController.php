<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\CheckinLog;
use App\Models\Event;
use App\Models\Member;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    // Main check-in screen
    public function index()
    {
        $activeEvent  = Event::where('status', 'active')->latest()->first();
        $recentCheckins = [];

        if ($activeEvent) {
            $recentCheckins = Attendance::with('member')
                ->where('event_id', $activeEvent->id)
                ->latest('checked_in_at')
                ->take(10)
                ->get();
        }

        $upcomingEvents = Event::where('status', 'upcoming')
            ->orderBy('event_date')->take(5)->get();

        return view('admin.checkin.index', compact(
            'activeEvent', 'recentCheckins', 'upcomingEvents'
        ));
    }

    // Search members by name (AJAX)
    public function search(Request $request)
    {
        $request->validate(['query' => 'required|string|min:2']);

        $term = $request->input('query'); // use input() not ->query

        $members = Member::where('status', 'active')
            ->where(function ($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name',  'like', "%{$term}%")
                    ->orWhere('member_id_card', 'like', "%{$term}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]);
            })
            ->take(8)
            ->get(['id', 'first_name', 'last_name', 'member_id_card', 'photo']);

        return response()->json($members);
    }

    // Process check-in (handles all methods)
    public function checkin(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'event_id'  => 'required|exists:events,id',
            'method'    => 'required|in:qr_scan,name_search,member_id,usher_marked',
        ]);

        $member = Member::findOrFail($request->member_id);
        $event  = Event::findOrFail($request->event_id);

        // Check event is still active
        if ($event->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This event is not currently active for check-in.',
            ], 422);
        }

        // Prevent duplicate check-ins
        $existing = Attendance::where('member_id', $member->id)
            ->where('event_id', $event->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success'  => false,
                'message'  => "{$member->full_name} is already checked in.",
                'duplicate' => true,
                'time'     => $existing->checked_in_at->format('h:i A'),
            ], 422);
        }

        // Create attendance record
        $attendance = Attendance::create([
            'member_id'      => $member->id,
            'event_id'       => $event->id,
            'checkin_method' => $request->method,
            'checked_in_by'  => auth()->id(),
            'checked_in_at'  => now(),
        ]);

        // Log the check-in
        CheckinLog::create([
            'attendance_id' => $attendance->id,
            'usher_id'      => auth()->id(),
            'device_info'   => $request->userAgent(),
            'ip_address'    => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$member->full_name} checked in successfully!",
            'member'  => [
                'id'             => $member->id,      // ← add this line
                'name'           => $member->full_name,
                'member_id_card' => $member->member_id_card,
                'initials'       => strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)),
            ],
            'time'  => now()->format('h:i A'),
            'total' => Attendance::where('event_id', $event->id)->count(),
        ]);
    }

    // QR code lookup
    public function scanQr(Request $request)
    {
        $request->validate(['qr_code' => 'required|string']);

        $member = Member::where('qr_code', $request->qr_code)
            ->where('status', 'active')
            ->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'QR code not recognised or member is inactive.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'member'  => [
                'id'             => $member->id,
                'name'           => $member->full_name,
                'member_id_card' => $member->member_id_card,
                'initials'       => strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)),
            ],
        ]);
    }

    // Add this method to app/Http/Controllers/Admin/CheckinController.php

    public function removeCheckin(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'event_id'  => 'required|exists:events,id',
        ]);

        $attendance = Attendance::where('member_id', $request->member_id)
            ->where('event_id', $request->event_id)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No check-in record found for this member.',
            ], 404);
        }

        // Delete the log first (foreign key)
        $attendance->log()->delete();
        $attendance->delete();

        $member = Member::findOrFail($request->member_id);

        return response()->json([
            'success' => true,
            'message' => "{$member->full_name}'s check-in has been removed.",
            'total'   => Attendance::where('event_id', $request->event_id)->count(),
        ]);
    }
}
