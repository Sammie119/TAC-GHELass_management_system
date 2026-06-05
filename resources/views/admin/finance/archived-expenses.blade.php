@extends('layouts.admin')
@section('page-title', 'Archived Expenses')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Archived Expense Records</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">{{ $records->total() }} archived records</p>
        </div>
        <a href="{{ route('admin.finance.expenses') }}"
           style="border:1px solid #d1d5db;color:#374151;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
            ← Back to expenses
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" style="display:flex;gap:10px;margin-bottom:1.5rem;flex-wrap:wrap;">
        <input type="date" name="from" value="{{ request('from') }}"
               style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
        <input type="date" name="to" value="{{ request('to') }}"
               style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
        <select name="category"
                style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
            <option value="">All categories</option>
            @foreach(config('finance.expense_categories') as $key => $label)
                <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit"
                style="background:#f3f4f6;border:1px solid #d1d5db;padding:8px 16px;border-radius:8px;font-size:13px;cursor:pointer;">
            Filter
        </button>
    </form>

    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#fef2f2;">
            <tr>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Date</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Description</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Category</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Amount</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Archived on</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($records as $record)
                <tr style="border-top:1px solid #f3f4f6;opacity:0.8;"
                    onmouseenter="this.style.background='#fef2f2';this.style.opacity='1'"
                    onmouseleave="this.style.background='';this.style.opacity='0.8'">
                    <td style="padding:12px 16px;color:#6b7280;">{{ $record->expense_date->format('d M Y') }}</td>
                    <td style="padding:12px 16px;">
                        <p style="font-weight:500;color:#6b7280;">{{ $record->description }}</p>
                        @if($record->payee)
                            <p style="font-size:11px;color:#9ca3af;">{{ $record->payee }}</p>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                    <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#f3f4f6;color:#6b7280;">
                        {{ ucfirst($record->category) }}
                    </span>
                    </td>
                    <td style="padding:12px 16px;color:#6b7280;">
                        {{ $record->currency }} {{ number_format($record->amount, 2) }}
                        <span style="font-size:11px;color:#9ca3af;display:block;">GH₵ {{ number_format($record->amount_ghs, 2) }}</span>
                    </td>
                    <td style="padding:12px 16px;color:#9ca3af;font-size:12px;">
                        {{ $record->deleted_at->format('d M Y, h:i A') }}
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex;gap:10px;align-items:center;">
                            {{-- Restore --}}
                            <form method="POST" action="{{ route('admin.finance.expenses.restore', $record->id) }}">
                                @csrf
                                <button type="submit"
                                        style="color:#16a34a;font-size:12px;background:none;border:none;cursor:pointer;font-weight:500;">
                                    Restore
                                </button>
                            </form>

                            {{-- Permanent delete --}}
                            @role('admin')
                                <form method="POST"
                                      action="{{ route('admin.finance.expenses.force-delete', $record->id) }}"
                                      onsubmit="return confirm('Permanently delete this record? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            style="color:#f87171;font-size:12px;background:none;border:none;cursor:pointer;">
                                        Delete permanently
                                    </button>
                                </form>
                            @endrole
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding:48px;text-align:center;color:#9ca3af;font-size:14px;">
                        No archived expense records.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $records->links() }}</div>

@endsection
