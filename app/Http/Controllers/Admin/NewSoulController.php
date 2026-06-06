<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\NewSoul;
use App\Models\SoulFollowup;
use App\Models\User;
use Illuminate\Http\Request;

class NewSoulController extends Controller
{
    // ── Index ────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = NewSoul::with(['wonBy', 'assignedTo', 'followups']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name',  'like', "%{$search}%")
                    ->orWhere('phone',      'like', "%{$search}%")
                    ->orWhere('area',       'like', "%{$search}%");
            });
        }

        if ($request->filled('area')) {
            $query->where('area', 'like', "%{$request->area}%");
        }

        $souls = $query->latest('date_won')->paginate(20)->withQueryString();

        $stats = [
            'total'       => NewSoul::count(),
            'new'         => NewSoul::where('status', 'new')->count(),
            'contacted'   => NewSoul::where('status', 'contacted')->count(),
            'attending'   => NewSoul::where('status', 'attending')->count(),
            'baptised'    => NewSoul::where('status', 'baptised')->count(),
            'converted'   => NewSoul::where('status', 'converted')->count(),
            'backslidden' => NewSoul::where('status', 'backslidden')->count(),
        ];

        $members = Member::where('status', 'active')->orderBy('first_name')->get();
        $users   = User::orderBy('name')->get();

        return view('admin.souls.index', compact('souls', 'stats', 'members', 'users'));
    }

    // ── Store new soul ───────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'            => 'required|string|max:100',
            'last_name'             => 'required|string|max:100',
            'phone'                 => 'nullable|string|max:20',
            'email'                 => 'nullable|email|max:150',
            'address'               => 'nullable|string|max:255',
            'area'                  => 'nullable|string|max:100',
            'date_won'              => 'required|date',
            'won_by'                => 'nullable|exists:members,id',
            'assigned_to'           => 'nullable|exists:users,id',
            'church_background'     => 'nullable|string|max:150',
            'salvation_prayer_date' => 'nullable|date',
            'notes'                 => 'nullable|string',
        ]);

        $validated['status'] = 'new';

        NewSoul::create($validated);

        return back()->with('success', 'New soul recorded successfully.');
    }

    // ── Show detail ──────────────────────────────────────
    public function show(NewSoul $soul)
    {
        $soul->load(['wonBy', 'assignedTo', 'followups.user']);
        $users = User::orderBy('name')->get();

        return view('admin.souls.show', compact('soul', 'users'));
    }

    // ── Update status ────────────────────────────────────
    public function updateStatus(Request $request, NewSoul $soul)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,attending,baptised,converted,backslidden',
        ]);

        $soul->update([
            'status'      => $request->status,
            'assigned_to' => $request->assigned_to ?? $soul->assigned_to,
            'notes'       => $request->notes ?? $soul->notes,
        ]);

        return back()->with('success', 'Status updated to ' . ucfirst($request->status) . '.');
    }

    // ── Add follow-up log ────────────────────────────────
    public function addFollowup(Request $request, NewSoul $soul)
    {
        $request->validate([
            'method'             => 'required|in:phone,visit,sms,email,church',
            'outcome'            => 'required|in:no_answer,spoke,visited_church,not_interested,other',
            'followup_date'      => 'required|date',
            'next_followup_date' => 'nullable|date',
            'notes'              => 'nullable|string|max:500',
        ]);

        SoulFollowup::create([
            'soul_id'            => $soul->id,
            'user_id'            => auth()->id(),
            'method'             => $request->method,
            'outcome'            => $request->outcome,
            'followup_date'      => $request->followup_date,
            'next_followup_date' => $request->next_followup_date,
            'notes'              => $request->notes,
        ]);

        // Auto-update status based on outcome
        if ($request->outcome === 'visited_church' && $soul->status === 'contacted') {
            $soul->update(['status' => 'attending']);
        } elseif ($request->outcome === 'spoke' && $soul->status === 'new') {
            $soul->update(['status' => 'contacted']);
        }

        return back()->with('success', 'Follow-up log added.');
    }

    // ── Convert to member ────────────────────────────────
    public function convertToMember(NewSoul $soul)
    {
        // Pre-fill member create form with soul data
        return redirect()->route('admin.members.create', [
            'first_name' => $soul->first_name,
            'last_name'  => $soul->last_name,
            'phone'      => $soul->phone,
            'email'      => $soul->email,
            'address'    => $soul->address,
            'soul_id'    => $soul->id,
        ]);
    }

    // ── After conversion, mark as converted ─────────────
    public function markConverted(Request $request)
    {
        if ($request->filled('soul_id')) {
            NewSoul::find($request->soul_id)?->update(['status' => 'converted']);
        }
    }

    // ── Delete ───────────────────────────────────────────
    public function destroy(NewSoul $soul)
    {
        $soul->delete();
        return back()->with('success', 'Record deleted.');
    }
}
