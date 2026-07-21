@extends('layouts.admin')
@section('page-title', 'Income Records')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Income Records</h2>
            <p style="font-size:13px;color:#9ca3af;">Total: GH₵ {{ number_format($total, 2) }}</p>
        </div>
        <did style="display:flex;justify-content:space-between; gap:12px;">
            <button onclick="document.getElementById('income-form').style.display='block'"
                    style="background:#16a34a;color:white;padding:8px 16px;border-radius:8px;font-size:14px;border:none;cursor:pointer;">
                + Record Income
            </button>
            <a href="{{ route('admin.finance.bulk-income') }}"
               style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;display:flex;align-items:center;gap:6px;">
                📋 Bulk Entry
            </a>
            @role('admin')
                <a href="{{ route('admin.finance.income.archived') }}"
                   style="background:#f3f4f6;border:1px solid #d1d5db;color:#6b7280;padding:8px 14px;border-radius:8px;font-size:14px;text-decoration:none;display:flex;align-items:center;gap:6px;">
                    🗄 Archived
                </a>
            @endrole
        </did>
    </div>

    {{-- Add income form --}}
    <div id="income-form" style="display:none;background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;margin-bottom:1.5rem;">
        <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:20px;">Record new income</h3>

        @if(session('success'))
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px;border-radius:8px;margin-bottom:16px;font-size:14px;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.finance.income.store') }}">
            @csrf

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px;">
                <style>@media(max-width:768px){.income-grid{grid-template-columns:1fr !important;}}</style>
                <div style="display:contents;" class="income-grid">

                    <div>
                        {{-- Replace the member_id select with this live search --}}
                        <div style="position:relative;">
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">
                                Member (optional)
                            </label>
                            <input type="text"
                                   id="member-search"
                                   placeholder="Type name or member ID..."
                                   autocomplete="off"
                                   style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='#3b82f6'"
                                   onblur="setTimeout(()=>document.getElementById('member-results').style.display='none',200)">
                            <input type="hidden" name="member_id" id="member-id-value">

                            {{-- Results dropdown --}}
                            <div id="member-results"
                                 style="display:none;position:absolute;top:100%;left:0;right:0;background:white;border:1px solid #d1d5db;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,0.1);z-index:50;max-height:220px;overflow-y:auto;">
                            </div>

                            {{-- Selected member display --}}
                            <div id="member-selected"
                                 style="display:none;margin-top:6px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:8px 12px;display:none;align-items:center;justify-content:space-between;">
                                <div>
                                    <p id="selected-name" style="font-size:13px;font-weight:600;color:#1d4ed8;"></p>
                                    <p id="selected-id"   style="font-size:11px;color:#6b7280;font-family:monospace;"></p>
                                </div>
                                <button type="button" onclick="clearMember()"
                                        style="color:#9ca3af;background:none;border:none;cursor:pointer;font-size:16px;">✕</button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Category *</label>
                        <select name="category"
                                style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                            @foreach(config('finance.income_categories') as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Payment date *</label>
                        <input type="date" name="payment_date" value="{{ today()->toDateString() }}"
                               style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;"
                               required>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Currency *</label>
                        <select name="currency" id="currency-select" onchange="updateExchangeRate()"
                                style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                            @foreach(config('finance.currencies') as $code => $info)
                                <option value="{{ $code }}">{{ $code }} — {{ $info['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Amount *</label>
                        <input type="number" name="amount" step="0.01" min="0.01"
                               placeholder="0.00" id="amount-input" oninput="calcGhs()"
                               style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;"
                               required>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Exchange rate to GHS</label>
                        <input type="number" name="exchange_rate" step="0.0001" id="exchange-rate"
                               value="1" oninput="calcGhs()"
                               style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;"
                               required>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Amount in GHS</label>
                        <input type="text" id="amount-ghs-display" readonly
                               value="0.00"
                               style="width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:9px 12px;font-size:14px;background:#f9fafb;color:#374151;box-sizing:border-box;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Payment method *</label>
                        <select name="payment_method"
                                style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                            @foreach(config('finance.payment_methods') as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Reference / Receipt no.</label>
                        <input type="text" name="reference" placeholder="e.g. TXN123456"
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

                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Notes</label>
                <textarea name="notes" rows="2" placeholder="Optional notes..."
                          style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;resize:none;box-sizing:border-box;"></textarea>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit"
                        style="background:#16a34a;color:white;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                    Save income
                </button>
                <button type="button" onclick="document.getElementById('income-form').style.display='none'"
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
            @foreach(config('finance.income_categories') as $key => $label)
                <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="currency"
                style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
            <option value="">All currencies</option>
            @foreach(config('finance.currencies') as $code => $info)
                <option value="{{ $code }}" {{ request('currency') === $code ? 'selected' : '' }}>{{ $code }}</option>
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
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Member</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Category</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Amount</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">GHS Equiv.</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Method</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Reference</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">By</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;"></th>
            </tr>
            </thead>
            <tbody>
            @forelse($records as $record)
                <tr style="border-top:1px solid #f3f4f6;"
                    onmouseenter="this.style.background='#f9fafb'"
                    onmouseleave="this.style.background=''">
                    <td style="padding:12px 16px;color:#6b7280;">{{ $record->payment_date->format('d M Y') }}</td>
                    {{-- Member --}}
                    <td style="padding:12px 16px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:32px;height:32px;border-radius:50%;
                                background:{{ $record->member_id ? '#dbeafe' : '#fef3c7' }};
                                display:flex;align-items:center;justify-content:center;
                                color:{{ $record->member_id ? '#2563eb' : '#d97706' }};
                                font-size:11px;font-weight:600;flex-shrink:0;">
                                {{ $record->member_id
                                    ? strtoupper(substr($record->member->first_name,0,1).substr($record->member->last_name,0,1))
                                    : '👤' }}
                            </div>
                            <div>
                                @if($record->member_id)
                                    <p style="font-weight:500;color:#111827;">{{ $record->member->full_name }}</p>
                                    <p style="font-size:11px;color:#9ca3af;font-family:monospace;">{{ $record->member->member_id_card }}</p>
                                @else
                                    <p style="font-weight:500;color:#d97706;">Guest Giver</p>
                                    <p style="font-size:11px;color:#9ca3af;">
                                        {{ Str::before($record->notes ?? '', ' |') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;">
                    <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#dcfce7;color:#15803d;">
                        {{ ucfirst($record->category) }}
                    </span>
                    </td>
                    <td style="padding:12px 16px;font-weight:700;color:#16a34a;">
                        {{ $record->currency }} {{ number_format($record->amount, 2) }}
                    </td>
                    <td style="padding:12px 16px;color:#374151;">
                        GH₵ {{ number_format($record->amount_ghs, 2) }}
                    </td>
                    <td style="padding:12px 16px;color:#6b7280;font-size:12px;">
                        {{ ucwords(str_replace('_', ' ', $record->payment_method)) }}
                    </td>
                    <td style="padding:12px 16px;color:#9ca3af;font-size:12px;font-family:monospace;">
                        {{ $record->reference ?? '—' }}
                    </td>
                    <td style="padding:12px 16px;color:#9ca3af;font-size:12px;">
                        {{ $record->recordedBy?->name ?? '—' }}
                    </td>
                    <td style="padding:12px 16px;">
                        <form method="POST" action="{{ route('admin.finance.income.destroy', $record) }}"
                              onsubmit="return confirm('Delete this record?')">
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
                    <td colspan="9" style="padding:40px;text-align:center;color:#9ca3af;">No income records found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $records->links() }}</div>

    <script>
        const rates = @json(collect(config('finance.currencies'))->map(fn($c) => $c['rate'] ?? 1));

        function updateExchangeRate() {
            const currency = document.getElementById('currency-select').value;
            document.getElementById('exchange-rate').value = rates[currency] || 1;
            calcGhs();
        }

        function calcGhs() {
            const amount = parseFloat(document.getElementById('amount-input').value) || 0;
            const rate   = parseFloat(document.getElementById('exchange-rate').value) || 1;
            document.getElementById('amount-ghs-display').value = (amount * rate).toFixed(2);
        }

        // ── Member live search ─────────────────────────────────
        let memberTimer;
        document.getElementById('member-search')?.addEventListener('input', function () {
            clearTimeout(memberTimer);
            const q = this.value.trim();
            const results = document.getElementById('member-results');

            if (q.length < 2) {
                results.style.display = 'none';
                return;
            }

            memberTimer = setTimeout(async () => {
                const res  = await fetch(`{{ route('admin.checkin.search') }}?query=${encodeURIComponent(q)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (!data.length) {
                    results.innerHTML = `<div style="padding:12px 16px;font-size:13px;color:#9ca3af;">No members found.</div>`;
                    results.style.display = 'block';
                    return;
                }

                results.innerHTML = data.map(m => `
                    <div onclick="selectMember(${m.id}, '${m.first_name} ${m.last_name}', '${m.member_id_card}')"
                         style="padding:10px 14px;cursor:pointer;display:flex;align-items:center;gap:10px;border-bottom:1px solid #f3f4f6;"
                         onmouseenter="this.style.background='#eff6ff'"
                         onmouseleave="this.style.background=''">
                        <div style="width:30px;height:30px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:11px;font-weight:600;flex-shrink:0;">
                            ${(m.first_name[0]+m.last_name[0]).toUpperCase()}
                        </div>
                        <div>
                            <p style="font-size:13px;font-weight:500;color:#111827;">${m.first_name} ${m.last_name}</p>
                            <p style="font-size:11px;color:#9ca3af;font-family:monospace;">${m.member_id_card}</p>
                        </div>
                    </div>
                `).join('');
                results.style.display = 'block';
            }, 300);
        });

        function selectMember(id, name, cardId) {
            document.getElementById('member-id-value').value  = id;
            document.getElementById('member-search').value    = '';
            document.getElementById('member-results').style.display = 'none';
            document.getElementById('selected-name').textContent   = name;
            document.getElementById('selected-id').textContent     = cardId;

            const box = document.getElementById('member-selected');
            box.style.display = 'flex';
        }

        function clearMember() {
            document.getElementById('member-id-value').value  = '';
            document.getElementById('member-search').value    = '';
            document.getElementById('member-selected').style.display = 'none';
        }
    </script>

@endsection
