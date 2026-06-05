<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Tithe — Member Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f0f9ff; min-height: 100vh; }
        .nav {
            background: white; border-bottom: 1px solid #e5e7eb;
            padding: 0 1rem; height: 56px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 10;
        }
        .nav-back { display: flex; align-items: center; gap: 8px; text-decoration: none; color: #374151; font-size: 14px; font-weight: 500; }
        .content { max-width: 480px; margin: 0 auto; padding: 1.5rem 1rem; }
        .card { background: white; border-radius: 16px; border: 1px solid #e5e7eb; padding: 24px; margin-bottom: 1rem; }
        h2 { font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .sub { font-size: 13px; color: #6b7280; margin-bottom: 1.5rem; }
        label { display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; }
        input, select, textarea {
            width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px;
            padding: 12px 14px; font-size: 15px; outline: none; color: #111827;
            margin-bottom: 14px; transition: border-color 0.15s;
        }
        input:focus, select:focus, textarea:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px #bfdbfe; }
        .btn { width: 100%; background: #16a34a; color: white; border: none; border-radius: 10px; padding: 14px; font-size: 16px; font-weight: 600; cursor: pointer; }
        .success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; padding: 12px 14px; border-radius: 10px; font-size: 14px; margin-bottom: 16px; }
        .momo-info { background: #fef3c7; border: 1px solid #fde68a; border-radius: 10px; padding: 14px; margin-bottom: 16px; font-size: 13px; color: #92400e; }
    </style>
</head>
<body>

<nav class="nav">
    <a href="{{ route('portal.dashboard') }}" class="nav-back">
        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back
    </a>
    <span style="font-size:15px;font-weight:600;color:#111827;">Make Payment</span>
    <span style="width:60px;"></span>
</nav>

<div class="content">

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <h2>Make a payment</h2>
        <p class="sub">Submit your tithe, offering or pledge online</p>

{{--        <div class="momo-info">--}}
{{--            📱 After submitting, our finance team will send a MoMo prompt to your phone to complete the payment.--}}
{{--        </div>--}}

        <form method="POST" action="{{ route('portal.pay.submit') }}">
            @csrf

            <label>Payment type</label>
            <select name="category">
                @foreach(config('finance.online_categories') as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>

            <label>Amount (GHS)</label>
            <input type="number" name="amount" step="0.01" min="1" placeholder="0.00" required>

            <label>MoMo phone number</label>
            <input type="text" name="phone"
                   value="{{ $member->phone }}"
                   placeholder="e.g. 0244000001" required>

            <label>Notes (optional)</label>
            <textarea name="notes" rows="2" placeholder="e.g. January tithe" style="resize:none;"></textarea>

            <button type="submit" class="btn">Submit</button>
        </form>
    </div>

    <p style="font-size:12px;color:#9ca3af;text-align:center;">
        Your payment is processed by our finance team. You will receive a confirmation once it is verified.
    </p>

</div>
</body>
</html>
