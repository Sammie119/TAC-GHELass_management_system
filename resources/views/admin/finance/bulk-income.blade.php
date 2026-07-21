@extends('layouts.admin')
@section('page-title', 'Bulk Income Entry')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Bulk Income Entry</h2>
            <p style="font-size:13px;color:#9ca3af;">Enter multiple income records at once — e.g. Sunday tithes</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.finance.income-template') }}"
               style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:8px 14px;border-radius:8px;font-size:13px;text-decoration:none;display:flex;align-items:center;gap:6px;">
                📥 Download Excel Template
            </a>
            <a href="{{ route('admin.finance.income') }}"
               style="border:1px solid #d1d5db;color:#374151;padding:8px 14px;border-radius:8px;font-size:13px;text-decoration:none;">
                ← Back
            </a>
        </div>
    </div>

{{--    @if(session('success'))--}}
{{--        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;">--}}
{{--            {{ session('success') }}--}}
{{--        </div>--}}
{{--    @endif--}}

    @if(session('error'))
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;">
            <ul style="margin:0;padding-left:16px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Excel upload --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;margin-bottom:1.5rem;">
        <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:6px;">Option 1 — Upload Excel file</h3>
        <p style="font-size:13px;color:#6b7280;margin-bottom:14px;">
            Download the template above, fill it in Excel, then upload it here.
            Columns: Member ID Card, Category, Amount, Currency, Payment Method, Payment Date, Reference, Notes.
        </p>

        <form method="POST" action="{{ route('admin.finance.upload-excel') }}" enctype="multipart/form-data"
              style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            @csrf
            <div style="flex:1;min-width:250px;">
                <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:5px;">
                    Select Excel file (.xlsx, .xls, .csv)
                </label>
                <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required
                       style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;box-sizing:border-box;">
            </div>
            <button type="submit"
                    style="background:#16a34a;color:white;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:500;border:none;cursor:pointer;white-space:nowrap;">
                Upload & Import
            </button>
        </form>
    </div>

    {{-- Manual bulk entry --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;">
        <div class="bulk-section-header" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
            <div>
                <h3 style="font-size:14px;font-weight:600;color:#111827;">Option 2 — Manual bulk entry</h3>
                <p style="font-size:13px;color:#6b7280;margin-top:2px;">Add rows and fill in details for each member</p>
            </div>

            {{-- Global defaults --}}
            <div class="bulk-defaults" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <select id="global-category"
                        style="flex:1;min-width:120px;border:1px solid #d1d5db;border-radius:6px;padding:6px 10px;font-size:12px;outline:none;">
                    @foreach(config('finance.income_categories') as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <input type="date" id="global-date" value="{{ today()->toDateString() }}"
                       style="flex:1;min-width:130px;border:1px solid #d1d5db;border-radius:6px;padding:6px 10px;font-size:12px;outline:none;">
                <select id="global-method"
                        style="flex:1;min-width:140px;border:1px solid #d1d5db;border-radius:6px;padding:6px 10px;font-size:12px;outline:none;">
                    @foreach(config('finance.payment_methods') as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select id="global-bank-account"
                        style="flex:1;min-width:160px;border:1px solid #d1d5db;border-radius:6px;padding:6px 10px;font-size:12px;outline:none;">
                    <option value="">— None / Cash —</option>
                    @foreach($bankAccounts as $account)
                        <option value="{{ $account->id }}">{{ $account->bank_name }} — {{ $account->name }}</option>
                    @endforeach
                </select>
                <button onclick="applyGlobal()"
                        style="background:#2563eb;color:white;padding:6px 12px;border-radius:6px;font-size:12px;border:none;cursor:pointer;">
                    Apply to all
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.finance.bulk-income.store') }}" id="bulk-form">
            @csrf

            {{-- Table --}}
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;" id="bulk-table">
                    <thead style="background:#f9fafb;">
                    <tr>
                        <th style="padding:10px 8px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:200px;">Member</th>
                        <th style="padding:10px 8px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:120px;">Category</th>
                        <th style="padding:10px 8px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:80px;">Currency</th>
                        <th style="padding:10px 8px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:100px;">Amount</th>
                        <th style="padding:10px 8px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:80px;">Rate</th>
                        <th style="padding:10px 8px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:120px;">Method</th>
                        <th style="padding:10px 8px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:160px;">Bank account</th>
                        <th style="padding:10px 8px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:110px;">Date</th>
                        <th style="padding:10px 8px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:100px;">Reference</th>
                        <th style="padding:10px 8px;width:40px;"></th>
                    </tr>
                    </thead>
                    <tbody id="bulk-body">
                    {{-- Rows added by JS --}}
                    </tbody>
                </table>
            </div>

            <div class="bulk-footer" style="display:flex;gap:10px;margin-top:16px;align-items:center;flex-wrap:wrap;">
                <button type="button" onclick="addRow()"
                        style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:9px 16px;border-radius:8px;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:6px;">
                    + Add row
                </button>
                <button type="button" onclick="addRows(10)"
                        style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:9px 16px;border-radius:8px;font-size:13px;cursor:pointer;">
                    + Add 10 rows
                </button>
                <span id="row-count" class="row-count" style="font-size:13px;color:#9ca3af;margin-left:auto;">0 rows</span>
                <button type="submit" class="save-btn"
                        style="background:#16a34a;color:white;padding:9px 20px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                    Save all records
                </button>
            </div>
        </form>
    </div>

    <script>
        let rowIndex = 0;
        const categories = @json(config('finance.income_categories'));
        const methods    = @json(config('finance.payment_methods'));
        const currencies = @json(array_keys(config('finance.currencies')));
        const rates      = @json(collect(config('finance.currencies'))->map(fn($c) => $c['rate'] ?? 1));
        const bankAccounts = @json($bankAccounts->mapWithKeys(fn($a) => [$a->id => $a->bank_name.' — '.$a->name]));

        function addRow(defaults = {}) {
            const i    = rowIndex++;
            const body = document.getElementById('bulk-body');
            const tr   = document.createElement('tr');
            tr.id      = `row-${i}`;
            tr.style.borderTop = '1px solid #f3f4f6';

            const catOptions = Object.entries(categories)
                .map(([k,v]) => `<option value="${k}" ${k === (defaults.category || 'tithe') ? 'selected' : ''}>${v}</option>`)
                .join('');

            const methodOptions = Object.entries(methods)
                .map(([k,v]) => `<option value="${k}" ${k === (defaults.method || 'cash') ? 'selected' : ''}>${v}</option>`)
                .join('');

            const currOptions = currencies
                .map(c => `<option value="${c}" ${c === (defaults.currency || 'GHS') ? 'selected' : ''}>${c}</option>`)
                .join('');

            const bankAccountOptions = '<option value="">— None / Cash —</option>' + Object.entries(bankAccounts)
                .map(([k,v]) => `<option value="${k}" ${k === String(defaults.bankAccountId || '') ? 'selected' : ''}>${v}</option>`)
                .join('');

            tr.innerHTML = `
        <td style="padding:6px 8px;">
            <div style="position:relative;">
                <input type="text"
                       id="ms-${i}"
                       placeholder="Search member..."
                       autocomplete="off"
                       style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"
                       oninput="searchMember(${i}, this.value)"
                       onfocus="this.style.borderColor='#3b82f6'"
                       onblur="setTimeout(()=>{const d=document.getElementById('mr-${i}');if(d)d.style.display='none'},200)">
                <input type="hidden" name="entries[${i}][member_id]" id="mv-${i}">
                <div id="mr-${i}"
                     style="display:none;position:absolute;top:100%;left:0;right:0;background:white;border:1px solid #d1d5db;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,0.1);z-index:100;max-height:160px;overflow-y:auto;">
                </div>
            </div>
        </td>
        <td style="padding:6px 8px;">
            <select name="entries[${i}][category]"
                    style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 8px;font-size:13px;outline:none;">
                ${catOptions}
            </select>
        </td>
        <td style="padding:6px 8px;">
            <select name="entries[${i}][currency]"
                    id="curr-${i}"
                    onchange="updateRate(${i})"
                    style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 8px;font-size:13px;outline:none;">
                ${currOptions}
            </select>
        </td>
        <td style="padding:6px 8px;">
            <input type="number" name="entries[${i}][amount]"
                   step="0.01" min="0" placeholder="0.00"
                   style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 8px;font-size:13px;outline:none;box-sizing:border-box;">
        </td>
        <td style="padding:6px 8px;">
            <input type="number" name="entries[${i}][exchange_rate]"
                   id="rate-${i}"
                   step="0.0001" value="${rates[defaults.currency || 'GHS'] || 1}"
                   style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 8px;font-size:13px;outline:none;box-sizing:border-box;">
        </td>
        <td style="padding:6px 8px;">
            <select name="entries[${i}][payment_method]"
                    style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 8px;font-size:13px;outline:none;">
                ${methodOptions}
            </select>
        </td>
        <td style="padding:6px 8px;">
            <select name="entries[${i}][bank_account_id]" id="bank-${i}"
                    style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 8px;font-size:13px;outline:none;">
                ${bankAccountOptions}
            </select>
        </td>
        <td style="padding:6px 8px;">
            <input type="date" name="entries[${i}][payment_date]"
                   value="${defaults.date || '{{ today()->toDateString() }}'}"
                   style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 8px;font-size:13px;outline:none;box-sizing:border-box;">
        </td>
        <td style="padding:6px 8px;">
            <input type="text" name="entries[${i}][reference]"
                   placeholder="Ref..."
                   style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 8px;font-size:13px;outline:none;box-sizing:border-box;">
        </td>
        <td style="padding:6px 8px;text-align:center;">
            <button type="button" onclick="removeRow(${i})"
                    style="color:#f87171;background:none;border:none;cursor:pointer;font-size:16px;">✕</button>
        </td>
    `;
            body.appendChild(tr);
            updateRowCount();
        }

        function addRows(n) {
            for (let i = 0; i < n; i++) addRow();
        }

        function removeRow(i) {
            const row = document.getElementById(`row-${i}`);
            if (row) { row.remove(); updateRowCount(); }
        }

        function updateRowCount() {
            const count = document.getElementById('bulk-body').children.length;
            document.getElementById('row-count').textContent = count + ' row' + (count !== 1 ? 's' : '');
        }

        function updateRate(i) {
            const currency = document.getElementById(`curr-${i}`).value;
            document.getElementById(`rate-${i}`).value = rates[currency] || 1;
        }

        // Member search per row
        let searchTimers = {};
        async function searchMember(i, query) {
            clearTimeout(searchTimers[i]);
            const results = document.getElementById(`mr-${i}`);
            if (query.length < 2) { results.style.display = 'none'; return; }

            searchTimers[i] = setTimeout(async () => {
                const res  = await fetch(`{{ route('admin.checkin.search') }}?query=${encodeURIComponent(query)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (!data.length) {
                    results.innerHTML = `<div style="padding:10px 12px;font-size:12px;color:#9ca3af;">No members found.</div>`;
                    results.style.display = 'block';
                    return;
                }

                results.innerHTML = data.map(m => `
            <div onclick="pickMember(${i}, ${m.id}, '${m.first_name} ${m.last_name}', '${m.member_id_card}')"
                 style="padding:8px 12px;cursor:pointer;font-size:12px;border-bottom:1px solid #f3f4f6;"
                 onmouseenter="this.style.background='#eff6ff'"
                 onmouseleave="this.style.background=''">
                <span style="font-weight:500;color:#111827;">${m.first_name} ${m.last_name}</span>
                <span style="color:#9ca3af;font-family:monospace;margin-left:6px;">${m.member_id_card}</span>
            </div>
        `).join('');
                results.style.display = 'block';
            }, 300);
        }

        function pickMember(i, id, name, cardId) {
            document.getElementById(`mv-${i}`).value  = id;
            document.getElementById(`ms-${i}`).value  = `${name} (${cardId})`;
            document.getElementById(`mr-${i}`).style.display = 'none';
        }

        function applyGlobal() {
            const cat     = document.getElementById('global-category').value;
            const date    = document.getElementById('global-date').value;
            const method  = document.getElementById('global-method').value;
            const bankAcc = document.getElementById('global-bank-account').value;

            document.querySelectorAll('#bulk-body tr').forEach((tr, idx) => {
                const selects = tr.querySelectorAll('select');
                const inputs  = tr.querySelectorAll('input[type=date]');
                if (selects[0]) selects[0].value = cat;
                if (selects[2]) selects[2].value = method;
                if (selects[3]) selects[3].value = bankAcc;
                if (inputs[0])  inputs[0].value  = date;
            });
        }

        // Start with 5 rows
        addRows(10);
    </script>

    <style>
        @media(max-width:640px){
            .bulk-section-header { align-items:flex-start; }
            .bulk-defaults { width:100%; }
            .bulk-defaults select,
            .bulk-defaults input[type=date] { flex:1 1 100%; min-width:0; box-sizing:border-box; }
            .bulk-footer .row-count { margin-left:0 !important; }
            .bulk-footer .save-btn { width:100%; text-align:center; order:3; }
        }
        @media(max-width:768px){
            .bulk-footer .row-count { order:1; width:100%; }
            .bulk-footer .save-btn { order:2; }
        }
    </style>

@endsection
