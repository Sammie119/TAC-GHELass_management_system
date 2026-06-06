@extends('layouts.admin')
@section('page-title', 'Cell Groups')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Cell Groups</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Manage home cell groups and their members</p>
        </div>
        <a href="{{ route('admin.cells.create') }}"
           style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
            + New Cell Group
        </a>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:1.5rem;">
        <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:18px;text-align:center;">
            <p style="font-size:32px;font-weight:800;color:#2563eb;">{{ $stats['total'] }}</p>
            <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Total groups</p>
        </div>
        <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:18px;text-align:center;">
            <p style="font-size:32px;font-weight:800;color:#16a34a;">{{ $stats['active'] }}</p>
            <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Active groups</p>
        </div>
        <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:18px;text-align:center;">
            <p style="font-size:32px;font-weight:800;color:#7c3aed;">{{ $stats['members'] }}</p>
            <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Total assignments</p>
        </div>
    </div>

    {{-- Groups grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;">
        @forelse($groups as $group)
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;transition:box-shadow 0.15s;"
                 onmouseenter="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.08)'"
                 onmouseleave="this.style.boxShadow='none'">

                {{-- Card header --}}
                <div style="background:linear-gradient(135deg,#2563eb,#4f46e5);padding:16px 18px;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                        <div>
                            <h3 style="font-size:16px;font-weight:700;color:white;margin-bottom:3px;">{{ $group->name }}</h3>
                            @if($group->area)
                                <p style="font-size:12px;color:rgba(255,255,255,0.8);">
                                    📍 {{ $group->area }}
                                </p>
                            @endif
                        </div>
                        <span style="background:{{ $group->status === 'active' ? '#dcfce7' : '#f3f4f6' }};
                             color:{{ $group->status === 'active' ? '#15803d' : '#6b7280' }};
                             padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">
                    {{ ucfirst($group->status) }}
                </span>
                    </div>
                </div>

                <div style="padding:16px 18px;">
                    {{-- Leader --}}
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                        <div style="width:28px;height:28px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:10px;font-weight:700;flex-shrink:0;">
                            {{ $group->leader ? strtoupper(substr($group->leader->first_name,0,1).substr($group->leader->last_name,0,1)) : '?' }}
                        </div>
                        <div>
                            <p style="font-size:12px;color:#9ca3af;">Leader</p>
                            <p style="font-size:13px;font-weight:500;color:#111827;">{{ $group->leader?->full_name ?? 'Not assigned' }}</p>
                        </div>
                    </div>

                    {{-- Meeting info --}}
                    @if($group->meeting_day)
                        <div style="background:#f9fafb;border-radius:8px;padding:8px 10px;margin-bottom:12px;font-size:12px;color:#6b7280;display:flex;align-items:center;gap:6px;">
                            <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ ucfirst($group->meeting_day) }}
                            @if($group->meeting_time)
                                at {{ \Carbon\Carbon::parse($group->meeting_time)->format('h:i A') }}
                            @endif
                            @if($group->meeting_venue)
                                · {{ $group->meeting_venue }}
                            @endif
                        </div>
                    @endif

                    {{-- Member count + actions --}}
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;color:#374151;">
                    <strong style="color:#2563eb;">{{ $group->members_count }}</strong> members
                </span>
                        <div style="display:flex;gap:8px;">
                            <a href="{{ route('admin.cells.show', $group) }}"
                               style="color:#2563eb;font-size:13px;font-weight:500;text-decoration:none;">View</a>
                            <a href="{{ route('admin.cells.edit', $group) }}"
                               style="color:#6b7280;font-size:13px;font-weight:500;text-decoration:none;">Edit</a>
                            <form method="POST" action="{{ route('admin.cells.destroy', $group) }}"
                                  onsubmit="return confirm('Delete this group?')">
                                @csrf @method('DELETE')
                                <button style="color:#f87171;font-size:13px;background:none;border:none;cursor:pointer;">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1;background:white;border-radius:14px;border:1px solid #e5e7eb;padding:48px;text-align:center;">
                <div style="font-size:40px;margin-bottom:12px;">👥</div>
                <p style="font-size:15px;font-weight:500;color:#374151;margin-bottom:6px;">No cell groups yet</p>
                <p style="font-size:13px;color:#9ca3af;margin-bottom:16px;">Create your first cell group to organise members.</p>
                <a href="{{ route('admin.cells.create') }}"
                   style="background:#2563eb;color:white;padding:10px 20px;border-radius:8px;font-size:14px;text-decoration:none;">
                    + Create first group
                </a>
            </div>
        @endforelse
    </div>

    <div style="margin-top:16px;">{{ $groups->links() }}</div>

@endsection
