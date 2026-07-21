@extends('layouts.admin')
@section('page-title', 'Expense Records')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Expense Records</h2>
            <p style="font-size:13px;color:#9ca3af;">Total: GH₵ {{ number_format($total, 2) }}</p>
        </div>
        <div style="display:flex;justify-content:space-between; gap:12px;">
            <button onclick="document.getElementById('expense-form').style.display='block'"
                    style="background:#dc2626;color:white;padding:8px 16px;border-radius:8px;font-size:14px;border:none;cursor:pointer;">
                + Record Expense
            </button>
            @role('admin')
                <a href="{{ route('admin.finance.expenses.archived') }}"
                   style="background:#f3f4f6;border:1px solid #d1d5db;color:#6b7280;padding:8px 14px;border-radius:8px;font-size:14px;text-decoration:none;display:flex;align-items:center;gap:6px;">
                    🗄 Archived
                </a>
            @endrole
        </div>

    </div>

    {{-- Add expense form --}}
    <div id="expense-form" style="display:none;background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;margin-bottom:1.5rem;">
        <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:20px;">Record new expense</h3>

        @if(session('success'))
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px;border-radius:8px;margin-bottom:16px;font-size:14px;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.finance.expenses.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px;">

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Category *</label>
                    <select name="category"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach(config('finance.expense_categories') as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="grid-column:span 2;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Description *</label>
                    <input type="text" name="description" placeholder="e.g. Electricity bill for October"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;"
                           required>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Currency *</label>
                    <select name="currency" id="exp-currency" onchange="updateExpRate()"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach(config('finance.currencies') as $code => $info)
                            <option value="{{ $code }}">{{ $code }} — {{ $info['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Amount *</label>
                    <input type="number" name="amount" step="0.01" min="0.01"
                           placeholder="0.00" id="exp-amount" oninput="calcExpGhs()"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;"
                           required>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Exchange rate to GHS</label>
                    <input type="number" name="exchange_rate" step="0.0001" id="exp-rate"
                           value="1" oninput="calcExpGhs()"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;"
                           required>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Expense date *</label>
                    <input type="date" name="expense_date" value="{{ today()->toDateString() }}"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;"
                           required>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Payment method *</label>
                    <select name="payment_method"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach(array_filter(config('finance.payment_methods'), fn($k) => $k !== 'online', ARRAY_FILTER_USE_KEY) as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Payee</label>
                    <input type="text" name="payee" placeholder="e.g. ECG Ghana"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Receipt number</label>
                    <input type="text" name="receipt_number" placeholder="e.g. RCP-001"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Bank account</label>
                    <select name="bank_account_id"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        <option value="">— None / Cash —</option>
                        @foreach($bankAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->bank_name }} — {{ $account->name }}</option>
                        @endforeach
                    </select>
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

            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Notes</label>
                    <textarea name="notes" rows="2"
                              style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;resize:none;box-sizing:border-box;"></textarea>
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Attachment (receipt)</label>
                    <input type="file" name="attachment" accept="image/*,.pdf"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:13px;box-sizing:border-box;">
                </div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit"
                        style="background:#dc2626;color:white;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                    Save expense
                </button>
                <button type="button" onclick="document.getElementById('expense-form').style.display='none'"
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
        <select name="category"
                style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
            <option value="">All categories</option>
            @foreach(config('finance.expense_categories') as $key => $label)
                <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit"
                style="background:#f3f4f6;border:1px solid #d1d5db;padding:8px 16px;border-radius:8px;font-size:13px;cursor:pointer;">
            Filter
        </button>
    </form>

    {{-- Table --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Date</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Description</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Category</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Amount</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">GHS Equiv.</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Payee</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Method</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;"></th>
            </tr>
            </thead>
            <tbody>
            @forelse($records as $record)
                <tr style="border-top:1px solid #f3f4f6;"
                    onmouseenter="this.style.background='#f9fafb'"
                    onmouseleave="this.style.background=''">
                    <td style="padding:12px 16px;color:#6b7280;">{{ $record->expense_date->format('d M Y') }}</td>
                    <td style="padding:12px 16px;">
                        <p style="font-weight:500;color:#111827;">{{ $record->description }}</p>
                        @if($record->receipt_number)
                            <p style="font-size:11px;color:#9ca3af;">Receipt: {{ $record->receipt_number }}</p>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                    <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#fee2e2;color:#dc2626;">
                        {{ ucfirst($record->category) }}
                    </span>
                    </td>
                    <td style="padding:12px 16px;font-weight:700;color:#dc2626;">
                        {{ $record->currency }} {{ number_format($record->amount, 2) }}
                    </td>
                    <td style="padding:12px 16px;color:#374151;">
                        GH₵ {{ number_format($record->amount_ghs, 2) }}
                    </td>
                    <td style="padding:12px 16px;color:#6b7280;">{{ $record->payee ?? '—' }}</td>
                    <td style="padding:12px 16px;color:#6b7280;font-size:12px;">
                        {{ ucwords(str_replace('_', ' ', $record->payment_method)) }}
                    </td>
                    <td style="padding:12px 16px;">
                        <form method="POST" action="{{ route('admin.finance.expenses.destroy', $record) }}"
                              onsubmit="return confirm('Delete this expense?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="color:#f87171;font-size:12px;background:none;border:none;cursor:pointer;">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding:40px;text-align:center;color:#9ca3af;">No expense records found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $records->links() }}</div>

    <script>
        const rates = @json(collect(config('finance.currencies'))->map(fn($c) => $c['rate'] ?? 1));
        function updateExpRate() {
            const currency = document.getElementById('exp-currency').value;
            document.getElementById('exp-rate').value = rates[currency] || 1;
            calcExpGhs();
        }
        function calcExpGhs() {
            const amount = parseFloat(document.getElementById('exp-amount').value) || 0;
            const rate   = parseFloat(document.getElementById('exp-rate').value) || 1;
        }
    </script>

@endsection
