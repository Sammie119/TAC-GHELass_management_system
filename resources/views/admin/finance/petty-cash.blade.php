@extends('layouts.admin')
@section('page-title', 'Petty Cash')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Petty Cash</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Track the petty cash float and disbursements</p>
        </div>
        <div style="display:flex;gap:10px;">
            <button onclick="document.getElementById('replenish-form').style.display='block'"
                    style="background:#16a34a;color:white;padding:8px 16px;border-radius:8px;font-size:14px;border:none;cursor:pointer;">
                + Replenish float
            </button>
            <button onclick="document.getElementById('disburse-form').style.display='block'"
                    style="background:#dc2626;color:white;padding:8px 16px;border-radius:8px;font-size:14px;border:none;cursor:pointer;">
                + Record disbursement
            </button>
            <a href="{{ route('admin.petty-cash.archived') }}"
               style="background:#f3f4f6;border:1px solid #d1d5db;color:#6b7280;padding:8px 14px;border-radius:8px;font-size:14px;text-decoration:none;display:flex;align-items:center;gap:6px;">
                🗄 Archived
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px;border-radius:8px;margin-bottom:16px;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px;border-radius:8px;margin-bottom:16px;font-size:14px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(1,1fr);gap:14px;margin-bottom:1.5rem;">
        <style>@media(min-width:768px){.petty-stats{grid-template-columns:repeat(4,1fr) !important;}}</style>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;" class="petty-stats">

            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:22px;font-weight:800;color:#2563eb;">GH₵ {{ number_format($balance, 2) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Current balance</p>
            </div>
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:20px;font-weight:800;color:#16a34a;">GH₵ {{ number_format($totalReplenished, 2) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Total replenished</p>
            </div>
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:20px;font-weight:800;color:#dc2626;">GH₵ {{ number_format($totalDisbursed, 2) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Total disbursed</p>
            </div>
            <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:12px;padding:16px;text-align:center;">
                <p style="font-size:20px;font-weight:800;color:#d97706;">GH₵ {{ number_format($monthDisbursed, 2) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Disbursed this month</p>
            </div>

        </div>
    </div>

    {{-- Replenish float form --}}
    <div id="replenish-form" style="display:none;background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;margin-bottom:1.5rem;">
        <h3 style="font-size:15px;font-weight:600;color:#111827;margin-bottom:20px;">Replenish float</h3>

        <form method="POST" action="{{ route('admin.petty-cash.replenish') }}">
            @csrf

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px;">

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Currency *</label>
                    <select name="currency" id="rep-currency" onchange="updateRepRate()"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach(config('finance.currencies') as $code => $info)
                            <option value="{{ $code }}">{{ $code }} — {{ $info['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Amount *</label>
                    <input type="number" name="amount" step="0.01" min="0.01" placeholder="0.00" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Exchange rate to GHS</label>
                    <input type="number" name="exchange_rate" step="0.0001" id="rep-rate" value="1" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Date *</label>
                    <input type="date" name="transaction_date" value="{{ today()->toDateString() }}" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Payment method *</label>
                    <select name="payment_method" required
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach(array_filter(config('finance.payment_methods'), fn($k) => $k !== 'online', ARRAY_FILTER_USE_KEY) as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Custodian</label>
                    <select name="custodian_id"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        <option value="">— None —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Bank account (withdrawn from)</label>
                    <select name="bank_account_id"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        <option value="">— None / Cash —</option>
                        @foreach($bankAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->bank_name }} — {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="grid-column:span 3;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Notes</label>
                    <input type="text" name="notes" placeholder="Any additional details"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit"
                        style="background:#16a34a;color:white;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                    Save replenishment
                </button>
                <button type="button" onclick="document.getElementById('replenish-form').style.display='none'"
                        style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:10px 20px;border-radius:8px;font-size:14px;cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    {{-- Disburse form --}}
    <div id="disburse-form" style="display:none;background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;margin-bottom:1.5rem;">
        <h3 style="font-size:15px;font-weight:600;color:#111827;margin-bottom:20px;">Record disbursement</h3>

        <form method="POST" action="{{ route('admin.petty-cash.disburse') }}">
            @csrf

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px;">

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Category *</label>
                    <select name="category" required
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach(config('finance.expense_categories') as $key => $label)
                            @unless($key === 'petty_cash_float')
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endunless
                        @endforeach
                    </select>
                </div>

                <div style="grid-column:span 2;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Description *</label>
                    <input type="text" name="description" placeholder="e.g. Stationery for office" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Amount (GHS) *</label>
                    <input type="number" name="amount" step="0.01" min="0.01" placeholder="0.00" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Date *</label>
                    <input type="date" name="transaction_date" value="{{ today()->toDateString() }}" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Payee</label>
                    <input type="text" name="payee" placeholder="e.g. Kwame's Shop"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Receipt number</label>
                    <input type="text" name="receipt_number" placeholder="e.g. RCP-001"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Budget line</label>
                    <select name="budget_line_id"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        <option value="">— None —</option>
                        @foreach($budgetLines as $line)
                            <option value="{{ $line->id }}">{{ $line->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="grid-column:span 3;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Notes</label>
                    <input type="text" name="notes" placeholder="Any additional details"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit"
                        style="background:#dc2626;color:white;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                    Save disbursement
                </button>
                <button type="button" onclick="document.getElementById('disburse-form').style.display='none'"
                        style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:10px 20px;border-radius:8px;font-size:14px;cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    {{-- Filter --}}
    <form method="GET" style="display:flex;gap:10px;margin-bottom:1.5rem;flex-wrap:wrap;">
        <input type="date" name="from" value="{{ request('from') }}"
               style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
        <input type="date" name="to" value="{{ request('to') }}"
               style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
        <select name="type"
                style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
            <option value="">All types</option>
            <option value="replenishment" {{ request('type') === 'replenishment' ? 'selected' : '' }}>Replenishment</option>
            <option value="disbursement" {{ request('type') === 'disbursement' ? 'selected' : '' }}>Disbursement</option>
        </select>
        <button type="submit"
                style="background:#f3f4f6;border:1px solid #d1d5db;padding:8px 16px;border-radius:8px;font-size:13px;cursor:pointer;">
            Filter
        </button>
    </form>

    {{-- Transactions table --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Date</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Type</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Description</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Category</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Amount</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Recorded by</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;"></th>
            </tr>
            </thead>
            <tbody>
            @forelse($transactions as $transaction)
                <tr style="border-top:1px solid #f3f4f6;"
                    onmouseenter="this.style.background='#f9fafb'"
                    onmouseleave="this.style.background=''">
                    <td style="padding:12px 16px;color:#6b7280;">{{ $transaction->transaction_date->format('d M Y') }}</td>
                    <td style="padding:12px 16px;">
                        @if($transaction->type === 'replenishment')
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#dcfce7;color:#15803d;">Replenishment</span>
                        @else
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#fee2e2;color:#dc2626;">Disbursement</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        <p style="font-weight:500;color:#111827;">{{ $transaction->description }}</p>
                        @if($transaction->payee)
                            <p style="font-size:11px;color:#9ca3af;">{{ $transaction->payee }}</p>
                        @endif
                        @if($transaction->custodian)
                            <p style="font-size:11px;color:#9ca3af;">Custodian: {{ $transaction->custodian->name }}</p>
                        @endif
                    </td>
                    <td style="padding:12px 16px;color:#6b7280;">
                        {{ $transaction->category ? ucfirst(str_replace('_', ' ', $transaction->category)) : '—' }}
                    </td>
                    <td style="padding:12px 16px;font-weight:700;color:{{ $transaction->type === 'replenishment' ? '#16a34a' : '#dc2626' }};">
                        {{ $transaction->type === 'replenishment' ? '+' : '−' }} GH₵ {{ number_format($transaction->amount_ghs, 2) }}
                    </td>
                    <td style="padding:12px 16px;color:#6b7280;font-size:12px;">{{ $transaction->recordedBy?->name ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                        <form method="POST" action="{{ route('admin.petty-cash.destroy', $transaction) }}"
                              onsubmit="return confirm('Void this transaction?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="color:#f87171;font-size:12px;background:none;border:none;cursor:pointer;">
                                Void
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding:48px;text-align:center;color:#9ca3af;font-size:14px;">
                        <div style="font-size:40px;margin-bottom:12px;">💵</div>
                        No petty cash transactions yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $transactions->links() }}</div>

    <script>
        const pettyRates = @json(collect(config('finance.currencies'))->map(fn($c) => $c['rate'] ?? 1));
        function updateRepRate() {
            const currency = document.getElementById('rep-currency').value;
            document.getElementById('rep-rate').value = pettyRates[currency] || 1;
        }
    </script>

@endsection
