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
        .footer-note { text-align: center; font-size: 10px; color: #374151; margin-top: 20px; font-style: italic; }

        .signatures { margin-top: 50px; width: 100%; }
        .sig-table { width: 100%; border-collapse: collapse; margin-bottom: 26px; }
        .sig-table td { font-size: 11px; padding: 0; vertical-align: bottom; }
        .sig-label-col { width: 55%; }
        .sig-sign-col { width: 45%; }
        .sig-dots { border-bottom: 1px dotted #6b7280; display: inline-block; width: 220px; }
    </style>
</head>
<body>

<div class="header">
    @if(config('church.logo_path'))
        <img class="letterhead-logo" src="{{ Storage::disk('public')->path(config('church.logo_path')) }}" alt="Logo">
    @endif
    <div class="title">
        <h1 style="font-size: 25px">THE APOSTOLIC CHURCH-GHANA</h1>
        <p class="form-tag" style="margin: 0">GENERAL HEADQUARTERS, P.O. BOX GP 633, ACCRA.</p>
        <p class="form-tag" style="margin: 0">WEBSITE: www.theapostolicchurch.org.gh</p>
        <p class="form-tag" style="margin: 0">Email: headquarters@tacmail.org, Tel: +233 (0)55 256 9990, +233 (0)54 012 7001</p>
    </div>
</div>

<div class="title">
    <h3>MONTHLY TITHES REMITTANCES STATEMENT</h3>
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

    <tr class="grand-row">
        <td class="label"><br></td>
        <td></td>
        <td class="num"></td>
        <td class="num"></td>
        <td class="num"></td>
        <td></td>
    </tr>
    <tr class="grand-row">
        <td class="label">Less {{ $retention_pct }}% Retention</td>
        <td></td>
        <td class="num"></td>
        <td class="num"></td>
        <td class="num">{{ number_format($retention_amount, 2) }}</td>
        <td></td>
    </tr>
    <tr class="grand-row">
        <td class="label">Net Tithes Remitted</td>
        <td></td>
        <td class="num"></td>
        <td class="num"></td>
        <td class="num">{{ number_format($net_remitted, 2) }}</td>
        <td></td>
    </tr>
    </tbody>
</table>

<div class="signatures">
    <table class="sig-table">
        <tr>
            <td class="sig-label-col">Presiding Elder/Pastor:<span class="sig-dots">&nbsp;</span></td>
            <td class="sig-sign-col">Signature:<span class="sig-dots" style="width:120px;">&nbsp;</span></td>
        </tr>
    </table>
    <table class="sig-table">
        <tr>
            <td class="sig-label-col">Finance Chairperson:<span class="sig-dots">&nbsp;</span></td>
            <td class="sig-sign-col">Signature:<span class="sig-dots" style="width:120px;">&nbsp;</span></td>
        </tr>
    </table>
</div>

<p class="footer-note">(A copy of this report shall be filled and kept at the local office)</p>

</body>
</html>
