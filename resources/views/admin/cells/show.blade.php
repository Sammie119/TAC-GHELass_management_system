@extends('layouts.admin')
@section('page-title', $cell->name)
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('admin.cells.index') }}" style="color:#9ca3af;font-size:13px;text-decoration:none;">← Back</a>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">{{ $cell->name }}</h2>
            <span style="background:{{ $cell->status === 'active' ? '#dcfce7' : '#f3f4f6' }};
                     color:{{ $cell->status === 'active' ? '#15803d' : '#6b7280' }};
                     padding:3px 12px;border-radius:20px;font-size:12px;font-weight:600;">
            {{ ucfirst($cell->status) }}
        </span>
        </div>
        <a href="{{ route('admin.cells.edit', $cell) }}"
           style="border:1px solid #d1d5db;color:#374151;padding:8px 16px;border-radius:8px;font-size:13px;text-decoration:none;">
            Edit group
        </a>
    </div>

    <div style="display:grid;gap:20px;">
        <style>@media(min-width:1024px){.cell-grid{grid-template-columns:1fr 2fr !important;}}</style>
        <div style="display:grid;gap:20px;" class="cell-grid">

            {{-- Info card --}}
            <div style="display:flex;flex-direction:column;gap:16px;">
                <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                    <div style="background:linear-gradient(135deg,#2563eb,#4f46e5);padding:20px;color:white;">
                        <div style="font-size:32px;margin-bottom:8px;">👥</div>
                        <h3 style="font-size:17px;font-weight:700;">{{ $cell->name }}</h3>
                        @if($cell->area)
                            <p style="font-size:13px;opacity:0.8;margin-top:3px;">📍 {{ $cell->area }}</p>
                        @endif
                    </div>
                    <div style="padding:16px;display:flex;flex-direction:column;gap:10px;font-size:13px;">
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #f9fafb;">
                            <span style="color:#9ca3af;">Leader</span>
                            <span style="font-weight:500;color:#111827;">{{ $cell->leader?->full_name ?? '—' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #f9fafb;">
                            <span style="color:#9ca3af;">Assistant</span>
                            <span style="font-weight:500;color:#111827;">{{ $cell->assistantLeader?->full_name ?? '—' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #f9fafb;">
                            <span style="color:#9ca3af;">Meets</span>
                            <span style="font-weight:500;color:#111827;">
                        {{ $cell->meeting_day ? ucfirst($cell->meeting_day) : '—' }}
                                @if($cell->meeting_time)
                                    at {{ \Carbon\Carbon::parse($cell->meeting_time)->format('h:i A') }}
                                @endif
                    </span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid #f9fafb;">
                            <span style="color:#9ca3af;">Venue</span>
                            <span style="font-weight:500;color:#111827;text-align:right;">{{ $cell->meeting_venue ?? '—' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Members</span>
                            <span style="font-size:18px;font-weight:800;color:#2563eb;">{{ $cell->members->count() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Add member --}}
                <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:18px;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:14px;">Add member</h3>
                    <form method="POST" action="{{ route('admin.cells.members.add', $cell) }}">
                        @csrf
                        <div style="margin-bottom:10px;">
                            <select name="member_id" required
                                    style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:13px;outline:none;">
                                <option value="">— Select member —</option>
                                @foreach($allMembers as $member)
                                    @if(!$cell->members->contains($member->id))
                                        <option value="{{ $member->id }}">
                                            {{ $member->full_name }} ({{ $member->member_id_card }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div style="margin-bottom:12px;">
                            <input type="date" name="joined_date" value="{{ today()->toDateString() }}"
                                   style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:13px;outline:none;box-sizing:border-box;">
                        </div>
                        <button type="submit"
                                style="width:100%;background:#2563eb;color:white;padding:9px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;">
                            Add to group
                        </button>
                    </form>
                </div>
            </div>

            {{-- Members list --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;">Members ({{ $cell->members->count() }})</h3>
                </div>

                @forelse($cell->members as $member)
                    <div style="padding:12px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f9fafb;"
                         onmouseenter="this.style.background='#f9fafb'"
                         onmouseleave="this.style.background=''">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;
                            background:{{ $member->pivot->is_leader ? '#fef3c7' : '#dbeafe' }};
                            display:flex;align-items:center;justify-content:center;
                            color:{{ $member->pivot->is_leader ? '#d97706' : '#2563eb' }};
                            font-size:11px;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
                            </div>
                            <div>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <p style="font-size:13px;font-weight:500;color:#111827;">{{ $member->full_name }}</p>
                                    @if($member->pivot->is_leader)
                                        <span style="background:#fef3c7;color:#d97706;padding:1px 8px;border-radius:20px;font-size:10px;font-weight:600;">
                            Leader
                        </span>
                                    @endif
                                </div>
                                <p style="font-size:11px;color:#9ca3af;">
                                    {{ $member->member_id_card }}
                                    @if($member->pivot->joined_date)
                                        · Joined {{ \Carbon\Carbon::parse($member->pivot->joined_date)->format('d M Y') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.cells.members.remove', [$cell, $member]) }}"
                              onsubmit="return confirm('Remove {{ $member->first_name }} from this group?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="color:#f87171;font-size:12px;background:none;border:none;cursor:pointer;">
                                Remove
                            </button>
                        </form>
                    </div>
                @empty
                    <div style="padding:40px;text-align:center;color:#9ca3af;font-size:14px;">
                        No members yet. Add members using the form.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

@endsection
