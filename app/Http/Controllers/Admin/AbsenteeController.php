<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenteeFlag;
use App\Models\Event;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;

class AbsenteeController extends Controller
{
    public function index(Request $request)
    {
        $query = AbsenteeFlag::with(['member', 'assignedTo'])->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name',  'like', "%{$request->search}%")
                    ->orWhere('phone',      'like', "%{$request->search}%");
            });
        }

        $flags   = $query->orderBy('consecutive_absences', 'desc')
            ->paginate(20)->withQueryString();
        $users   = User::role('admin')->get();
        $summary = [
            'flagged'   => AbsenteeFlag::where('status', 'flagged')->count(),
            'contacted' => AbsenteeFlag::where('status', 'contacted')->count(),
            'resolved'  => AbsenteeFlag::where('status', 'resolved')->count(),
        ];

        return view('admin.absentees.index', compact('flags', 'users', 'summary'));
    }

    // Manually flag a member
    public function flag(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'notes'     => 'nullable|string|max:500',
        ]);

        $member     = Member::findOrFail($request->member_id);
        $lastAtt    = $member->attendance()->latest('checked_in_at')->first();

        AbsenteeFlag::updateOrCreate(
            ['member_id' => $member->id],
            [
                'consecutive_absences' => $request->absences ?? 0,
                'last_attended'        => $lastAtt?->checked_in_at->toDateString(),
                'flagged_on'           => now()->toDateString(),
                'status'               => 'flagged',
                'notes'                => $request->notes,
            ]
        );

        return back()->with('success', "{$member->full_name} has been flagged for follow-up.");
    }

    // Update follow-up status
    public function updateStatus(Request $request, AbsenteeFlag $flag)
    {
        $request->validate([
            'status'      => 'required|in:flagged,contacted,resolved',
            'notes'       => 'nullable|string|max:500',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $flag->update([
            'status'      => $request->status,
            'notes'       => $request->notes,
            'assigned_to' => $request->assigned_to,
            'resolved_at' => $request->status === 'resolved' ? now() : null,
        ]);

        return back()->with('success', 'Follow-up status updated.');
    }

    // Run flag scan manually
    public function runScan(Request $request)
    {
        $threshold = $request->threshold ?? 3;

        \Artisan::call('absentees:flag', ['--threshold' => $threshold]);

        $output = \Artisan::output();

        return back()->with('success', 'Scan complete. ' . trim($output));
    }

    // Remove a flag
    public function unflag(AbsenteeFlag $flag)
    {
        $name = $flag->member->full_name;
        $flag->delete();

        return back()->with('success', "{$name} has been removed from the follow-up list.");
    }
}
