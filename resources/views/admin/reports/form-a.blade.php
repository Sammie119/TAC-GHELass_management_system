@extends('layouts.admin')
@section('page-title', 'Form A — Monthly Tithes Remittances Statement')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Form A — Monthly Tithes Remittances Statement</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">
                Period: {{ $period_start->format('d M Y') }} — {{ $period_end->format('d M Y') }}
            </p>
        </div>
        <a href="{{ route('admin.reports.form-a.pdf', ['year' => $year, 'month' => $month]) }}"
           style="background:#2563eb;color:white;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
            Download PDF
        </a>
    </div>

    {{-- Month/year filter --}}
    <form method="GET" action="{{ route('admin.reports.form-a') }}"
          style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:16px 20px;margin-bottom:1.5rem;display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        <div>
            <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:5px;">Month</label>
            <select name="month" onchange="this.form.submit()"
                    style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m === $month ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(2000, $m, 1)->format('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:5px;">Year</label>
            <input type="number" name="year" value="{{ $year }}" onchange="this.form.submit()"
                   style="width:100px;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;box-sizing:border-box;">
        </div>
        <p style="font-size:12px;color:#9ca3af;margin:0;">
            Reporting month runs from the second Sunday of the selected month through the first Sunday of the following month.
        </p>
    </form>

    {{-- Preview table --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;margin-bottom:1.5rem;">
        <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;">
            <p style="font-size:13px;color:#6b7280;"><strong>Name of Assembly:</strong> {{ $assembly_name }}</p>
            <p style="font-size:13px;color:#6b7280;"><strong>District:</strong> {{ $district_name }}</p>
            <p style="font-size:13px;color:#6b7280;"><strong>Month/Year:</strong> {{ $month_label }} {{ $year }}</p>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="background:#f9fafb;">
                <tr>
                    <th style="padding:10px 12px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;">Sunday</th>
                    <th style="padding:10px 12px;text-align:right;font-size:11px;color:#6b7280;font-weight:500;">Total Membership<br>(Adults ≥20yrs)<br>A</th>
                    <th style="padding:10px 12px;text-align:right;font-size:11px;color:#6b7280;font-weight:500;">Tithe (GH₵)<br>B</th>
                    <th style="padding:10px 12px;text-align:right;font-size:11px;color:#6b7280;font-weight:500;">Offerings (GH₵)<br>C</th>
                    <th style="padding:10px 12px;text-align:right;font-size:11px;color:#6b7280;font-weight:500;">Total (GH₵)<br>D = (B + C)</th>
                    <th style="padding:10px 12px;text-align:right;font-size:11px;color:#6b7280;font-weight:500;">Tithes Per Capita (GH₵)<br>E = (D / A)</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr style="border-top:1px solid #f3f4f6;">
                        <td style="padding:8px 12px;">{{ $row['label'] }} ({{ $row['date']->format('d-M-Y') }})</td>
                        <td style="padding:8px 12px;text-align:right;">{{ number_format($row['membership']) }}</td>
                        <td style="padding:8px 12px;text-align:right;">{{ number_format($row['tithe'], 2) }}</td>
                        <td style="padding:8px 12px;text-align:right;">{{ number_format($row['offering'], 2) }}</td>
                        <td style="padding:8px 12px;text-align:right;">{{ number_format($row['total'], 2) }}</td>
                        <td style="padding:8px 12px;text-align:right;">{{ number_format($row['per_capita'], 2) }}</td>
                    </tr>
                @endforeach
                <tr style="border-top:2px solid #e5e7eb;font-weight:700;">
                    <td style="padding:8px 12px;">GRAND TOTAL</td>
                    <td style="padding:8px 12px;"></td>
                    <td style="padding:8px 12px;text-align:right;">{{ number_format($grand_tithe, 2) }}</td>
                    <td style="padding:8px 12px;text-align:right;">{{ number_format($grand_offering, 2) }}</td>
                    <td style="padding:8px 12px;text-align:right;">{{ number_format($grand_total, 2) }}</td>
                    <td style="padding:8px 12px;"></td>
                </tr>
                <tr style="border-top:1px solid #f3f4f6;">
                    <td style="padding:8px 12px;color:#374151;">Less {{ $retention_pct }}% Retention</td>
                    <td colspan="3"></td>
                    <td style="padding:8px 12px;text-align:right;font-style:italic;">{{ number_format($retention_amount, 2) }}</td>
                    <td></td>
                </tr>
                <tr style="border-top:1px solid #f3f4f6;font-weight:700;">
                    <td style="padding:8px 12px;">Net Tithes Remitted</td>
                    <td colspan="3"></td>
                    <td style="padding:8px 12px;text-align:right;font-style:italic;">{{ number_format($net_remitted, 2) }}</td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <p style="font-size:12px;color:#9ca3af;">
        Membership count excludes any active member without a date of birth on file. Check Members if a Sunday's count looks off.
    </p>

@endsection
