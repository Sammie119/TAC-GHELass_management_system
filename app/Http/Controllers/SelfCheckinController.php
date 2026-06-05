<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Member;
use Illuminate\Http\Request;

class SelfCheckinController extends Controller
{
    // Public check-in landing page (via event QR)
    public function show(string $token)
    {
        $event = Event::where('qr_token', $token)->firstOrFail();

        if ($event->status !== 'active') {
            return view('admin.checkin.unavailable', compact('event'));
        }

        return view('admin.checkin.show', compact('event'));
    }

    // Lookup member by ID card, phone, or email
    public function lookup(Request $request, string $token)
    {
        $event = Event::where('qr_token', $token)->firstOrFail();

        if ($event->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This event is no longer accepting check-ins.',
            ], 422);
        }

        $request->validate([
            'identifier' => 'required|string|min:2',
        ]);

        $identifier = trim($request->identifier);

        $member = Member::where('status', 'active')
            ->where(function ($q) use ($identifier) {
                $q->where('member_id_card', $identifier)
                    ->orWhere('phone', $identifier)
                    ->orWhere('tacms_number', $identifier);
            })
            ->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'No active member found with that ID, phone, or email.',
            ], 404);
        }

        // Check if already checked in
        $alreadyCheckedIn = Attendance::where('member_id', $member->id)
            ->where('event_id', $event->id)
            ->exists();

        return response()->json([
            'success'           => true,
            'already_checked_in' => $alreadyCheckedIn,
            'member' => [
                'id'             => $member->id,
                'name'           => $member->full_name,
                'member_id_card' => $member->member_id_card,
                'initials'       => strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)),
            ],
        ]);
    }

    // Confirm and complete self check-in
    public function checkin(Request $request, string $token)
    {
        $event = Event::where('qr_token', $token)->firstOrFail();

        if ($event->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This event is no longer accepting check-ins.',
            ], 422);
        }

        $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        $member = Member::findOrFail($request->member_id);

        // Prevent duplicate
        $existing = Attendance::where('member_id', $member->id)
            ->where('event_id', $event->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => "You are already checked in at {$existing->checked_in_at->format('h:i A')}.",
            ], 422);
        }

        Attendance::create([
            'member_id'      => $member->id,
            'event_id'       => $event->id,
            'checkin_method' => 'member_id',
            'checked_in_by'  => null,
            'checked_in_at'  => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Welcome, {$member->first_name}! You are checked in.",
            'time'    => now()->format('h:i A'),
            'member'  => [
                'name'     => $member->full_name,
                'initials' => strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)),
            ],
        ]);
    }
}
