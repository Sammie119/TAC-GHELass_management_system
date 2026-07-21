@extends('layouts.admin')
@section('page-title', 'Financial Requests')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Financial Requests</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Requires approval from the Pastor and Finance Chairman before a PV can be generated</p>
        </div>
        <button onclick="document.getElementById('new-request-form').style.display='block'"
                style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-size:14px;border:none;cursor:pointer;">
            + New Request
        </button>
    </div>

{{--    @if(session('success'))--}}
{{--        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px;border-radius:8px;margin-bottom:16px;font-size:14px;">--}}
{{--            {{ session('success') }}--}}
{{--        </div>--}}
{{--    @endif--}}
    @if(session('error'))
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px;border-radius:8px;margin-bottom:16px;font-size:14px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- New request form --}}
    <div id="new-request-form" style="display:none;background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;margin-bottom:1.5rem;">
        <h3 style="font-size:15px;font-weight:600;color:#111827;margin-bottom:20px;">New financial request</h3>

        <form method="POST" action="{{ route('admin.financial-requests.store') }}">
            @csrf

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px;">

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Category *</label>
                    <select name="category" required
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach(config('finance.expense_categories') as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="grid-column:span 2;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Description *</label>
                    <input type="text" name="description" placeholder="e.g. Repair of church van" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Currency *</label>
                    <select name="currency" id="fr-currency" onchange="updateFrRate()" required
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
                    <input type="number" name="exchange_rate" id="fr-rate" step="0.0001" value="1" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Intended payment method *</label>
                    <select name="payment_method" required
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach(array_filter(config('finance.payment_methods'), fn($k) => $k !== 'online', ARRAY_FILTER_USE_KEY) as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Payee</label>
                    <input type="text" name="payee" placeholder="Who is to be paid"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Request date *</label>
                    <input type="date" name="request_date" value="{{ today()->toDateString() }}" required
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

                <div style="grid-column:span 3;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Notes</label>
                    <textarea name="notes" rows="2"
                              style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;resize:none;box-sizing:border-box;"></textarea>
                </div>

            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit"
                        style="background:#2563eb;color:white;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                    Submit request
                </button>
                <button type="button" onclick="document.getElementById('new-request-form').style.display='none'"
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
        <select name="status"
                style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
            <option value="">All statuses</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
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
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Amount</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Requested by</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Pastor</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Fin. Chair</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Status</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;"></th>
            </tr>
            </thead>
            <tbody>
            @forelse($requests as $r)
                <tr style="border-top:1px solid #f3f4f6;"
                    onmouseenter="this.style.background='#f9fafb'"
                    onmouseleave="this.style.background=''">
                    <td style="padding:12px 16px;color:#6b7280;">{{ $r->request_date->format('d M Y') }}</td>
                    <td style="padding:12px 16px;">
                        <p style="font-weight:500;color:#111827;">{{ $r->description }}</p>
                        <p style="font-size:11px;color:#9ca3af;">{{ ucfirst(str_replace('_', ' ', $r->category)) }}</p>
                    </td>
                    <td style="padding:12px 16px;font-weight:700;color:#111827;">
                        {{ $r->currency }} {{ number_format($r->amount, 2) }}
                    </td>
                    <td style="padding:12px 16px;color:#6b7280;font-size:12px;">{{ $r->requestedBy?->name ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                        @if($r->pastor_approved_at)
                            <span style="color:#16a34a;font-size:12px;">✓ {{ $r->pastorApprovedBy?->name }}</span>
                        @else
                            <span style="color:#9ca3af;font-size:12px;">Pending</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($r->super_admin_approved_at)
                            <span style="color:#16a34a;font-size:12px;">✓ {{ $r->superAdminApprovedBy?->name }}</span>
                        @else
                            <span style="color:#9ca3af;font-size:12px;">Pending</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($r->status === 'approved')
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#dcfce7;color:#15803d;">Approved</span>
                        @elseif($r->status === 'rejected')
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#fee2e2;color:#dc2626;">Rejected</span>
                        @else
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#fef3c7;color:#d97706;">Pending</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        <a href="{{ route('admin.financial-requests.show', $r) }}"
                           style="color:#2563eb;font-size:13px;font-weight:500;text-decoration:none;">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding:48px;text-align:center;color:#9ca3af;font-size:14px;">
                        <div style="font-size:40px;margin-bottom:12px;">🧾</div>
                        No financial requests yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $requests->links() }}</div>

    <script>
        const frRates = @json(collect(config('finance.currencies'))->map(fn($c) => $c['rate'] ?? 1));
        function updateFrRate() {
            const currency = document.getElementById('fr-currency').value;
            document.getElementById('fr-rate').value = frRates[currency] || 1;
        }
    </script>

@endsection
