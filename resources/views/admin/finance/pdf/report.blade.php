<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body  { font-family: Arial, sans-serif; font-size: 11px; color: #111827; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2563eb; padding-bottom: 14px; }
        h1    { font-size: 20px; color: #2563eb; margin: 0 0 4px; }
        .sub  { font-size: 12px; color: #6b7280; }
        .summary { display: flex; gap: 16px; margin-bottom: 20px; }
        .sum-card { flex: 1; padding: 12px; border-radius: 8px; text-align: center; }
        .sum-label { font-size: 11px; margin-bottom: 4px; }
        .sum-value { font-size: 18px; font-weight: 800; }
        h2    { font-size: 13px; font-weight: 700; margin: 16px 0 8px; color: #374151; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th    { padding: 7px 10px; text-align: left; font-size: 10px; font-weight: 600; }
        td    { padding: 6px 10px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
        .income-header th  { background: #16a34a; color: white; }
        .expense-header th { background: #dc2626; color: white; }
        .total-row td { font-weight: 700; background: #f9fafb; }
        .footer { margin-top: 20px; font-size: 10px; color: #9ca3af; text-align: right; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Church Finance Report</h1>
    <p class="sub">Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
    <p class="sub">Generated: {{ now()->format('d M Y, h:i A') }}</p>
</div>

{{-- Summary --}}
<table style="margin-bottom:20px;">
    <tr>
        <td style="padding:10px 16px;background:#f0fdf4;border-radius:8px;text-align:center;border:1px solid #bbf7d0;">
            <div style="font-size:11px;color:#15803d;margin-bottom:4px;">Total Income</div>
            <div style="font-size:18px;font-weight:800;color:#16a34a;">GHS {{ number_format($totalIncome, 2) }}</div>
        </td>
        <td style="width:10px;"></td>
        <td style="padding:10px 16px;background:#fef2f2;border-radius:8px;text-align:center;border:1px solid #fecaca;">
            <div style="font-size:11px;color:#dc2626;margin-bottom:4px;">Total Expenses</div>
            <div style="font-size:18px;font-weight:800;color:#dc2626;">GHS {{ number_format($totalExpenses, 2) }}</div>
        </td>
        <td style="width:10px;"></td>
        <td style="padding:10px 16px;background:#eff6ff;border-radius:8px;text-align:center;border:1px solid #bfdbfe;">
            <div style="font-size:11px;color:#2563eb;margin-bottom:4px;">Net {{ $netBalance >= 0 ? 'Surplus' : 'Deficit' }}</div>
            <div style="font-size:18px;font-weight:800;color:{{ $netBalance >= 0 ? '#2563eb' : '#dc2626' }};">GHS {{ number_format(abs($netBalance), 2) }}</div>
        </td>
    </tr>
</table>

{{-- Income category summary in PDF --}}
<h2>Income Summary by Category</h2>
<table>
    <thead class="income-header">
    <tr><th>Category</th><th>Records</th><th style="text-align:right;">Amount (GHS)</th></tr>
    </thead>
    <tbody>
    @foreach($incomeByCategory as $data)
        @if($data['total'] > 0)
            <tr>
                <td>{{ $data['label'] }}</td>
                <td>{{ $data['count'] }}</td>
                <td style="text-align:right;font-weight:700;color:#16a34a;">GHS {{ number_format($data['total'], 2) }}</td>
            </tr>
        @endif
    @endforeach
    <tr class="total-row">
        <td colspan="2" style="text-align:right;">Total Income</td>
        <td style="text-align:right;">GHS {{ number_format($totalIncome, 2) }}</td>
    </tr>
    </tbody>
</table>

{{-- Expense category summary in PDF --}}
<h2>Expense Summary by Category</h2>
<table>
    <thead class="expense-header">
    <tr><th>Category</th><th>Records</th><th style="text-align:right;">Amount (GHS)</th></tr>
    </thead>
    <tbody>
    @foreach($expenseByCategory as $data)
        @if($data['total'] > 0)
            <tr>
                <td>{{ $data['label'] }}</td>
                <td>{{ $data['count'] }}</td>
                <td style="text-align:right;font-weight:700;color:#dc2626;">GHS {{ number_format($data['total'], 2) }}</td>
            </tr>
        @endif
    @endforeach
    <tr class="total-row">
        <td colspan="2" style="text-align:right;">Total Expenses</td>
        <td style="text-align:right;">GHS {{ number_format($totalExpenses, 2) }}</td>
    </tr>
    </tbody>
</table>

<div class="footer">{{ config('app.name', 'Church Management System') }} · Finance Report · Confidential</div>

</body>
</html>
