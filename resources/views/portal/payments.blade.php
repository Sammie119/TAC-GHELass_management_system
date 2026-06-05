<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Payments — Member Portal</title>
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
        .nav-title { font-size: 15px; font-weight: 600; color: #111827; }
        .content { max-width: 640px; margin: 0 auto; padding: 1.5rem 1rem; }

        {{-- Summary cards --}}
        .summary-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 12px; margin-bottom: 1.5rem; }
        .sum-card { background: white; border-radius: 12px; border: 1px solid #e5e7eb; padding: 16px; text-align: center; }
        .sum-value { font-size: 18px; font-weight: 800; color: #111827; margin-bottom: 4px; }
        .sum-label { font-size: 11px; color: #9ca3af; }

        .card { background: white; border-radius: 14px; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1rem; }
        .card-header { padding: 14px 16px; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; }
        .card-title  { font-size: 14px; font-weight: 600; color: #111827; }

        .pay-row { padding: 13px 16px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f9fafb; }
        .pay-row:last-child { border-bottom: none; }

        .cat-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
        .pay-name  { font-size: 14px; font-weight: 500; color: #111827; }
        .pay-meta  { font-size: 12px; color: #9ca3af; }
        .pay-amount { font-size: 14px; font-weight: 700; color: #16a34a; }
        .pay-ghs    { font-size: 11px; color: #9ca3af; text-align: right; }

        .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; }

        .pagination { margin-top: 1rem; }
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
    <span class="nav-title">My Payments</span>
    <a href="{{ route('portal.pay') }}"
       style="background:#16a34a;color:white;padding:6px 14px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
        Pay Now
    </a>
</nav>

<div class="content">

    {{-- Summary --}}
    <div class="summary-grid">
        <div class="sum-card">
            <div class="sum-value" style="color:#16a34a;">GH₵ {{ number_format($summary['total'], 2) }}</div>
            <div class="sum-label">Total paid (all time)</div>
        </div>
        <div class="sum-card">
            <div class="sum-value" style="color:#2563eb;">GH₵ {{ number_format($summary['this_year'], 2) }}</div>
            <div class="sum-label">Paid this year</div>
        </div>
        <div class="sum-card">
            <div class="sum-value" style="color:#7c3aed;">GH₵ {{ number_format($summary['tithe'], 2) }}</div>
            <div class="sum-label">Total tithes</div>
        </div>
        <div class="sum-card">
            <div class="sum-value" style="color:#d97706;">GH₵ {{ number_format($summary['offering'], 2) }}</div>
            <div class="sum-label">Total offerings</div>
        </div>
    </div>

    {{-- Payment list --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">All payments</span>
            <span style="font-size:12px;color:#9ca3af;">{{ $payments->total() }} records</span>
        </div>

        @forelse($payments as $payment)
            <div class="pay-row">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div class="cat-icon" style="
                    {{ $payment->category === 'tithe'        ? 'background:#dbeafe;' : '' }}
                    {{ $payment->category === 'offering'     ? 'background:#dcfce7;' : '' }}
                    {{ $payment->category === 'thanksgiving' ? 'background:#fef3c7;' : '' }}
                    {{ $payment->category === 'pledge'       ? 'background:#ede9fe;' : '' }}
                    {{ $payment->category === 'welfare'      ? 'background:#fee2e2;' : '' }}
                    {{ !in_array($payment->category, ['tithe','offering','thanksgiving','pledge','welfare']) ? 'background:#f3f4f6;' : '' }}">
                        @switch($payment->category)
                            @case('tithe')        🙏 @break
                            @case('offering')     💝 @break
                            @case('thanksgiving') 🎉 @break
                            @case('pledge')       📋 @break
                            @case('welfare')      ❤️ @break
                            @default              💰
                        @endswitch
                    </div>
                    <div>
                        <p class="pay-name">{{ ucfirst($payment->category) }}</p>
                        <p class="pay-meta">
                            {{ $payment->payment_date->format('D, d M Y') }}
                        </p>
                        <p class="pay-meta">
                            {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                            @if($payment->reference)
                                · <span style="font-family:monospace;">{{ $payment->reference }}</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div style="text-align:right;">
                    <p class="pay-amount">
                        {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                    </p>
                    @if($payment->currency !== 'GHS')
                        <p class="pay-ghs">GH₵ {{ number_format($payment->amount_ghs, 2) }}</p>
                    @endif
                    <span class="badge" style="background:#dcfce7;color:#15803d;margin-top:4px;">
                    ✓ Confirmed
                </span>
                </div>
            </div>
        @empty
            <div style="padding:48px 16px;text-align:center;">
                <div style="font-size:40px;margin-bottom:12px;">💳</div>
                <p style="font-size:15px;font-weight:500;color:#374151;margin-bottom:6px;">No payment records yet</p>
                <p style="font-size:13px;color:#9ca3af;margin-bottom:16px;">
                    Your tithe and offering records will appear here.
                </p>
                <a href="{{ route('portal.pay') }}"
                   style="display:inline-block;background:#16a34a;color:white;padding:10px 24px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;">
                    Make a payment →
                </a>
            </div>
        @endforelse
    </div>

    <div class="pagination">
        {{ $payments->links() }}
    </div>

</div>
</body>
</html>
