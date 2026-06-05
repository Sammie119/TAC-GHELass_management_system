@extends('layouts.admin')
@section('page-title', 'Visitors')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Visitors</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">{{ $visitors->total() }} total visitor records</p>
        </div>
        <a href="{{ route('admin.visitors.create') }}"
           style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
            + Record Visitor
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET"
          style="display:flex;gap:12px;margin-bottom:1.5rem;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name, phone, or email..."
               style="flex:1;min-width:200px;border:1px solid #d1d5db;border-radius:8px;padding:8px 14px;font-size:14px;outline:none;">
        <select name="event_id"
                style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;outline:none;min-width:180px;">
            <option value="">All events</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                    {{ $event->title }} ({{ $event->event_date->format('d M Y') }})
                </option>
            @endforeach
        </select>
        <button type="submit"
                style="background:#f3f4f6;border:1px solid #d1d5db;padding:8px 16px;border-radius:8px;font-size:14px;cursor:pointer;">
            Filter
        </button>
        @if(request('search') || request('event_id'))
            <a href="{{ route('admin.visitors.index') }}"
               style="border:1px solid #d1d5db;color:#6b7280;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
                Clear
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Visitor</th>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Contact</th>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Event</th>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Visited</th>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Recorded by</th>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($visitors as $visitor)
                <tr style="border-top:1px solid #f3f4f6;"
                    onmouseenter="this.style.background='#f9fafb'"
                    onmouseleave="this.style.background=''">
                    <td style="padding:14px 20px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;color:#d97706;font-size:12px;font-weight:600;flex-shrink:0;">
                                {{ strtoupper(substr($visitor->first_name,0,1).substr($visitor->last_name,0,1)) }}
                            </div>
                            <div>
                                <p style="font-weight:500;color:#111827;">{{ $visitor->full_name }}</p>
                                @if($visitor->notes)
                                    <p style="font-size:12px;color:#9ca3af;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $visitor->notes }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="padding:14px 20px;color:#4b5563;">
                        <p>{{ $visitor->phone ?? '—' }}</p>
                        <p style="font-size:12px;color:#9ca3af;">{{ $visitor->email ?? '' }}</p>
                    </td>
                    <td style="padding:14px 20px;">
                        <p style="color:#111827;font-weight:500;">{{ $visitor->event->title }}</p>
                        <p style="font-size:12px;color:#9ca3af;">{{ $visitor->event->event_date->format('d M Y') }}</p>
                    </td>
                    <td style="padding:14px 20px;color:#6b7280;font-size:13px;">
                        {{ $visitor->visited_at->format('d M Y') }}<br>
                        <span style="color:#9ca3af;">{{ $visitor->visited_at->format('h:i A') }}</span>
                    </td>
                    <td style="padding:14px 20px;color:#6b7280;font-size:13px;">
                        {{ $visitor->recordedBy->name ?? 'System' }}
                    </td>
                    <td style="padding:14px 20px;">
                        <div style="display:flex;gap:12px;">
                            <a href="{{ route('admin.visitors.show', $visitor) }}"
                               style="color:#2563eb;font-size:13px;text-decoration:none;">View</a>
                            <a href="{{ route('admin.visitors.edit', $visitor) }}"
                               style="color:#6b7280;font-size:13px;text-decoration:none;">Edit</a>
                            <form method="POST" action="{{ route('admin.visitors.destroy', $visitor) }}"
                                  onsubmit="return confirm('Delete this visitor record?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="color:#f87171;font-size:13px;background:none;border:none;cursor:pointer;padding:0;">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6"
                        style="padding:48px 20px;text-align:center;color:#9ca3af;font-size:14px;">
                        No visitor records found.
                        <a href="{{ route('admin.visitors.create') }}"
                           style="color:#2563eb;text-decoration:none;margin-left:4px;">
                            Record the first visitor →
                        </a>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $visitors->links() }}
    </div>

@endsection
