<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body  { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #111827; margin: 0; padding: 24px; }
        .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #111827; padding-bottom: 12px; }
        .letterhead-logo { width: 48px; height: 48px; object-fit: cover; border-radius: 8px; margin: 0 auto 8px; display: block; }
        h1 { font-size: 15px; margin: 0 0 4px; }
        .sub { font-size: 10px; color: #374151; }
        .title { text-align: center; margin: 18px 0 4px; }
        .title h2 { font-size: 16px; margin: 0; letter-spacing: 0.5px; }
        .title .form-tag { font-size: 12px; font-weight: 700; margin-top: 2px; }
        .fields { margin: 16px 0; width: 100%; }
        .fields td { padding: 4px 0; font-size: 11px; }
        .fields .underline { border-bottom: 1px dotted #6b7280; padding: 0 6px; }
        table.form-table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        table.form-table th, table.form-table td { border: 1px solid #111827; padding: 6px 8px; font-size: 10px; }
        table.form-table th { background: #f3f4f6; text-align: center; font-weight: 700; }
        table.form-table td.num { text-align: right; }
        table.form-table td.label { text-align: left; }
        .grand-row { font-weight: 700; }
        .retention-row td, .net-row td { border: none; padding: 4px 8px; font-size: 11px; }
        .net-row { font-weight: 700; font-style: italic; }
        .retention-row { font-style: italic; }
        .signatures { margin-top: 50px; }
        .sig-row { display: flex; justify-content: space-between; margin-bottom: 26px; }
        .sig-label { font-size: 11px; }
        .sig-dots { border-bottom: 1px dotted #6b7280; display: inline-block; width: 220px; }
        .footer-note { text-align: center; font-size: 10px; color: #374151; margin-top: 20px; font-style: italic; }
    </style>
</head>
<body>

<div class="header">
    @if(config('church.logo_path'))
        <img class="letterhead-logo" src="{{ Storage::disk('public')->path(config('church.logo_path')) }}" alt="Logo">
    @endif
    <h1>{{ config('app.name') }}</h1>
    @if(config('church.address'))
        <p class="sub">{{ config('church.address') }}</p>
    @endif
</div>

<div class="title">
    <h2>MONTHLY TITHES REMITTANCES STATEMENT</h2>
    <p class="form-tag">FORM A</p>
</div>

<table class="fields">
    <tr>
        <td style="width:60%;">NAME OF ASSEMBLY: <span class="underline">{{ $assembly_name }}</span></td>
        <td style="width:40%;">DISTRICT: <span class="underline">{{ $district_name }}</span></td>
    </tr>
    <tr>
        <td>MONTH: <span class="underline">{{ $month_label }}</span></td>
        <td>YEAR: <span class="underline">{{ $year }}</span></td>
    </tr>
</table>

<table class="form-table">
    <thead>
    <tr>
        <th style="width:22%;"></th>
        <th style="width:15%;">TOTAL MEMBERSHIP<br>(ADULTS &gt;= 20yrs)<br>A</th>
        <th style="width:15%;">TITHE<br>(GH₵)<br>B</th>
        <th style="width:15%;">OFFERINGS<br>(GH₵)<br>C</th>
        <th style="width:15%;">TOTAL<br>(GH₵)<br>D = (B + C)</th>
        <th style="width:18%;">TITHES PER CAPITAL<br>(GH₵)<br>E = (D / A)</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $row)
        <tr>
            <td class="label">{{ $row['label'] }} ({{ $row['date']->format('d-M-Y') }})</td>
            <td class="num">{{ number_format($row['membership']) }}</td>
            <td class="num">{{ number_format($row['tithe'], 2) }}</td>
            <td class="num">{{ number_format($row['offering'], 2) }}</td>
            <td class="num">{{ number_format($row['total'], 2) }}</td>
            <td class="num">{{ number_format($row['per_capita'], 2) }}</td>
        </tr>
    @endforeach
    <tr class="grand-row">
        <td class="label">GRAND TOTAL</td>
        <td></td>
        <td class="num">{{ number_format($grand_tithe, 2) }}</td>
        <td class="num">{{ number_format($grand_offering, 2) }}</td>
        <td class="num">{{ number_format($grand_total, 2) }}</td>
        <td></td>
    </tr>
    </tbody>
</table>

<table style="width:100%;border-collapse:collapse;">
    <tr class="retention-row">
        <td style="width:22%;"></td>
        <td style="width:52%;">Less {{ $retention_pct }}% Retention</td>
        <td style="width:26%;text-align:right;">{{ number_format($retention_amount, 2) }}</td>
    </tr>
    <tr class="net-row">
        <td></td>
        <td>Net Tithes Remitted</td>
        <td style="text-align:right;">{{ number_format($net_remitted, 2) }}</td>
    </tr>
</table>

<div class="signatures">
    <div class="sig-row">
        <div class="sig-label">Presiding Elder/Pastor:<span class="sig-dots">&nbsp;</span></div>
        <div class="sig-label">Signature:<span class="sig-dots" style="width:120px;">&nbsp;</span></div>
    </div>
    <div class="sig-row">
        <div class="sig-label">Finance Chairperson:<span class="sig-dots">&nbsp;</span></div>
        <div class="sig-label">Signature:<span class="sig-dots" style="width:120px;">&nbsp;</span></div>
    </div>
</div>

<p class="footer-note">(A copy of this report shall be filled and kept at the local office)</p>

</body>
</html>
