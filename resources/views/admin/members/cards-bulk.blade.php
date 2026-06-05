<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background: white;
            padding: 8mm;
        }

        .page-title {
            font-size: 9pt; color: #9ca3af;
            margin-bottom: 5mm; text-align: center;
            letter-spacing: 1px; text-transform: uppercase;
        }

        /* 2 columns × 4 rows = 8 fronts per page, then 8 backs per page */
        .cards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4mm;
        }

        .card {
            width: 85.6mm;
            height: 54mm;
            border: 0.3mm solid #e5e7eb;
            border-radius: 3mm;
            position: relative;
            overflow: hidden;
            page-break-inside: avoid;
            background: white;
        }

        /* ── FRONT styles ── */
        .header {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 60%, #3b82f6 100%);
            height: 14mm; padding: 2.5mm 3.5mm;
            display: flex; align-items: center; justify-content: space-between;
            position: relative; overflow: hidden;
        }

        .header-circle-1 {
            position:absolute;top:-6mm;right:-5mm;
            width:20mm;height:20mm;border-radius:50%;
            background:rgba(255,255,255,0.07);
        }

        .church-name { color:white;font-size:8pt;font-weight:bold; }
        .card-label  { color:rgba(255,255,255,0.75);font-size:5.5pt;text-transform:uppercase;letter-spacing:0.8px; }

        .body {
            padding: 2.5mm 3.5mm;
            display: flex; gap: 2.5mm; align-items: flex-start;
        }

        .avatar {
            width:13mm;height:13mm;border-radius:50%;
            background:#dbeafe;
            display:flex;align-items:center;justify-content:center;
            font-size:12pt;font-weight:bold;color:#1d4ed8;
            flex-shrink:0;border:1px solid #bfdbfe;
        }

        .info { flex:1; }
        .member-name  { font-size:9.5pt;font-weight:bold;color:#111827;margin-bottom:0.8mm;line-height:1.2; }
        .member-id    { font-size:7.5pt;color:#2563eb;font-family:'Courier New',monospace;font-weight:bold;margin-bottom:1mm; }
        .detail-row   { font-size:6pt;color:#6b7280;margin-bottom:0.5mm; }
        .detail-label { font-weight:bold;color:#374151; }

        .qr-section { display:flex;flex-direction:column;align-items:center;flex-shrink:0; }
        .qr-code    { width:16mm;height:16mm; }
        .qr-label   { font-size:4.5pt;color:#9ca3af;margin-top:0.5mm;text-align:center; }

        .card-footer {
            position:absolute;bottom:0;left:0;right:0;
            background:#1e3a8a;height:4.5mm;
            display:flex;align-items:center;justify-content:space-between;
            padding:0 3.5mm;
        }

        .footer-text { color:rgba(255,255,255,0.65);font-size:4.5pt; }

        /* ── BACK styles ── */
        .card-back {
            width: 85.6mm;
            height: 54mm;
            border: 0.3mm solid #e5e7eb;
            border-radius: 3mm;
            position: relative;
            overflow: hidden;
            background: white;
            page-break-inside: avoid;
        }

        .back-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
            height: 11mm;
            display: flex; align-items: center; justify-content: center;
        }

        .back-header-text {
            color: white; font-size: 8pt; font-weight: bold;
            letter-spacing: 1px; text-transform: uppercase;
        }

        .back-body { padding: 2.5mm 3.5mm; position: relative; }

        .back-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1.2mm 0; border-bottom: 0.2mm solid #f3f4f6;
            font-size: 6pt;
        }

        .back-row:last-child { border-bottom: none; }
        .back-label { color: #9ca3af; font-weight: bold; text-transform: uppercase; }
        .back-value { color: #111827; font-weight: 500; text-align: right; }

        .back-info { padding-right: 22mm; }

        .back-qr-area {
            position: absolute; right: 3mm; top: 0;
            display: flex; flex-direction: column; align-items: center;
        }

        .back-qr      { width: 18mm; height: 18mm; }
        .back-qr-text { font-size: 4pt; color: #9ca3af; text-align: center; margin-top: 0.3mm; }

        .back-footer {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: #1e3a8a; height: 5mm;
            display: flex; align-items: center; justify-content: center;
            padding: 0 3.5mm;
        }

        .back-footer-text {
            color: rgba(255,255,255,0.65); font-size: 4.5pt;
            letter-spacing: 0.3px; text-align: center;
        }

        /* Page break after every 8 cards */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

{{-- ══ PAGE 1+: FRONTS ════════════════════════════════════ --}}
<div class="page-title">Member ID Cards — Front — {{ now()->format('d M Y') }}</div>

@foreach($members->chunk(8) as $chunkIndex => $chunk)

    @if($chunkIndex > 0)
        <div class="page-break"></div>
        <div class="page-title">Member ID Cards — Front (continued)</div>
    @endif

    <div class="cards-grid">
        @foreach($chunk as $member)
            <div class="card">

                <div class="header">
                    <div class="header-circle-1"></div>
                    <div>
                        <div class="church-name">{{ config('app.name', 'Church Management System') }}</div>
                        <div class="card-label">Member Identification Card</div>
                    </div>
                    <svg style="width:7mm;height:7mm;opacity:0.8;" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24">
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
                            <span style="color:#16a34a;font-weight:bold;">ACTIVE</span>
                        </div>
                    </div>
                    <div class="qr-section">
                        <img src="data:image/svg+xml;base64,{{ $member->qr_base64 }}" class="qr-code" alt="QR">
                        <div class="qr-label">SCAN TO CHECK IN</div>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="footer-text">VALID MEMBER CARD — DO NOT TRANSFER</span>
                    <span class="footer-text">{{ now()->format('Y') }}</span>
                </div>

            </div>
        @endforeach
    </div>

    {{-- ── BACKS for this chunk ── --}}
    <div class="page-break"></div>
    <div class="page-title">Member ID Cards — Back</div>

    <div class="cards-grid">
        @foreach($chunk as $member)
            <div class="card-back">

                <div class="back-header">
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
                                <span class="back-value" style="font-size:5.5pt;">{{ $member->email }}</span>
                            </div>
                        @endif
                        @if($member->address)
                            <div class="back-row">
                                <span class="back-label">Address</span>
                                <span class="back-value" style="font-size:5.5pt;">{{ $member->address }}</span>
                            </div>
                        @endif
                        <div class="back-row">
                            <span class="back-label">DOB</span>
                            <span class="back-value">
                            {{ $member->date_of_birth
                                ? \Carbon\Carbon::parse($member->date_of_birth)->format('d M Y')
                                : '—' }}
                        </span>
                        </div>
                    </div>

                    <div class="back-qr-area">
                        <img src="data:image/svg+xml;base64,{{ $member->qr_base64 }}" class="back-qr" alt="QR">
                        <div class="back-qr-text">SELF CHECK-IN</div>
                    </div>

                </div>

                <div class="back-footer">
                <span class="back-footer-text">
                    If found, return to church office · Property of {{ config('app.name', 'Church Management System') }}
                </span>
                </div>

            </div>
        @endforeach
    </div>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif

@endforeach

</body>
</html>
