@extends('layouts.admin')
@section('page-title', 'Finance Report')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Finance Report</h2>
            <p style="font-size:13px;color:#9ca3af;">{{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.finance.export.pdf') }}?from={{ $from }}&to={{ $to }}"
               style="background:#dc2626;color:white;padding:8px 16px;border-radius:8px;font-size:13px;text-decoration:none;">
                Export PDF
            </a>
            <a href="{{ route('admin.finance.export.excel') }}?from={{ $from }}&to={{ $to }}"
               style="background:#16a34a;color:white;padding:8px 16px;border-radius:8px;font-size:13px;text-decoration:none;">
                Export Excel
            </a>
        </div>
    </div>

    {{-- Date range picker --}}
    <form method="GET" style="display:flex;gap:10px;align-items:flex-end;margin-bottom:1.5rem;flex-wrap:wrap;">
        <div>
            <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">From</label>
            <input type="date" name="from" value="{{ $from }}"
                   style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;outline:none;">
        </div>
        <div>
            <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">To</label>
            <input type="date" name="to" value="{{ $to }}"
                   style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;outline:none;">
        </div>

        {{-- Quick ranges --}}
        <div style="display:flex;gap:6px;flex-wrap:wrap;">
            @php
                $ranges = [
                    'This month'    => [now()->startOfMonth()->toDateString(), now()->toDateString()],
                    'Last month'    => [now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
                    'This quarter'  => [now()->startOfQuarter()->toDateString(), now()->toDateString()],
                    'This year'     => [now()->startOfYear()->toDateString(), now()->toDateString()],
                ];
            @endphp
            @foreach($ranges as $label => [$f, $t])
                <a href="{{ route('admin.finance.report') }}?from={{ $f }}&to={{ $t }}"
                   style="font-size:12px;padding:8px 12px;border-radius:8px;text-decoration:none;
                  background:{{ $from === $f && $to === $t ? '#2563eb' : '#f3f4f6' }};
                  color:{{ $from === $f && $to === $t ? 'white' : '#374151' }};
                  border:1px solid {{ $from === $f && $to === $t ? '#2563eb' : '#d1d5db' }};">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <button type="submit"
                style="background:#2563eb;color:white;padding:9px 18px;border-radius:8px;font-size:14px;border:none;cursor:pointer;">
            Apply
        </button>
    </form>

    {{-- Summary --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:1.5rem;">
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:18px;text-align:center;">
            <p style="font-size:13px;color:#15803d;margin-bottom:6px;font-weight:500;">Total Income</p>
            <p style="font-size:28px;font-weight:800;color:#16a34a;">GH₵ {{ number_format($totalIncome, 2) }}</p>
        </div>
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:18px;text-align:center;">
            <p style="font-size:13px;color:#dc2626;margin-bottom:6px;font-weight:500;">Total Expenses</p>
            <p style="font-size:28px;font-weight:800;color:#dc2626;">GH₵ {{ number_format($totalExpenses, 2) }}</p>
        </div>
        <div style="background:{{ $netBalance >= 0 ? '#eff6ff' : '#fef2f2' }};border:1px solid {{ $netBalance >= 0 ? '#bfdbfe' : '#fecaca' }};border-radius:12px;padding:18px;text-align:center;">
            <p style="font-size:13px;color:{{ $netBalance >= 0 ? '#2563eb' : '#dc2626' }};margin-bottom:6px;font-weight:500;">
                Net {{ $netBalance >= 0 ? 'Surplus' : 'Deficit' }}
            </p>
            <p style="font-size:28px;font-weight:800;color:{{ $netBalance >= 0 ? '#2563eb' : '#dc2626' }};">
                GH₵ {{ number_format(abs($netBalance), 2) }}
            </p>
        </div>
    </div>

    {{-- Category breakdown --}}
    <div style="display:grid;gap:20px;margin-bottom:1.5rem;">
        <style>@media(min-width:768px){.cat-grid{grid-template-columns:1fr 1fr !important;}}</style>
        <div style="display:grid;gap:20px;" class="cat-grid">

            {{-- Income by category --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;background:#f0fdf4;display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;">Income by category</h3>
                    <span style="font-size:12px;color:#16a34a;font-weight:600;">GH₵ {{ number_format($totalIncome, 2) }}</span>
                </div>
                @foreach($incomeByCategory as $key => $data)
                    <div style="padding:12px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #f9fafb;
            {{ $data['total'] > 0 ? '' : 'opacity:0.45;' }}">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:8px;height:8px;border-radius:50%;background:{{ $data['total'] > 0 ? '#16a34a' : '#d1d5db' }};flex-shrink:0;"></div>
                            <div>
                                <p style="font-size:13px;font-weight:500;color:#111827;">{{ $data['label'] }}</p>
                                <p style="font-size:11px;color:#9ca3af;">{{ $data['count'] }} record(s)</p>
                            </div>
                        </div>
                        <p style="font-size:14px;font-weight:700;color:{{ $data['total'] > 0 ? '#16a34a' : '#9ca3af' }};">
                            GH₵ {{ number_format($data['total'], 2) }}
                        </p>
                    </div>
                @endforeach
                <div style="padding:12px 16px;background:#f0fdf4;display:flex;justify-content:space-between;">
                    <p style="font-size:13px;font-weight:700;color:#15803d;">Total</p>
                    <p style="font-size:14px;font-weight:800;color:#16a34a;">GH₵ {{ number_format($totalIncome, 2) }}</p>
                </div>
            </div>

            {{-- Expense by category --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;background:#fef2f2;display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;">Expenses by category</h3>
                    <span style="font-size:12px;color:#dc2626;font-weight:600;">GH₵ {{ number_format($totalExpenses, 2) }}</span>
                </div>
                @foreach($expenseByCategory as $key => $data)
                    <div style="padding:12px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #f9fafb;
            {{ $data['total'] > 0 ? '' : 'opacity:0.45;' }}">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:8px;height:8px;border-radius:50%;background:{{ $data['total'] > 0 ? '#dc2626' : '#d1d5db' }};flex-shrink:0;"></div>
                            <div>
                                <p style="font-size:13px;font-weight:500;color:#111827;">{{ $data['label'] }}</p>
                                <p style="font-size:11px;color:#9ca3af;">{{ $data['count'] }} record(s)</p>
                            </div>
                        </div>
                        <p style="font-size:14px;font-weight:700;color:{{ $data['total'] > 0 ? '#dc2626' : '#9ca3af' }};">
                            GH₵ {{ number_format($data['total'], 2) }}
                        </p>
                    </div>
                @endforeach
                <div style="padding:12px 16px;background:#fef2f2;display:flex;justify-content:space-between;">
                    <p style="font-size:13px;font-weight:700;color:#dc2626;">Total</p>
                    <p style="font-size:14px;font-weight:800;color:#dc2626;">GH₵ {{ number_format($totalExpenses, 2) }}</p>
                </div>
            </div>

        </div>
    </div>

    {{-- Member tithes breakdown --}}
    @if(count($memberTithes) > 0)
        <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;margin-bottom:1.5rem;">
            <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
                <h3 style="font-size:14px;font-weight:600;color:#111827;">Tithe by member</h3>
                <span style="font-size:12px;color:#9ca3af;">{{ count($memberTithes) }} tithers</span>
            </div>
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="background:#f9fafb;">
                <tr>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">#</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">Member</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">Member ID</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">Records</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;color:#6b7280;font-weight:500;">Total Tithe (GHS)</th>
                </tr>
                </thead>
                <tbody>
                @foreach($memberTithes as $i => $tithe)
                    <tr style="border-top:1px solid #f3f4f6;"
                        onmouseenter="this.style.background='#f9fafb'"
                        onmouseleave="this.style.background=''">
                        <td style="padding:10px 16px;color:#9ca3af;">{{ $i + 1 }}</td>
                        <td style="padding:10px 16px;font-weight:500;color:#111827;">{{ $tithe['name'] }}</td>
                        <td style="padding:10px 16px;font-family:monospace;color:#6b7280;font-size:12px;">{{ $tithe['id'] }}</td>
                        <td style="padding:10px 16px;color:#6b7280;">{{ $tithe['count'] }}</td>
                        <td style="padding:10px 16px;font-weight:700;color:#16a34a;text-align:right;">
                            GH₵ {{ number_format($tithe['total'], 2) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr style="background:#f0fdf4;border-top:2px solid #bbf7d0;">
                    <td colspan="4" style="padding:10px 16px;font-weight:700;color:#15803d;font-size:13px;">Total Tithes</td>
                    <td style="padding:10px 16px;font-weight:800;color:#16a34a;text-align:right;font-size:14px;">
                        GH₵ {{ number_format($incomeByCategory->get('tithe')['total'] ?? 0, 2) }}
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    @endif


    {{-- Income table --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;margin-bottom:1.5rem;">
        <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;background:#f0fdf4;">
            <h3 style="font-size:14px;font-weight:600;color:#111827;">Income transactions ({{ count($income) }})</h3>
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">Date</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">Member</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">Category</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">Amount</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">GHS</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">Method</th>
            </tr>
            </thead>
            <tbody>
            @foreach($income as $record)
                <tr style="border-top:1px solid #f3f4f6;">
                    <td style="padding:10px 16px;color:#6b7280;">{{ $record->payment_date->format('d M Y') }}</td>
                    <td style="padding:10px 16px;font-weight:500;color:#111827;">{{ $record->member?->full_name ?? 'Anonymous' }}</td>
                    <td style="padding:10px 16px;color:#374151;">{{ ucfirst($record->category) }}</td>
                    <td style="padding:10px 16px;font-weight:600;color:#16a34a;">{{ $record->currency }} {{ number_format($record->amount, 2) }}</td>
                    <td style="padding:10px 16px;color:#374151;">GH₵ {{ number_format($record->amount_ghs, 2) }}</td>
                    <td style="padding:10px 16px;color:#9ca3af;">{{ ucwords(str_replace('_', ' ', $record->payment_method)) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Expenses table --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;background:#fef2f2;">
            <h3 style="font-size:14px;font-weight:600;color:#111827;">Expense transactions ({{ count($expenses) }})</h3>
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">Date</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">Description</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">Category</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">Amount</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">GHS</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">Payee</th>
            </tr>
            </thead>
            <tbody>
            @foreach($expenses as $record)
                <tr style="border-top:1px solid #f3f4f6;">
                    <td style="padding:10px 16px;color:#6b7280;">{{ $record->expense_date->format('d M Y') }}</td>
                    <td style="padding:10px 16px;font-weight:500;color:#111827;">{{ $record->description }}</td>
                    <td style="padding:10px 16px;color:#374151;">{{ ucfirst($record->category) }}</td>
                    <td style="padding:10px 16px;font-weight:600;color:#dc2626;">{{ $record->currency }} {{ number_format($record->amount, 2) }}</td>
                    <td style="padding:10px 16px;color:#374151;">GH₵ {{ number_format($record->amount_ghs, 2) }}</td>
                    <td style="padding:10px 16px;color:#9ca3af;">{{ $record->payee ?? '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection
