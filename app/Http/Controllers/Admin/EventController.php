<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::withCount('attendance')->with('createdBy')->orderByDesc('id');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->latest('event_date')->paginate(20)->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:150',
            'type'        => 'required|in:sunday,midweek,special',
            'event_date'  => 'required|date',
            'start_time'  => 'required',
            'end_time'    => 'nullable',
            'description' => 'nullable|string',
            'status'      => 'required|in:upcoming,active,closed',
        ]);

        $validated['created_by'] = auth()->id();

        // If setting to active, close any currently active event
        if ($validated['status'] === 'active') {
            $this->ensureSingleActiveEvent();
        }

        $event = Event::create($validated);

        return redirect()->route('admin.events.show', $event)
            ->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        $event->loadCount('attendance', 'visitors');
        $attendance = $event->attendance()->with('member')->latest('checked_in_at')->paginate(20);

        return view('admin.events.show', compact('event', 'attendance'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:150',
            'type'        => 'required|in:sunday,midweek,special',
            'event_date'  => 'required|date',
            'start_time'  => 'required',
            'end_time'    => 'nullable',
            'description' => 'nullable|string',
            'status'      => 'required|in:upcoming,active,closed',
        ]);

        // If setting to active, close any other active event
        if ($validated['status'] === 'active') {
            $this->ensureSingleActiveEvent($event->id);
        }

        $event->update($validated);

        return redirect()->route('admin.events.show', $event)
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }

    // Quickly activate an event from the index page
    public function activate(Event $event)
    {
        // Close ALL currently active events first
        $this->ensureSingleActiveEvent($event->id);
        $event->update(['status' => 'active']);

        return back()->with('success', "'{$event->title}' is now active for check-in.");
    }

    // Close an active event
    public function close(Event $event)
    {
        $event->update(['status' => 'closed']);

        return back()->with('success', "'{$event->title}' has been closed.");
    }

    public function downloadQr(Event $event)
    {
        $url     = route('checkin.show', $event->qr_token);
        $qrSvg   = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($url);

        $path = 'event-qrcodes/' . $event->qr_token . '.svg';
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $qrSvg);

        return response($qrSvg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . str_replace(' ', '-', $event->title) . '-checkin-qr.svg"');
    }

    private function ensureSingleActiveEvent(?int $excludeId = null): void
    {
        $query = Event::where('status', 'active');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $query->update(['status' => 'closed']);
    }
}
