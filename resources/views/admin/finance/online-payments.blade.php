@extends('layouts.admin')
@section('page-title', 'Online Payments')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <h2 style="font-size:18px;font-weight:600;color:#111827;">Online Payments</h2>
    </div>

    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Member</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Category</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Amount</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Phone</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Status</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Date</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($payments as $payment)
                <tr style="border-top:1px solid #f3f4f6;"
                    onmouseenter="this.style.background='#f9fafb'"
                    onmouseleave="this.style.background=''">
                    <td style="padding:12px 16px;">
                        <p style="font-weight:500;color:#111827;">{{ $payment->member->full_name }}</p>
                        <p style="font-size:11px;color:#9ca3af;">{{ $payment->member->member_id_card }}</p>
                    </td>
                    <td style="padding:12px 16px;color:#374151;">{{ ucfirst($payment->category) }}</td>
                    <td style="padding:12px 16px;font-weight:700;color:#16a34a;">
                        {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                    </td>
                    <td style="padding:12px 16px;color:#6b7280;">{{ $payment->phone ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                    <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;
                        {{ $payment->status === 'pending'   ? 'background:#fef3c7;color:#d97706;' : '' }}
                        {{ $payment->status === 'confirmed' ? 'background:#dcfce7;color:#15803d;' : '' }}
                        {{ $payment->status === 'failed'    ? 'background:#fee2e2;color:#dc2626;' : '' }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                    </td>
                    <td style="padding:12px 16px;color:#6b7280;">{{ $payment->created_at->format('d M Y h:i A') }}</td>
                    <td style="padding:12px 16px;">
                        @if($payment->status === 'pending')
                            <did style="display:flex;">
{{--                                <form method="POST" action="{{ route('admin.finance.payments.confirm', $payment) }}">--}}
{{--                                    @csrf--}}
{{--                                    <button type="submit"--}}
{{--                                            style="background:#16a34a;color:white;padding:5px 12px;border-radius:6px;font-size:12px;border:none;cursor:pointer;">--}}
{{--                                        Confirm--}}
{{--                                    </button>--}}
{{--                                </form>--}}
                                {{-- Verify button → opens modal --}}
                                <button onclick="openVerifyModal(
                                    {{ $payment->id }},
                                    '{{ $payment->reference }}',
                                    '{{ $payment->member->full_name }}',
                                    '{{ $payment->member->member_id_card }}',
                                    '{{ $payment->phone }}',
                                    '{{ $payment->currency }} {{ number_format($payment->amount, 2) }}',
                                    '{{ ucfirst($payment->category) }}'
                                )"
                                        style="background:#fef3c7;color:#d97706;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:600;border:none;cursor:pointer;display:flex;align-items:center;gap:4px;margin-left:5px">
                                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Verify
                                </button>
{{--                                <a href="{{ route('admin.finance.verify-payment', $payment) }}"--}}
{{--                                   style="background:#fef3c7;color:#d97706;padding:5px 12px;border-radius:6px;font-size:12px;border:none;cursor:pointer;margin-left:5px">--}}
{{--                                    Verify--}}
{{--                                </a>--}}
                            </did>
                        @else
                            <span style="font-size:12px;color:#9ca3af;">
                        {{ $payment->confirmedBy?->name ?? '—' }}
                    </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding:40px;text-align:center;color:#9ca3af;">No online payments yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $payments->links() }}</div>

    <div id="verify-modal"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:200;align-items:center;justify-content:center;padding:1rem;overflow-y:auto;">
        <div style="background:white;border-radius:20px;width:100%;max-width:480px;margin:auto;box-shadow:0 24px 64px rgba(0,0,0,0.2);overflow:hidden;">

            {{-- Modal header --}}
            <div style="background:linear-gradient(135deg,#16a34a,#22c55e);padding:20px 24px;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:22px;height:22px;" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size:16px;font-weight:700;color:white;margin:0;">Verify Payment</h3>
                        <p style="font-size:12px;color:rgba(255,255,255,0.8);margin:2px 0 0;">Confirm Online Transaction</p>
                    </div>
                </div>
                <button onclick="closeVerifyModal()"
                        style="color:rgba(255,255,255,0.8);background:none;border:none;cursor:pointer;font-size:22px;line-height:1;">✕</button>
            </div>

            {{-- Payment summary --}}
            <div style="background:#f0fdf4;border-bottom:1px solid #dcfce7;padding:16px 24px;">
                {{-- Verification form --}}
                <form method="POST" id="verify-form" style="padding:20px 24px;">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <p style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;">Member</p>
                            <p id="vm-member-name" style="font-size:14px;font-weight:600;color:#111827;"></p>
                            <p id="vm-member-id"   style="font-size:11px;color:#9ca3af;font-family:monospace;"></p>
                        </div>
                        <div>
                            <p style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;">Amount</p>
                            <p id="vm-amount" style="font-size:20px;font-weight:800;color:#16a34a;"></p>
                        </div>
                        <div>
                            <p style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;">Category</p>
                            <p id="vm-category" style="font-size:14px;font-weight:500;color:#374151;"></p>
                        </div>
                        <div>
                            <p style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;">Phone Number</p>
                            <p id="vm-phone" style="font-size:14px;font-weight:500;color:#374151;"></p>
                        </div>
                    </div>
                    <div style="margin-top:10px;margin-bottom:10px;padding:8px 12px;background:white;border-radius:8px;border:1px solid #bbf7d0;display:flex;align-items:center;gap:8px;">
                        <span style="font-size:12px;color:#6b7280;">Reference:</span>
                        <input type="text" name="vm_reference" id="vm-reference" placeholder="Enter reference number" style="font-family:monospace;font-size:13px;font-weight:700;color:#16a34a;padding:5px 5px;width:100%" required>
                    </div>

                    {{-- Buttons --}}
                    <div style="display:flex;gap:10px;">
                        <button type="submit"
                                style="flex:1;background:#16a34a;color:white;padding:13px;border-radius:10px;font-size:15px;font-weight:700;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
                            <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            Confirm Payment
                        </button>
                        <button type="button" onclick="closeVerifyModal()"
                                style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:13px 18px;border-radius:10px;font-size:14px;cursor:pointer;">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        // ── Verify modal ────────────────────────────────────────
        function openVerifyModal(paymentId, reference, memberName, memberId, phone, amount, category) {
            document.getElementById('verify-form').action = `/admin/finance/online-payments/${paymentId}/confirm`
                // `/admin/finance/online-payments/${paymentId}/confirm`;

            document.getElementById('vm-member-name').textContent = memberName;
            document.getElementById('vm-member-id').textContent   = memberId;
            document.getElementById('vm-amount').textContent      = amount;
            document.getElementById('vm-category').textContent    = category;
            document.getElementById('vm-phone').textContent       = phone;

            document.getElementById('verify-modal').style.display = 'flex';
            setTimeout(() => document.getElementById('vm-reference').focus(), 100);
        }

        function closeVerifyModal() {
            document.getElementById('verify-modal').style.display = 'none';
        }
    </script>

@endsection
