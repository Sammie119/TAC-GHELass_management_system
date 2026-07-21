@extends('layouts.admin')
@section('page-title', 'Archived Petty Cash Transactions')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Archived Petty Cash Transactions</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">{{ $transactions->total() }} archived transactions</p>
        </div>
        <a href="{{ route('admin.petty-cash.index') }}"
           style="border:1px solid #d1d5db;color:#374151;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
            ← Back to petty cash
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" style="display:flex;gap:10px;margin-bottom:1.5rem;flex-wrap:wrap;">
        <input type="date" name="from" value="{{ request('from') }}"
               style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
        <input type="date" name="to" value="{{ request('to') }}"
               style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
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
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Type</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Description</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Amount</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Voided on</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($transactions as $transaction)
                <tr style="border-top:1px solid #f3f4f6;opacity:0.8;"
                    onmouseenter="this.style.background='#fef2f2';this.style.opacity='1'"
                    onmouseleave="this.style.background='';this.style.opacity='0.8'">
                    <td style="padding:12px 16px;color:#6b7280;">{{ $transaction->transaction_date->format('d M Y') }}</td>
                    <td style="padding:12px 16px;">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#f3f4f6;color:#6b7280;">
                            {{ ucfirst($transaction->type) }}
                        </span>
                    </td>
                    <td style="padding:12px 16px;color:#6b7280;">{{ $transaction->description }}</td>
                    <td style="padding:12px 16px;color:#6b7280;">
                        GH₵ {{ number_format($transaction->amount_ghs, 2) }}
                    </td>
                    <td style="padding:12px 16px;color:#9ca3af;font-size:12px;">
                        {{ $transaction->deleted_at->format('d M Y, h:i A') }}
                    </td>
                    <td style="padding:12px 16px;">
                        <form method="POST" action="{{ route('admin.petty-cash.restore', $transaction->id) }}">
                            @csrf
                            <button type="submit"
                                    style="color:#16a34a;font-size:12px;background:none;border:none;cursor:pointer;font-weight:500;">
                                Restore
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding:48px;text-align:center;color:#9ca3af;font-size:14px;">
                        No voided petty cash transactions.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $transactions->links() }}</div>

@endsection
