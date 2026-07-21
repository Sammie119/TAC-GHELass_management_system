@extends('layouts.admin')
@section('page-title', 'Cash Book')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Cash Book</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Chronological ledger of receipts and payments, per account</p>
        </div>

        <form method="GET" style="display:flex;gap:8px;align-items:center;">
            <input type="hidden" name="account" value="{{ $account }}">
            <label style="font-size:13px;color:#374151;">Financial year</label>
            <select name="year" onchange="this.form.submit()"
                    style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
                @foreach(range(now()->year + 1, now()->year - 3) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px;border-radius:8px;margin-bottom:16px;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Account tabs --}}
    <div style="display:flex;gap:8px;margin-bottom:1.5rem;flex-wrap:wrap;">
        @foreach($tabs as $key => $label)
            <a href="{{ route('admin.cash-book.index', ['year' => $year, 'account' => $key]) }}"
               style="padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;
                  {{ $account === $key
                     ? 'background:#2563eb;color:white;'
                     : 'background:white;border:1px solid #d1d5db;color:#374151;' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(1,1fr);gap:14px;margin-bottom:1.5rem;">
        <style>@media(min-width:768px){.cashbook-stats{grid-template-columns:repeat(4,1fr) !important;}}</style>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;" class="cashbook-stats">

            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:20px;font-weight:800;color:#2563eb;">GH₵ {{ number_format($openingBalance, 2) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Opening balance</p>
            </div>
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:20px;font-weight:800;color:#16a34a;">GH₵ {{ number_format($totalReceipts, 2) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Total receipts</p>
            </div>
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:20px;font-weight:800;color:#dc2626;">GH₵ {{ number_format($totalPayments, 2) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Total payments</p>
            </div>
            <div style="background:{{ $closingBalance >= 0 ? '#f0fdf4' : '#fef2f2' }};border:1px solid {{ $closingBalance >= 0 ? '#bbf7d0' : '#fecaca' }};border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:20px;font-weight:800;color:{{ $closingBalance >= 0 ? '#16a34a' : '#dc2626' }};">GH₵ {{ number_format($closingBalance, 2) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Closing balance</p>
            </div>

        </div>
    </div>

    {{-- Opening balance form --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;margin-bottom:1.5rem;">
        <button type="button" onclick="document.getElementById('opening-balance-form').style.display='flex'; this.style.display='none';"
                style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:8px 16px;border-radius:8px;font-size:13px;cursor:pointer;">
            Set opening balance for {{ $tabs[$account] }} ({{ $year }})
        </button>
        <form id="opening-balance-form" method="POST" action="{{ route('admin.cash-book.opening-balance') }}"
              style="display:none;gap:10px;align-items:flex-end;margin-top:4px;">
            @csrf
            <input type="hidden" name="financial_year" value="{{ $year }}">
            <input type="hidden" name="account" value="{{ $account }}">
            <div>
                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Opening balance (GHS)</label>
                <input type="number" name="amount" step="0.01" min="0" value="{{ $openingBalance }}" required
                       style="border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;width:200px;">
            </div>
            <button type="submit"
                    style="background:#2563eb;color:white;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                Save
            </button>
        </form>
    </div>

    {{-- Ledger --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Date</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Particulars</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Reference</th>
                <th style="padding:11px 16px;text-align:right;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Receipts</th>
                <th style="padding:11px 16px;text-align:right;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Payments</th>
                <th style="padding:11px 16px;text-align:right;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Balance</th>
            </tr>
            </thead>
            <tbody>
            <tr style="border-top:1px solid #f3f4f6;background:#f9fafb;">
                <td colspan="5" style="padding:10px 16px;font-weight:600;color:#374151;">Opening balance</td>
                <td style="padding:10px 16px;text-align:right;font-weight:700;color:#2563eb;">GH₵ {{ number_format($openingBalance, 2) }}</td>
            </tr>
            @forelse($ledger as $row)
                <tr style="border-top:1px solid #f3f4f6;"
                    onmouseenter="this.style.background='#f9fafb'"
                    onmouseleave="this.style.background=''">
                    <td style="padding:12px 16px;color:#6b7280;">{{ $row['date']->format('d M Y') }}</td>
                    <td style="padding:12px 16px;font-weight:500;color:#111827;">{{ $row['particulars'] }}</td>
                    <td style="padding:12px 16px;color:#6b7280;font-family:monospace;font-size:12px;">{{ $row['reference'] ?? '—' }}</td>
                    <td style="padding:12px 16px;text-align:right;color:#16a34a;font-weight:600;">
                        {{ $row['type'] === 'receipt' ? number_format($row['amount_ghs'], 2) : '—' }}
                    </td>
                    <td style="padding:12px 16px;text-align:right;color:#dc2626;font-weight:600;">
                        {{ $row['type'] === 'payment' ? number_format($row['amount_ghs'], 2) : '—' }}
                    </td>
                    <td style="padding:12px 16px;text-align:right;font-weight:700;color:{{ $row['balance'] >= 0 ? '#111827' : '#dc2626' }};">
                        GH₵ {{ number_format($row['balance'], 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding:48px;text-align:center;color:#9ca3af;font-size:14px;">
                        No {{ strtolower($tabs[$account]) }} transactions recorded for {{ $year }}.
                    </td>
                </tr>
            @endforelse
            @if($ledger->isNotEmpty())
                <tr style="border-top:2px solid #e5e7eb;background:#f9fafb;">
                    <td colspan="3" style="padding:12px 16px;font-weight:700;color:#374151;">Totals / Closing balance</td>
                    <td style="padding:12px 16px;text-align:right;font-weight:700;color:#16a34a;">GH₵ {{ number_format($totalReceipts, 2) }}</td>
                    <td style="padding:12px 16px;text-align:right;font-weight:700;color:#dc2626;">GH₵ {{ number_format($totalPayments, 2) }}</td>
                    <td style="padding:12px 16px;text-align:right;font-weight:800;color:{{ $closingBalance >= 0 ? '#111827' : '#dc2626' }};">
                        GH₵ {{ number_format($closingBalance, 2) }}
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

@endsection
