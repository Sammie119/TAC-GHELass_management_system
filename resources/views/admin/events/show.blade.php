@extends('layouts.admin')
@section('page-title', 'Event Details')
@section('content')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">{{ $event->title }}</h2>
            <p class="text-sm text-gray-400 mt-0.5">
                {{ $event->event_date->format('D, d M Y') }} ·
                {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}
            </p>
        </div>

        <div class="flex gap-2">
            @if($event->status === 'upcoming')
                <form method="POST" action="{{ route('admin.events.activate', $event) }}">
                    @csrf
                    <button type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition-colors">
                        Activate for check-in
                    </button>
                </form>
            @elseif($event->status === 'active')
                <form method="POST" action="{{ route('admin.events.close', $event) }}">
                    @csrf
                    <button type="submit"
                            class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-600 transition-colors">
                        Close event
                    </button>
                </form>
            @endif
            <div class="flex gap-2">
                {{-- existing activate/close buttons --}}

                <a href="{{ route('admin.events.qr', $event) }}"
                   class="border border-purple-300 text-purple-600 px-4 py-2 rounded-lg text-sm hover:bg-purple-50 transition-colors">
                    Download QR
                </a>

                <a href="{{ route('admin.events.edit', $event) }}"
                   class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
                    Edit
                </a>
            </div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $event->attendance_count }}</p>
            <p class="text-xs text-gray-400 mt-1">Members checked in</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
            <p class="text-3xl font-bold text-amber-500">{{ $event->visitors_count }}</p>
            <p class="text-xs text-gray-400 mt-1">Visitors</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
            {{ $event->status === 'active'   ? 'bg-green-100 text-green-700' : '' }}
            {{ $event->status === 'upcoming' ? 'bg-yellow-100 text-yellow-700' : '' }}
            {{ $event->status === 'closed'   ? 'bg-gray-100 text-gray-500' : '' }}">
            {{ ucfirst($event->status) }}
        </span>
            <p class="text-xs text-gray-400 mt-2">Status</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
            {{ $event->type === 'sunday'  ? 'bg-blue-50 text-blue-600' : '' }}
            {{ $event->type === 'midweek' ? 'bg-purple-50 text-purple-600' : '' }}
            {{ $event->type === 'special' ? 'bg-amber-50 text-amber-600' : '' }}">
            {{ ucfirst($event->type) }}
        </span>
            <p class="text-xs text-gray-400 mt-2">Type</p>
        </div>
    </div>

    {{-- Self check-in link --}}
    @if($event->status === 'active')
        <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 mb-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-blue-800">Self check-in link</p>
                <p class="text-xs text-blue-600 font-mono mt-0.5">
                    {{ route('checkin.show', $event->qr_token) }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.events.qr', $event) }}"
                   class="text-xs bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700">
                    Download QR
                </a>
                <a href="{{ route('checkin.show', $event->qr_token) }}" target="_blank"
                   class="text-xs border border-blue-300 text-blue-600 px-3 py-2 rounded-lg hover:bg-blue-100">
                    Preview
                </a>
            </div>
        </div>
    @endif

    {{-- Attendance list --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800 text-sm">Attendance register</h3>
            <span class="text-xs text-gray-400">{{ $attendance->total() }} records</span>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">Member</th>
                <th class="px-5 py-3 text-left">Member ID</th>
                <th class="px-5 py-3 text-left">Check-in method</th>
                <th class="px-5 py-3 text-left">Time</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($attendance as $record)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center
                                    text-blue-600 text-xs font-semibold shrink-0">
                                {{ strtoupper(substr($record->member->first_name,0,1).substr($record->member->last_name,0,1)) }}
                            </div>
                            <a href="{{ route('admin.members.show', $record->member) }}"
                               class="font-medium text-gray-800 hover:text-blue-600">
                                {{ $record->member->full_name }}
                            </a>
                        </div>
                    </td>
                    <td class="px-5 py-3 font-mono text-gray-400 text-xs">
                        {{ $record->member->member_id_card }}
                    </td>
                    <td class="px-5 py-3">
                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">
                        {{ ucwords(str_replace('_', ' ', $record->checkin_method)) }}
                    </span>
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs">
                        {{ $record->checked_in_at->format('h:i A') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-12 text-center text-gray-400 text-sm">
                        No check-ins recorded for this event yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($attendance->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $attendance->links() }}
            </div>
        @endif
    </div>

@endsection
