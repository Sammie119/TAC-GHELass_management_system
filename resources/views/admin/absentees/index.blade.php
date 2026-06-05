@extends('layouts.admin')
@section('page-title', 'Absentee Follow-up')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Absentee Follow-up</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">
                Track and follow up with members who have missed multiple services
            </p>
        </div>

        {{-- Run scan --}}
        <form method="POST" action="{{ route('admin.absentees.scan') }}" style="display:flex;gap:8px;align-items:center;">
            @csrf
            <select name="threshold"
                    style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
                <option value="1">1+ consecutive absences</option>
                <option value="2">2+ consecutive absences</option>
                <option value="3" selected>3+ consecutive absences</option>
                <option value="4">4+ consecutive absences</option>
                <option value="5">5+ consecutive absences</option>
            </select>
            <button type="submit"
                    style="background:#7c3aed;color:white;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;border:none;cursor:pointer;display:flex;align-items:center;gap:6px;">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Run scan
            </button>
        </form>
    </div>

    {{-- Summary cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:1.5rem;">
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:18px;text-align:center;">
            <p style="font-size:28px;font-weight:800;color:#dc2626;">{{ $summary['flagged'] }}</p>
            <p style="font-size:13px;color:#9ca3af;margin-top:4px;">Flagged</p>
        </div>
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:18px;text-align:center;">
            <p style="font-size:28px;font-weight:800;color:#d97706;">{{ $summary['contacted'] }}</p>
            <p style="font-size:13px;color:#9ca3af;margin-top:4px;">Contacted</p>
        </div>
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:18px;text-align:center;">
            <p style="font-size:28px;font-weight:800;color:#16a34a;">{{ $summary['resolved'] }}</p>
            <p style="font-size:13px;color:#9ca3af;margin-top:4px;">Resolved</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" style="display:flex;gap:12px;margin-bottom:1.5rem;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name or phone..."
               style="flex:1;min-width:200px;border:1px solid #d1d5db;border-radius:8px;padding:8px 14px;font-size:14px;outline:none;">
        <select name="status"
                style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;outline:none;">
            <option value="">All statuses</option>
            <option value="flagged"   {{ request('status') === 'flagged'   ? 'selected' : '' }}>Flagged</option>
            <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
            <option value="resolved"  {{ request('status') === 'resolved'  ? 'selected' : '' }}>Resolved</option>
        </select>
        <button type="submit"
                style="background:#f3f4f6;border:1px solid #d1d5db;padding:8px 16px;border-radius:8px;font-size:14px;cursor:pointer;">
            Filter
        </button>
    </form>

    {{-- Flags table --}}
    <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Member</th>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Absences</th>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Last Seen</th>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Status</th>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Assigned to</th>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($flags as $flag)
                <tr style="border-top:1px solid #f3f4f6;"
                    onmouseenter="this.style.background='#f9fafb'"
                    onmouseleave="this.style.background=''">

                    {{-- Member --}}
                    <td style="padding:14px 20px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;color:#dc2626;font-size:12px;font-weight:600;flex-shrink:0;">
                                {{ strtoupper(substr($flag->member->first_name,0,1).substr($flag->member->last_name,0,1)) }}
                            </div>
                            <div>
                                <p style="font-weight:500;color:#111827;">{{ $flag->member->full_name }}</p>
                                <p style="font-size:12px;color:#9ca3af;">{{ $flag->member->phone ?? $flag->member->email ?? '—' }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Consecutive absences --}}
                    <td style="padding:14px 20px;">
                    <span style="font-size:22px;font-weight:800;color:{{ $flag->consecutive_absences >= 5 ? '#dc2626' : ($flag->consecutive_absences >= 3 ? '#d97706' : '#374151') }};">
                        {{ $flag->consecutive_absences }}
                    </span>
                        <span style="font-size:12px;color:#9ca3af;margin-left:2px;">in a row</span>
                    </td>

                    {{-- Last seen --}}
                    <td style="padding:14px 20px;font-size:13px;color:#6b7280;">
                        @if($flag->last_attended)
                            {{ $flag->last_attended->format('d M Y') }}
                            <span style="display:block;font-size:11px;color:#9ca3af;">
                            {{ $flag->last_attended->diffForHumans() }}
                        </span>
                        @else
                            <span style="color:#dc2626;font-size:13px;">Never attended</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td style="padding:14px 20px;">
                    <span style="padding:4px 12px;border-radius:20px;font-size:12px;font-weight:500;
                        {{ $flag->status === 'flagged'   ? 'background:#fee2e2;color:#dc2626;' : '' }}
                        {{ $flag->status === 'contacted' ? 'background:#fef3c7;color:#d97706;' : '' }}
                        {{ $flag->status === 'resolved'  ? 'background:#dcfce7;color:#16a34a;' : '' }}">
                        {{ ucfirst($flag->status) }}
                    </span>
                    </td>

                    {{-- Assigned to --}}
                    <td style="padding:14px 20px;font-size:13px;color:#6b7280;">
                        {{ $flag->assignedTo->name ?? '—' }}
                    </td>

                    {{-- Actions --}}
                    <td style="padding:14px 20px;">
                        <button onclick="openModal({{ $flag->id }}, '{{ $flag->member->full_name }}', '{{ $flag->status }}', '{{ $flag->assignedTo?->id }}', `{{ addslashes($flag->notes ?? '') }}`)"
                                style="background:#2563eb;color:white;padding:5px 12px;border-radius:6px;font-size:12px;border:none;cursor:pointer;margin-right:6px;">
                            Update
                        </button>
                        <form method="POST" action="{{ route('admin.absentees.unflag', $flag) }}"
                              style="display:inline;"
                              onsubmit="return confirm('Remove flag for {{ $flag->member->full_name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="color:#9ca3af;font-size:12px;background:none;border:none;cursor:pointer;">
                                Remove
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding:48px 20px;text-align:center;">
                        <p style="font-size:28px;margin-bottom:8px;">✅</p>
                        <p style="font-weight:500;color:#15803d;margin-bottom:4px;">No absentees flagged</p>
                        <p style="font-size:13px;color:#9ca3af;">
                            Run a scan above or adjust the absence threshold.
                        </p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">{{ $flags->links() }}</div>

    {{-- Update status modal --}}
    <div id="modal-overlay"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:100;align-items:center;justify-content:center;">
        <div style="background:white;border-radius:16px;padding:28px;width:100%;max-width:460px;margin:1rem;">
            <h3 style="font-size:16px;font-weight:600;color:#111827;margin-bottom:4px;" id="modal-title">
                Update Follow-up
            </h3>
            <p style="font-size:13px;color:#9ca3af;margin-bottom:20px;" id="modal-subtitle"></p>

            <form method="POST" id="modal-form">
                @csrf @method('PATCH')

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Status</label>
                    <select name="status" id="modal-status"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;">
                        <option value="flagged">Flagged</option>
                        <option value="contacted">Contacted</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Assign to</label>
                    <select name="assigned_to" id="modal-assigned"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;">
                        <option value="">— Unassigned —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Notes</label>
                    <textarea name="notes" id="modal-notes" rows="3"
                              placeholder="e.g. Called on Sunday, will visit next week..."
                              style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;resize:none;box-sizing:border-box;"></textarea>
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit"
                            style="flex:1;background:#2563eb;color:white;padding:11px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                        Save
                    </button>
                    <button type="button" onclick="closeModal()"
                            style="flex:1;background:#f3f4f6;color:#374151;padding:11px;border-radius:8px;font-size:14px;border:1px solid #e5e7eb;cursor:pointer;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(flagId, memberName, status, assignedTo, notes) {
            document.getElementById('modal-title').textContent   = 'Update: ' + memberName;
            document.getElementById('modal-form').action         =
                '/admin/absentees/' + flagId + '/status';
            document.getElementById('modal-status').value        = status;
            document.getElementById('modal-assigned').value      = assignedTo || '';
            document.getElementById('modal-notes').value         = notes || '';

            const overlay = document.getElementById('modal-overlay');
            overlay.style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('modal-overlay').style.display = 'none';
        }

        // Close on overlay click
        document.getElementById('modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>

@endsection
