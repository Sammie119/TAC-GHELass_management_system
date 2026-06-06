<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You — {{ config('app.name') }}</title>
    {{-- Favicons --}}
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="shortcut icon" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/favicon.svg">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 60%, #7c3aed 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 1.5rem;
        }
        .card {
            background: white; border-radius: 24px;
            width: 100%; max-width: 420px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.2);
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .success-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg,#16a34a,#22c55e);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(22,163,74,0.3);
        }
        h1 { font-size: 24px; font-weight: 800; color: #111827; margin-bottom: 8px; }
        .sub { font-size: 14px; color: #6b7280; line-height: 1.7; margin-bottom: 24px; }
        .receipt {
            background: #f9fafb; border: 1px solid #e5e7eb;
            border-radius: 14px; padding: 18px; margin-bottom: 24px;
            text-align: left;
        }
        .receipt-row {
            display: flex; justify-content: space-between;
            padding: 8px 0; border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }
        .receipt-row:last-child { border-bottom: none; }
        .receipt-label { color: #9ca3af; }
        .receipt-value { font-weight: 600; color: #111827; }
        .ref {
            font-family: monospace; font-size: 13px;
            background: #eff6ff; color: #2563eb;
            padding: 4px 10px; border-radius: 6px;
        }
        .btn {
            display: block; width: 100%;
            background: linear-gradient(135deg,#2563eb,#4f46e5);
            color: white; border: none; border-radius: 12px;
            padding: 13px; font-size: 15px; font-weight: 700;
            cursor: pointer; text-decoration: none;
            margin-bottom: 10px;
        }
        .btn-outline {
            display: block; width: 100%;
            border: 1.5px solid #e5e7eb; border-radius: 12px;
            padding: 12px; font-size: 14px; font-weight: 500;
            color: #374151; text-decoration: none; background: white;
        }
        .verse {
            margin-top: 20px; font-size: 13px;
            color: #9ca3af; font-style: italic; line-height: 1.6;
        }
    </style>
</head>
<body>
<div class="card">

    <div class="success-icon">
        <svg style="width:40px;height:40px;" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1>Thank you, <br>{{ $name }}!</h1>
    <br>
{{--    <p class="sub">--}}
{{--        Your giving request has been submitted successfully.--}}
{{--    </p>--}}

    {{-- Receipt --}}
    <div class="receipt">
        <div class="receipt-row">
            <span class="receipt-label">Reference</span>
            <span class="ref">{{ $ref }}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Category</span>
            <span class="receipt-value">{{ ucfirst($category) }}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Amount</span>
            <span class="receipt-value" style="color:#16a34a;font-size:16px;">
                {{ $currency }} {{ number_format($amount, 2) }}
            </span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Status</span>
            <span style="background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                confirmed
            </span>
        </div>
    </div>

    <a href="{{ route('give.show') }}" class="btn">Give again</a>
    <a href="{{ url('/') }}" class="btn-outline">← Back to home</a>

    <p class="verse">
        "Each of you should give what you have decided in your heart to give,
        not reluctantly or under compulsion, for God loves a cheerful giver."
        <br>— 2 Corinthians 9:7
    </p>

</div>
</body>
</html>
