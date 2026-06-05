@extends('layouts.admin')
@section('page-title', 'Member Tithes')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Member Tithes</h2>
            <p style="font-size:13px;color:#9ca3af;">Tithe history per member</p>
        </div>
        <a href="{{ route('admin.finance.income') }}"
           style="background:#16a34a;color:white;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
            + Record Tithe
        </a>
    </div>

    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">#</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Member</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Department</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Total Tithe (GHS)</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($members as $i => $member)
                <tr style="border-top:1px solid #f3f4f6;"
                    onmouseenter="this.style.background='#f9fafb'"
                    onmouseleave="this.style.background=''">
                    <td style="padding:12px 16px;color:#9ca3af;">
                        {{ ($members->currentPage() - 1) * $members->perPage() + $i + 1 }}
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:11px;font-weight:600;flex-shrink:0;">
                                {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
                            </div>
                            <div>
                                <p style="font-weight:500;color:#111827;">{{ $member->full_name }}</p>
                                <p style="font-size:11px;color:#9ca3af;font-family:monospace;">{{ $member->member_id_card }}</p>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;color:#6b7280;">{{ $member->department ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                    <span style="font-size:16px;font-weight:800;color:#16a34a;">
                        GH₵ {{ number_format($member->total_tithe ?? 0, 2) }}
                    </span>
                    </td>
                    <td style="padding:12px 16px;">
                        <a href="{{ route('admin.finance.income') }}?member_id={{ $member->id }}&category=tithe"
                           style="font-size:12px;color:#2563eb;text-decoration:none;">
                            View history →
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding:40px;text-align:center;color:#9ca3af;">No tithe records found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $members->links() }}</div>

@endsection
