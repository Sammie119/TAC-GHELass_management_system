@extends('layouts.admin')
@section('page-title', 'Pledge Tracking')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Pledge Tracking</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Track member pledges and payment progress</p>
        </div>
        <button onclick="document.getElementById('add-pledge-form').style.display='block'"
                style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-size:14px;border:none;cursor:pointer;">
            + Record Pledge
        </button>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(1,1fr);gap:14px;margin-bottom:1.5rem;">
        <style>@media(min-width:768px){.pledge-stats{grid-template-columns:repeat(6,1fr) !important;}}</style>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;" class="pledge-stats">

            <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:24px;font-weight:800;color:#2563eb;">{{ $stats['total'] }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Total pledges</p>
            </div>
            <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:24px;font-weight:800;color:#d97706;">{{ $stats['active'] }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Active</p>
            </div>
            <div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:24px;font-weight:800;color:#16a34a;">{{ $stats['completed'] }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Completed</p>
            </div>
            <div style="background:#fee2e2;border:1px solid #fecaca;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:24px;font-weight:800;color:#dc2626;">{{ $stats['overdue'] }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Overdue</p>
            </div>
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:14px;font-weight:800;color:#2563eb;">GH₵ {{ number_format($stats['total_pledged'], 2) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Total pledged</p>
            </div>
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:14px;font-weight:800;color:#16a34a;">GH₵ {{ number_format($stats['total_paid'], 2) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Total paid</p>
            </div>

        </div>
    </div>

    {{-- Overall progress bar --}}
    @php $overallProgress = $stats['total_pledged'] > 0 ? round(($stats['total_paid'] / $stats['total_pledged']) * 100) : 0; @endphp
    <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:16px;margin-bottom:1.5rem;">
        <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
            <span style="font-size:13px;font-weight:600;color:#111827;">Overall collection progress</span>
            <span style="font-size:13px;font-weight:700;color:#16a34a;">{{ $overallProgress }}%</span>
        </div>
        <div style="height:10px;background:#f3f4f6;border-radius:5px;overflow:hidden;">
            <div style="height:100%;width:{{ $overallProgress }}%;background:linear-gradient(90deg,#16a34a,#22c55e);border-radius:5px;transition:width 0.5s;"></div>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:6px;font-size:12px;color:#9ca3af;">
            <span>GH₵ {{ number_format($stats['total_paid'], 2) }} collected</span>
            <span>GH₵ {{ number_format($stats['total_pledged'] - $stats['total_paid'], 2) }} remaining</span>
        </div>
    </div>

    {{-- Add pledge form --}}
    <div id="add-pledge-form"
         style="display:none;background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;margin-bottom:1.5rem;">
        <h3 style="font-size:15px;font-weight:600;color:#111827;margin-bottom:20px;">Record new pledge</h3>

        <form method="POST" action="{{ route('admin.pledges.store') }}">
            @csrf

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px;">

                <div style="grid-column:span 2;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Member *</label>
                    <select name="member_id" required
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        <option value="">— Select member —</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->full_name }} ({{ $member->member_id_card }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Category *</label>
                    <select name="category" required
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach(config('finance.income_categories') as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="grid-column:span 3;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Description</label>
                    <input type="text" name="description" placeholder="e.g. Building fund pledge 2026"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Currency *</label>
                    <select name="currency"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach(config('finance.currencies') as $code => $info)
                            <option value="{{ $code }}">{{ $code }} — {{ $info['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Pledged amount *</label>
                    <input type="number" name="pledged_amount" step="0.01" min="1" placeholder="0.00" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Pledge date *</label>
                    <input type="date" name="pledge_date" value="{{ today()->toDateString() }}" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Due date (optional)</label>
                    <input type="date" name="due_date"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div style="grid-column:span 2;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Notes</label>
                    <input type="text" name="notes" placeholder="Any additional details"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit"
                        style="background:#2563eb;color:white;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                    Save pledge
                </button>
                <button type="button" onclick="document.getElementById('add-pledge-form').style.display='none'"
                        style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:10px 20px;border-radius:8px;font-size:14px;cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    {{-- Filter --}}
    <form method="GET" style="display:flex;gap:10px;margin-bottom:1.5rem;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search member..."
               style="flex:1;min-width:200px;border:1px solid #d1d5db;border-radius:8px;padding:8px 14px;font-size:14px;outline:none;">
        <select name="status"
                style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
            <option value="">All statuses</option>
            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="overdue"   {{ request('status') === 'overdue'   ? 'selected' : '' }}>Overdue</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <button type="submit"
                style="background:#f3f4f6;border:1px solid #d1d5db;padding:8px 16px;border-radius:8px;font-size:13px;cursor:pointer;">
            Filter
        </button>
    </form>

    {{-- Pledges table --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Member</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Description</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Pledged</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Progress</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Due date</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Status</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($pledges as $pledge)
                <tr style="border-top:1px solid #f3f4f6;"
                    onmouseenter="this.style.background='#f9fafb'"
                    onmouseleave="this.style.background=''">

                    <td style="padding:12px 16px;">
                        <p style="font-weight:500;color:#111827;">{{ $pledge->member->full_name }}</p>
                        <p style="font-size:11px;color:#9ca3af;font-family:monospace;">{{ $pledge->member->member_id_card }}</p>
                    </td>

                    <td style="padding:12px 16px;">
                        <p style="color:#374151;">{{ $pledge->description ?? ucfirst($pledge->category) }}</p>
                        <p style="font-size:11px;color:#9ca3af;">{{ $pledge->pledge_date->format('d M Y') }}</p>
                    </td>

                    <td style="padding:12px 16px;">
                        <p style="font-weight:700;color:#111827;">{{ $pledge->currency }} {{ number_format($pledge->pledged_amount, 2) }}</p>
                        <p style="font-size:11px;color:#16a34a;">Paid: {{ number_format($pledge->paid_amount, 2) }}</p>
                    </td>

                    <td style="padding:12px 16px;min-width:120px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="flex:1;height:6px;background:#f3f4f6;border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:{{ $pledge->progress }}%;
                                        background:{{ $pledge->progress >= 100 ? '#16a34a' : ($pledge->progress >= 50 ? '#d97706' : '#2563eb') }};
                                        border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;font-weight:600;color:#374151;min-width:32px;">{{ $pledge->progress }}%</span>
                        </div>
                        <p style="font-size:11px;color:#9ca3af;margin-top:3px;">
                            Rem: {{ $pledge->currency }} {{ number_format($pledge->remaining, 2) }}
                        </p>
                    </td>

                    <td style="padding:12px 16px;color:#6b7280;font-size:12px;">
                        @if($pledge->due_date)
                            {{ $pledge->due_date->format('d M Y') }}
                            @if($pledge->due_date->isPast() && $pledge->status !== 'completed')
                                <span style="display:block;color:#dc2626;font-weight:500;">Overdue</span>
                            @elseif($pledge->due_date->diffInDays() <= 7 && $pledge->status === 'active')
                                <span style="display:block;color:#d97706;font-weight:500;">Due soon</span>
                            @endif
                        @else
                            —
                        @endif
                    </td>

                    <td style="padding:12px 16px;">
                    <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;
                        {{ $pledge->status === 'active'    ? 'background:#fef3c7;color:#d97706;' : '' }}
                        {{ $pledge->status === 'completed' ? 'background:#dcfce7;color:#15803d;' : '' }}
                        {{ $pledge->status === 'overdue'   ? 'background:#fee2e2;color:#dc2626;' : '' }}
                        {{ $pledge->status === 'cancelled' ? 'background:#f3f4f6;color:#6b7280;' : '' }}">
                        {{ ucfirst($pledge->status) }}
                    </span>
                    </td>

                    <td style="padding:12px 16px;">
                        <div style="display:flex;gap:8px;align-items:center;">
                            <a href="{{ route('admin.pledges.show', $pledge) }}"
                               style="color:#2563eb;font-size:13px;font-weight:500;text-decoration:none;">View</a>
                            @if($pledge->status === 'active' || $pledge->status === 'overdue')
                                <form method="POST" action="{{ route('admin.pledges.cancel', $pledge) }}"
                                      onsubmit="return confirm('Cancel this pledge?')">
                                    @csrf
                                    <button style="color:#9ca3af;font-size:12px;background:none;border:none;cursor:pointer;">Cancel</button>
                                </form>
                            @endif
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding:48px;text-align:center;color:#9ca3af;font-size:14px;">
                        <div style="font-size:40px;margin-bottom:12px;">📋</div>
                        No pledges recorded yet. Click "Record Pledge" to get started.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $pledges->links() }}</div>

@endsection
