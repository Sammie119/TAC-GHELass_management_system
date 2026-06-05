<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background: white;
        }

        .card {
            width: 85.6mm;
            height: 54mm;
            position: relative;
            overflow: hidden;
            page-break-after: always;
        }

        /* ── FRONT ─────────────────────────────── */
        .header {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 60%, #3b82f6 100%);
            height: 16mm;
            padding: 3mm 4mm;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .circle-1 {
            position: absolute; top: -8mm; right: -6mm;
            width: 24mm; height: 24mm; border-radius: 50%;
            background: rgba(255,255,255,0.08);
        }

        .circle-2 {
            position: absolute; top: -4mm; right: 2mm;
            width: 14mm; height: 14mm; border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }

        .church-name { color: white; font-size: 9pt; font-weight: bold; letter-spacing: 0.5px; }
        .card-label  { color: rgba(255,255,255,0.8); font-size: 6pt; text-transform: uppercase; letter-spacing: 1px; }

        .body {
            padding: 3mm 4mm;
            display: flex;
            gap: 3mm;
            align-items: flex-start;
        }

        .avatar {
            width: 14mm; height: 14mm; border-radius: 50%;
            background: #dbeafe;
            display: flex; align-items: center; justify-content: center;
            font-size: 14pt; font-weight: bold; color: #1d4ed8;
            flex-shrink: 0; border: 1.5px solid #bfdbfe;
        }

        .info { flex: 1; }
        .member-name { font-size: 11pt; font-weight: bold; color: #111827; margin-bottom: 1mm; line-height: 1.2; }
        .member-id   { font-size: 8pt; color: #2563eb; font-family: 'Courier New', monospace; font-weight: bold; margin-bottom: 1.5mm; }
        .detail-row  { font-size: 6.5pt; color: #6b7280; margin-bottom: 0.8mm; }
        .detail-label{ font-weight: bold; color: #374151; }

        .qr-section  { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; }
        .qr-code     { width: 18mm; height: 18mm; }
        .qr-label    { font-size: 5pt; color: #9ca3af; margin-top: 0.5mm; text-align: center; }

        .footer {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: #1e3a8a; height: 5mm;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 4mm;
        }

        .footer-text { color: rgba(255,255,255,0.7); font-size: 5pt; letter-spacing: 0.5px; }

        /* ── BACK ──────────────────────────────── */
        .card-back {
            width: 85.6mm;
            height: 54mm;
            position: relative;
            overflow: hidden;
            background: white;
        }

        .back-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
            height: 12mm;
            display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }

        .back-header-text {
            color: white; font-size: 9pt; font-weight: bold;
            letter-spacing: 1px; text-transform: uppercase;
        }

        .back-body {
            padding: 3mm 4mm;
        }

        .back-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5mm 0;
            border-bottom: 0.2mm solid #f3f4f6;
            font-size: 6.5pt;
        }

        .back-row:last-child { border-bottom: none; }

        .back-label { color: #9ca3af; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .back-value { color: #111827; font-weight: 500; text-align: right; }

        .back-qr-section {
            position: absolute;
            right: 4mm;
            top: 14mm;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .back-qr { width: 20mm; height: 20mm; }
        .back-qr-label { font-size: 4.5pt; color: #9ca3af; text-align: center; margin-top: 0.5mm; }

        .back-info { padding-right: 25mm; }

        .back-footer {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: #1e3a8a; height: 6mm;
            display: flex; align-items: center; justify-content: center;
            padding: 0 4mm;
        }

        .back-footer-text {
            color: rgba(255,255,255,0.7); font-size: 5pt;
            letter-spacing: 0.5px; text-align: center;
        }

        .barcode-row {
            margin-top: 2mm;
            text-align: center;
        }

        .member-id-large {
            font-family: 'Courier New', monospace;
            font-size: 9pt;
            font-weight: bold;
            color: #1d4ed8;
            letter-spacing: 2px;
        }

        .tagline {
            font-size: 5.5pt;
            color: #9ca3af;
            margin-top: 1mm;
            font-style: italic;
        }
    </style>
</head>
<body>

{{-- ══ FRONT ══════════════════════════════════════════════ --}}
<div class="card">

    <div class="header">
        <div class="circle-1"></div>
        <div class="circle-2"></div>
        <div>
            <div class="church-name">{{ config('app.name', 'Church Management System') }}</div>
            <div class="card-label">Member Identification Card</div>
        </div>
        <svg style="width:8mm;height:8mm;opacity:0.85;" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    </div>

    <div class="body">
        <div class="avatar">
            {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
        </div>
        <div class="info">
            <div class="member-name">{{ strtoupper($member->full_name) }}</div>
            <div class="member-id">{{ $member->member_id_card }}</div>
            @if($member->phone)
                <div class="detail-row"><span class="detail-label">TEL:</span> {{ $member->phone }}</div>
            @endif
            @if($member->gender)
                <div class="detail-row"><span class="detail-label">GENDER:</span> {{ strtoupper($member->gender) }}</div>
            @endif
            <div class="detail-row"><span class="detail-label">JOINED:</span> {{ $member->created_at->format('d M Y') }}</div>
            <div class="detail-row">
                <span class="detail-label">STATUS:</span>
                <span style="color:#16a34a;font-weight:bold;">{{ strtoupper($member->status) }}</span>
            </div>
        </div>
        <div class="qr-section">
            <img src="data:image/svg+xml;base64,{{ $qrBase64 }}" class="qr-code" alt="QR">
            <div class="qr-label">SCAN TO CHECK IN</div>
        </div>
    </div>

    <div class="footer">
        <span class="footer-text">VALID MEMBER CARD — DO NOT TRANSFER</span>
        <span class="footer-text">{{ now()->format('Y') }}</span>
    </div>

</div>

{{-- ══ BACK ═══════════════════════════════════════════════ --}}
<div class="card-back">

    <div class="back-header">
        <div class="circle-1"></div>
        <div class="circle-2"></div>
        <span class="back-header-text">Member Details</span>
    </div>

    <div class="back-body">
        <div class="back-info">

            <div class="back-row">
                <span class="back-label">Full Name</span>
                <span class="back-value">{{ $member->full_name }}</span>
            </div>

            <div class="back-row">
                <span class="back-label">Member ID</span>
                <span class="back-value" style="font-family:'Courier New',monospace;color:#2563eb;font-weight:bold;">
                    {{ $member->member_id_card }}
                </span>
            </div>

            @if($member->phone)
                <div class="back-row">
                    <span class="back-label">Phone</span>
                    <span class="back-value">{{ $member->phone }}</span>
                </div>
            @endif

            @if($member->email)
                <div class="back-row">
                    <span class="back-label">Email</span>
                    <span class="back-value" style="font-size:6pt;">{{ $member->email }}</span>
                </div>
            @endif

            @if($member->address)
                <div class="back-row">
                    <span class="back-label">Address</span>
                    <span class="back-value" style="font-size:6pt;">{{ $member->address }}</span>
                </div>
            @endif

            <div class="back-row">
                <span class="back-label">Date of Birth</span>
                <span class="back-value">
                    {{ $member->date_of_birth
                        ? \Carbon\Carbon::parse($member->date_of_birth)->format('d M Y')
                        : '—' }}
                </span>
            </div>

        </div>

        {{-- QR on back --}}
        <div class="back-qr-section">
            <img src="data:image/svg+xml;base64,{{ $qrBase64 }}" class="back-qr" alt="QR">
            <div class="back-qr-label">SELF CHECK-IN</div>
        </div>

    </div>

    <div class="back-footer">
        <span class="back-footer-text">
            If found, please return to the church office ·
            This card is the property of {{ config('app.name', 'Church Management System') }}
        </span>
    </div>

</div>

</body>
</html>
