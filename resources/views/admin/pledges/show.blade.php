@extends('layouts.admin')
@section('page-title', 'Pledge Detail')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('admin.pledges.index') }}" style="color:#9ca3af;font-size:13px;text-decoration:none;">← Back</a>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">
                {{ $pledge->description ?? ucfirst($pledge->category) }} — {{ $pledge->member->full_name }}
            </h2>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display:grid;gap:20px;">
        <style>@media(min-width:1024px){.pledge-detail{grid-template-columns:1fr 1fr !important;}}</style>
        <div style="display:grid;gap:20px;" class="pledge-detail">

            {{-- Left: pledge info + add payment --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- Pledge card --}}
                <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                    <div style="background:linear-gradient(135deg,#2563eb,#4f46e5);padding:20px;color:white;">
                        <p style="font-size:12px;opacity:0.8;margin-bottom:4px;">{{ $pledge->member->full_name }} · {{ $pledge->member->member_id_card }}</p>
                        <h3 style="font-size:18px;font-weight:700;margin-bottom:4px;">
                            {{ $pledge->description ?? ucfirst($pledge->category) }}
                        </h3>
                        <p style="font-size:26px;font-weight:800;">
                            {{ $pledge->currency }} {{ number_format($pledge->pledged_amount, 2) }}
                        </p>
                    </div>

                    {{-- Progress --}}
                    <div style="padding:16px 18px;border-bottom:1px solid #f3f4f6;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:13px;">
                            <span style="color:#6b7280;">Payment progress</span>
                            <span style="font-weight:700;color:{{ $pledge->progress >= 100 ? '#16a34a' : '#374151' }};">
                        {{ $pledge->progress }}%
                    </span>
                        </div>
                        <div style="height:12px;background:#f3f4f6;border-radius:6px;overflow:hidden;margin-bottom:8px;">
                            <div style="height:100%;width:{{ $pledge->progress }}%;
                                background:{{ $pledge->progress >= 100 ? 'linear-gradient(90deg,#16a34a,#22c55e)' : 'linear-gradient(90deg,#2563eb,#3b82f6)' }};
                                border-radius:6px;transition:width 0.5s;"></div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;text-align:center;">
                            <div style="background:#f9fafb;border-radius:8px;padding:10px;">
                                <p style="font-size:14px;font-weight:700;color:#16a34a;">{{ number_format($pledge->paid_amount, 2) }}</p>
                                <p style="font-size:11px;color:#9ca3af;">Paid</p>
                            </div>
                            <div style="background:#f9fafb;border-radius:8px;padding:10px;">
                                <p style="font-size:14px;font-weight:700;color:#dc2626;">{{ number_format($pledge->remaining, 2) }}</p>
                                <p style="font-size:11px;color:#9ca3af;">Remaining</p>
                            </div>
                            <div style="background:#f9fafb;border-radius:8px;padding:10px;">
                                <p style="font-size:14px;font-weight:700;color:#374151;">{{ $pledge->payments->count() }}</p>
                                <p style="font-size:11px;color:#9ca3af;">Payments</p>
                            </div>
                        </div>
                    </div>

                    <div style="padding:14px 18px;display:flex;flex-direction:column;gap:8px;font-size:13px;">
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Pledge date</span>
                            <span style="font-weight:500;">{{ $pledge->pledge_date->format('d M Y') }}</span>
                        </div>
                        @if($pledge->due_date)
                            <div style="display:flex;justify-content:space-between;">
                                <span style="color:#9ca3af;">Due date</span>
                                <span style="font-weight:500;color:{{ $pledge->due_date->isPast() && $pledge->status !== 'completed' ? '#dc2626' : '#111827' }};">
                        {{ $pledge->due_date->format('d M Y') }}
                    </span>
                            </div>
                        @endif
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Status</span>
                            <span style="padding:3px 12px;border-radius:20px;font-size:11px;font-weight:600;
                        {{ $pledge->status === 'active'    ? 'background:#fef3c7;color:#d97706;' : '' }}
                        {{ $pledge->status === 'completed' ? 'background:#dcfce7;color:#15803d;' : '' }}
                        {{ $pledge->status === 'overdue'   ? 'background:#fee2e2;color:#dc2626;' : '' }}
                        {{ $pledge->status === 'cancelled' ? 'background:#f3f4f6;color:#6b7280;' : '' }}">
                        {{ ucfirst($pledge->status) }}
                    </span>
                        </div>
                    </div>
                </div>

                {{-- Add payment form --}}
                @if(in_array($pledge->status, ['active', 'overdue']))
                    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;">
                        <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:16px;">Record payment</h3>
                        <form method="POST" action="{{ route('admin.pledges.payment', $pledge) }}">
                            @csrf

                            <div style="margin-bottom:12px;">
                                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">
                                    Amount (max: {{ $pledge->currency }} {{ number_format($pledge->remaining, 2) }}) *
                                </label>
                                <input type="number" name="amount" step="0.01" min="0.01"
                                       max="{{ $pledge->remaining }}"
                                       placeholder="0.00" required
                                       style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                            </div>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                                <div>
                                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Payment date *</label>
                                    <input type="date" name="payment_date" value="{{ today()->toDateString() }}" required
                                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:13px;outline:none;box-sizing:border-box;">
                                </div>
                                <div>
                                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Method *</label>
                                    <select name="payment_method" required
                                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:13px;outline:none;">
                                        @foreach(config('finance.payment_methods') as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div style="margin-bottom:14px;">
                                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Reference</label>
                                <input type="text" name="reference" placeholder="Transaction ref (optional)"
                                       style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:13px;outline:none;box-sizing:border-box;">
                            </div>

                            <button type="submit"
                                    style="width:100%;background:#16a34a;color:white;padding:10px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                                Record payment
                            </button>
                        </form>
                    </div>
                @endif

            </div>

            {{-- Right: payment history --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="font-size:14px;font-weight:600;color:#111827;">Payment history</h3>
                    <span style="font-size:12px;color:#9ca3af;">{{ $pledge->payments->count() }} payment(s)</span>
                </div>

                @forelse($pledge->payments as $payment)
                    <div style="padding:14px 16px;border-bottom:1px solid #f9fafb;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:32px;height:32px;border-radius:8px;background:#dcfce7;display:flex;align-items:center;justify-content:center;font-size:14px;">
                                    💵
                                </div>
                                <div>
                                    <p style="font-size:14px;font-weight:700;color:#16a34a;">
                                        {{ $pledge->currency }} {{ number_format($payment->amount, 2) }}
                                    </p>
                                    <p style="font-size:11px;color:#9ca3af;">
                                        {{ $payment->payment_date->format('d M Y') }} ·
                                        {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                                    </p>
                                </div>
                            </div>
                            @if($payment->reference)
                                <span style="font-family:monospace;font-size:11px;color:#6b7280;background:#f3f4f6;padding:3px 8px;border-radius:6px;">
                    {{ $payment->reference }}
                </span>
                            @endif
                        </div>
                        @if($payment->notes)
                            <p style="font-size:12px;color:#9ca3af;margin-top:4px;margin-left:40px;">{{ $payment->notes }}</p>
                        @endif
                        <p style="font-size:11px;color:#9ca3af;margin-top:2px;margin-left:40px;">
                            Recorded by {{ $payment->recordedBy?->name ?? '—' }}
                        </p>
                    </div>
                @empty
                    <div style="padding:40px;text-align:center;color:#9ca3af;font-size:14px;">
                        No payments recorded yet.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

@endsection
