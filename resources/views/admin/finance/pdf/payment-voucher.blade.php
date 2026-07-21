<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body  { font-family: Arial, sans-serif; font-size: 12px; color: #111827; margin: 0; padding: 24px; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #2563eb; padding-bottom: 14px; }
        h1    { font-size: 20px; color: #2563eb; margin: 0 0 4px; }
        .sub  { font-size: 12px; color: #6b7280; }
        .pv-number { font-size: 16px; font-weight: 700; font-family: monospace; margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        td, th { padding: 8px 10px; font-size: 12px; }
        .field-label { color: #6b7280; width: 40%; }
        .field-value { font-weight: 600; }
        .amount-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 16px; text-align: center; margin-bottom: 20px; }
        .amount-value { font-size: 22px; font-weight: 800; color: #2563eb; }
        .signatures { margin-top: 60px; }
        .sig-row { display: flex; }
        .sig-box { flex: 1; text-align: center; padding: 0 20px; }
        .sig-line { border-top: 1px solid #111827; margin-top: 40px; padding-top: 6px; font-size: 11px; color: #374151; }
        .footer { margin-top: 30px; font-size: 10px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        .letterhead-logo { width: 48px; height: 48px; object-fit: cover; border-radius: 8px; margin: 0 auto 8px; display: block; }
    </style>
</head>
<body>

<div class="header">
    @if(config('church.logo_path'))
        <img class="letterhead-logo" src="{{ Storage::disk('public')->path(config('church.logo_path')) }}" alt="Logo">
    @endif
    <h1>{{ config('app.name') }}</h1>
    @if(config('church.address'))
        <p class="pv-number">{{ config('church.address') }}</p>
    @endif
    <p class="pv-number">Payment Voucher</p>
    <p class="sub">{{ $financialRequest->pv_number }}</p>
</div>

<div class="amount-box">
    <div style="font-size:11px;color:#2563eb;margin-bottom:4px;">Amount</div>
    <div class="amount-value">{{ $financialRequest->currency }} {{ number_format($financialRequest->amount, 2) }}</div>
</div>

<table>
    <tr><td class="field-label">Payee</td><td class="field-value">{{ $financialRequest->payee ?? '—' }}</td></tr>
    <tr><td class="field-label">Description</td><td class="field-value">{{ $financialRequest->description }}</td></tr>
    <tr><td class="field-label">Category</td><td class="field-value">{{ ucfirst(str_replace('_', ' ', $financialRequest->category)) }}</td></tr>
    <tr><td class="field-label">Payment method</td><td class="field-value">{{ ucwords(str_replace('_', ' ', $financialRequest->payment_method)) }}</td></tr>
    <tr><td class="field-label">Request date</td><td class="field-value">{{ $financialRequest->request_date->format('d M Y') }}</td></tr>
    <tr><td class="field-label">Requested by</td><td class="field-value">{{ $financialRequest->requestedBy?->name ?? '—' }}</td></tr>
    <tr><td class="field-label">Approved by (Pastor)</td><td class="field-value">{{ $financialRequest->pastorApprovedBy?->name ?? '—' }} — {{ optional($financialRequest->pastor_approved_at)->format('d M Y') }}</td></tr>
    <tr><td class="field-label">Approved by (Finance Chairman)</td><td class="field-value">{{ $financialRequest->superAdminApprovedBy?->name ?? '—' }} — {{ optional($financialRequest->super_admin_approved_at)->format('d M Y') }}</td></tr>
    <tr><td class="field-label">PV generated</td><td class="field-value">{{ $financialRequest->pv_generated_at->format('d M Y, h:i A') }}</td></tr>
</table>

<div class="signatures">
    <div class="sig-row">
        <div class="sig-box">
            <div class="sig-line">Recipient's signature</div>
        </div>
    </div>
</div>

<div class="footer">
    Generated {{ now()->format('d M Y, h:i A') }} — {{ $financialRequest->pv_number }}
</div>

</body>
</html>
