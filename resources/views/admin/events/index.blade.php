@extends('layouts.admin')
@section('page-title', 'Events')
@section('content')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Events</h2>
            <p class="text-sm text-gray-400 mt-0.5">{{ $events->total() }} events total</p>
        </div>
        <a href="{{ route('admin.events.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
            + New Event
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex gap-3 mb-6 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search events..."
               class="flex-1 min-w-[200px] border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

        <select name="type"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All types</option>
            <option value="sunday"  {{ request('type') === 'sunday'  ? 'selected' : '' }}>Sunday</option>
            <option value="midweek" {{ request('type') === 'midweek' ? 'selected' : '' }}>Midweek</option>
            <option value="special" {{ request('type') === 'special' ? 'selected' : '' }}>Special</option>
        </select>

        <select name="status"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All statuses</option>
            <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="closed"   {{ request('status') === 'closed'   ? 'selected' : '' }}>Closed</option>
        </select>

        <button type="submit"
                class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
            Filter
        </button>

        @if(request('search') || request('type') || request('status'))
            <a href="{{ route('admin.events.index') }}"
               class="border border-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
                Clear
            </a>
        @endif
    </form>

    {{-- Events table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">Event</th>
                <th class="px-5 py-3 text-left">Date & Time</th>
                <th class="px-5 py-3 text-left">Type</th>
                <th class="px-5 py-3 text-left">Status</th>
                <th class="px-5 py-3 text-left">Attendance</th>
                <th class="px-5 py-3 text-left">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($events as $event)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">{{ $event->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Created by {{ $event->createdBy->name ?? 'System' }}
                        </p>
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        <p>{{ $event->event_date->format('D, d M Y') }}</p>
                        <p class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}
                            @if($event->end_time)
                                – {{ \Carbon\Carbon::parse($event->end_time)->format('h:i A') }}
                            @endif
                        </p>
                    </td>
                    <td class="px-5 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $event->type === 'sunday'  ? 'bg-blue-50 text-blue-600' : '' }}
                        {{ $event->type === 'midweek' ? 'bg-purple-50 text-purple-600' : '' }}
                        {{ $event->type === 'special' ? 'bg-amber-50 text-amber-600' : '' }}">
                        {{ ucfirst($event->type) }}
                    </span>
                    </td>
                    <td class="px-5 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $event->status === 'active'   ? 'bg-green-100 text-green-700' : '' }}
                        {{ $event->status === 'upcoming' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $event->status === 'closed'   ? 'bg-gray-100 text-gray-500' : '' }}">
                        {{ ucfirst($event->status) }}
                    </span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="font-semibold text-gray-800">{{ $event->attendance_count }}</span>
                        <span class="text-gray-400 text-xs ml-1">checked in</span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.events.show', $event) }}"
                               class="text-blue-600 hover:underline text-xs">View</a>
                            <a href="{{ route('admin.events.edit', $event) }}"
                               class="text-gray-500 hover:underline text-xs">Edit</a>

                            @if($event->status === 'upcoming')
                                <form method="POST" action="{{ route('admin.events.activate', $event) }}">
                                    @csrf
                                    <button type="submit"
                                            class="text-green-600 hover:underline text-xs">
                                        Activate
                                    </button>
                                </form>
                            @elseif($event->status === 'active')
                                <form method="POST" action="{{ route('admin.events.close', $event) }}">
                                    @csrf
                                    <button type="submit"
                                            class="text-orange-500 hover:underline text-xs">
                                        Close
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">
                        No events found.
                        <a href="{{ route('admin.events.create') }}" class="text-blue-600 hover:underline ml-1">
                            Create your first event →
                        </a>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $events->links() }}
    </div>

@endsection
