<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        $query = Visitor::with(['event', 'recordedBy'])->orderByDesc('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name',  'like', "%{$search}%")
                    ->orWhere('phone',      'like', "%{$search}%")
                    ->orWhere('email',      'like', "%{$search}%");
            });
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        $visitors = $query->latest('visited_at')->paginate(20)->withQueryString();
        $events   = Event::orderBy('event_date', 'desc')->take(30)->get();

        return view('admin.visitors.index', compact('visitors', 'events'));
    }

    public function create(Request $request)
    {
        $events      = Event::whereIn('status', ['active', 'upcoming'])
            ->orderBy('event_date')->get();
        $selectedEvent = $request->filled('event_id')
            ? Event::find($request->event_id)
            : Event::where('status', 'active')->latest()->first();

        return view('admin.visitors.create', compact('events', 'selectedEvent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:150',
            'event_id'   => 'required|exists:events,id',
            'notes'      => 'nullable|string|max:500',
        ]);

        $validated['recorded_by'] = auth()->id();
        $validated['visited_at']  = now();

        Visitor::create($validated);

        // If redirecting back to checkin page
        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)
                ->with('success', 'Visitor recorded successfully.');
        }

        return redirect()->route('admin.visitors.index')
            ->with('success', 'Visitor recorded successfully.');
    }

    public function show(Visitor $visitor)
    {
        $visitor->load(['event', 'recordedBy']);

        // Check if visitor has been here before
        $previousVisits = Visitor::where(function ($q) use ($visitor) {
            $q->where('phone', $visitor->phone)
                ->orWhere('email', $visitor->email);
        })
            ->where('id', '!=', $visitor->id)
            ->whereNotNull($visitor->phone ? 'phone' : 'email')
            ->with('event')
            ->latest('visited_at')
            ->get();

        return view('admin.visitors.show', compact('visitor', 'previousVisits'));
    }

    public function edit(Visitor $visitor)
    {
        $events = Event::orderBy('event_date', 'desc')->take(30)->get();
        return view('admin.visitors.edit', compact('visitor', 'events'));
    }

    public function update(Request $request, Visitor $visitor)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:150',
            'event_id'   => 'required|exists:events,id',
            'notes'      => 'nullable|string|max:500',
        ]);

        $visitor->update($validated);

        return redirect()->route('admin.visitors.show', $visitor)
            ->with('success', 'Visitor updated successfully.');
    }

    public function destroy(Visitor $visitor)
    {
        $visitor->delete();

        return redirect()->route('admin.visitors.index')
            ->with('success', 'Visitor record deleted.');
    }

    // Quick record via AJAX from check-in page
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:150',
            'event_id'   => 'required|exists:events,id',
        ]);

        $validated['recorded_by'] = auth()->id();
        $validated['visited_at']  = now();

        $visitor = Visitor::create($validated);

        return response()->json([
            'success' => true,
            'message' => "{$visitor->first_name} {$visitor->last_name} recorded as a visitor.",
            'visitor' => [
                'id'       => $visitor->id,
                'name'     => "{$visitor->first_name} {$visitor->last_name}",
                'initials' => strtoupper(substr($visitor->first_name,0,1).substr($visitor->last_name,0,1)),
            ],
        ]);
    }
}
