@extends('layouts.admin')
@section('page-title', 'Sunday Tithes Entry')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Sunday Tithes Entry</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Quickly enter all tithes and offerings after a service</p>
        </div>
        <a href="{{ route('admin.finance.income') }}"
           style="border:1px solid #d1d5db;color:#374151;padding:8px 14px;border-radius:8px;font-size:13px;text-decoration:none;">
            ← Back to income
        </a>
    </div>

    {{-- Service settings --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:20px;margin-bottom:1.5rem;">
        <h3 style="font-size:14px;font-weight:600;color:#111827;margin-bottom:16px;">
            1. Service details
        </h3>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
            <style>@media(max-width:768px){.service-grid{grid-template-columns:1fr !important;}}</style>
            <div style="display:contents;" class="service-grid">

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Service / Event</label>
                    <select id="event-select"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        <option value="">— General (no event) —</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}"
                                    data-date="{{ $event->event_date->toDateString() }}"
                                {{ $activeEvent && $activeEvent->id === $event->id ? 'selected' : '' }}>
                                {{ $event->title }} — {{ $event->event_date->format('d M Y') }}
                                {{ $event->status === 'active' ? '(Live)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Payment date</label>
                    <input type="date" id="global-date"
                           value="{{ $activeEvent ? $activeEvent->event_date->toDateString() : today()->toDateString() }}"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Currency</label>
                    <select id="global-currency" onchange="updateAllRates()"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach(config('finance.currencies') as $code => $info)
                            <option value="{{ $code }}">{{ $code }} — {{ $info['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Exchange rate to GHS</label>
                    <input type="number" id="global-rate" value="1" step="0.0001"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Payment method</label>
                    <select id="global-method"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach(config('finance.payment_methods') as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Default category</label>
                    <select id="global-category"
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach(config('finance.income_categories') as $key => $label)
                            <option value="{{ $key }}" {{ $key === 'tithe' ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;">
            <button onclick="applyGlobalSettings()"
                    style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;border:none;cursor:pointer;">
                ✓ Apply to all rows
            </button>
            <button onclick="addRows(5)"
                    style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:8px 14px;border-radius:8px;font-size:13px;cursor:pointer;">
                + Add 5 rows
            </button>
            <button onclick="addRows(10)"
                    style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:8px 14px;border-radius:8px;font-size:13px;cursor:pointer;">
                + Add 10 rows
            </button>
            <button onclick="clearEmpty()"
                    style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:8px 14px;border-radius:8px;font-size:13px;cursor:pointer;">
                Clear empty rows
            </button>
        </div>
    </div>

    {{-- Totals bar --}}
    <div style="background:linear-gradient(135deg,#16a34a,#22c55e);border-radius:12px;padding:14px 20px;margin-bottom:1rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;gap:24px;flex-wrap:wrap;">
            <div>
                <p style="font-size:12px;color:rgba(255,255,255,0.8);">Rows entered</p>
                <p id="total-rows" style="font-size:22px;font-weight:800;color:white;">0</p>
            </div>
            <div>
                <p style="font-size:12px;color:rgba(255,255,255,0.8);">Total amount</p>
                <p style="font-size:22px;font-weight:800;color:white;">
                    GH₵ <span id="grand-total">0.00</span>
                </p>
            </div>
        </div>
        <button onclick="submitAll()"
                style="background:white;color:#16a34a;padding:10px 24px;border-radius:10px;font-size:15px;font-weight:700;border:none;cursor:pointer;display:flex;align-items:center;gap:8px;">
            <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            Save all records
        </button>
    </div>

    {{-- Entry table --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;background:#f9fafb;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:14px;font-weight:600;color:#111827;">2. Enter tithes & offerings</h3>
            <span id="row-count-label" style="font-size:12px;color:#9ca3af;">0 rows</span>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="background:#f9fafb;">
                <tr>
                    <th style="padding:10px 12px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:220px;">Member</th>
                    <th style="padding:10px 12px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:120px;">Category</th>
                    <th style="padding:10px 12px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:120px;">Amount</th>
                    <th style="padding:10px 12px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:80px;">GHS</th>
                    <th style="padding:10px 12px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:150px;">Method</th>
                    <th style="padding:10px 12px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;min-width:120px;">Notes</th>
                    <th style="padding:10px 12px;width:40px;"></th>
                </tr>
                </thead>
                <tbody id="entry-body"></tbody>
            </table>
        </div>

        <div style="padding:14px 16px;border-top:1px solid #f3f4f6;display:flex;gap:8px;">
            <button onclick="addRows(1)"
                    style="background:#eff6ff;border:1px solid #bfdbfe;color:#2563eb;padding:8px 14px;border-radius:8px;font-size:13px;cursor:pointer;">
                + Add row
            </button>
        </div>
    </div>

    {{-- Hidden master form --}}
    <form method="POST" action="{{ route('admin.finance.sunday-tithes.store') }}" id="master-form">
        @csrf
        <input type="hidden" name="event_id"       id="f-event-id">
        <input type="hidden" name="payment_date"   id="f-payment-date">
        <input type="hidden" name="currency"       id="f-currency">
        <input type="hidden" name="exchange_rate"  id="f-exchange-rate">
        <input type="hidden" name="payment_method" id="f-payment-method">
        <div id="f-entries"></div>
    </form>

    <script>
        let rowIndex = 0;
        const categories = @json(config('finance.income_categories'));
        const methods    = @json(config('finance.payment_methods'));
        const currencies = @json(array_keys(config('finance.currencies')));
        const rates      = { GHS: 1, USD: 15.5, GBP: 19.5, EUR: 17.0 };
        let searchTimers = {};

        // ── Add rows ────────────────────────────────────────────
        function addRows(n = 1) {
            for (let i = 0; i < n; i++) addRow();
        }

        function addRow(defaults = {}) {
            const i    = rowIndex++;
            const body = document.getElementById('entry-body');
            const tr   = document.createElement('tr');
            tr.id      = `row-${i}`;
            tr.style.cssText = 'border-top:1px solid #f3f4f6;';

            const currency = defaults.currency || document.getElementById('global-currency').value || 'GHS';
            const method   = defaults.method   || document.getElementById('global-method').value   || 'cash';
            const category = defaults.category || document.getElementById('global-category').value || 'tithe';

            const catOpts = Object.entries(categories)
                .map(([k,v]) => `<option value="${k}" ${k === category ? 'selected' : ''}>${v}</option>`)
                .join('');

            const methodOpts = Object.entries(methods)
                .map(([k,v]) => `<option value="${k}" ${k === method ? 'selected' : ''}>${v}</option>`)
                .join('');

            tr.innerHTML = `
        <td style="padding:6px 10px;">
            <div style="position:relative;">
                <input type="text"
                       id="ms-${i}"
                       placeholder="Type name or ID..."
                       autocomplete="off"
                       style="width:100%;border:1px solid #e5e7eb;border-radius:7px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"
                       oninput="searchMember(${i},this.value)"
                       onfocus="this.style.borderColor='#3b82f6'"
                       onblur="this.style.borderColor='#e5e7eb';setTimeout(()=>{const d=document.getElementById('mr-${i}');if(d)d.style.display='none'},200)">
                <input type="hidden" id="mv-${i}">
                <div id="mr-${i}"
                     style="display:none;position:absolute;top:100%;left:0;right:0;background:white;border:1px solid #d1d5db;border-radius:7px;box-shadow:0 4px 12px rgba(0,0,0,0.1);z-index:100;max-height:160px;overflow-y:auto;"></div>
            </div>
        </td>
        <td style="padding:6px 10px;">
            <select id="cat-${i}"
                    style="width:100%;border:1px solid #e5e7eb;border-radius:7px;padding:7px 8px;font-size:13px;outline:none;">
                ${catOpts}
            </select>
        </td>
        <td style="padding:6px 10px;">
            <input type="number" id="amt-${i}"
                   step="0.01" min="0" placeholder="0.00"
                   oninput="calcRow(${i});updateTotals()"
                   style="width:100%;border:1px solid #e5e7eb;border-radius:7px;padding:7px 8px;font-size:13px;outline:none;box-sizing:border-box;">
        </td>
        <td style="padding:6px 10px;">
            <input type="text" id="ghs-${i}" readonly
                   style="width:80px;border:1px solid #e5e7eb;border-radius:7px;padding:7px 8px;font-size:13px;background:#f9fafb;color:#16a34a;font-weight:600;">
        </td>
        <td style="padding:6px 10px;">
            <select id="mth-${i}"
                    style="width:100%;border:1px solid #e5e7eb;border-radius:7px;padding:7px 8px;font-size:13px;outline:none;">
                ${methodOpts}
            </select>
        </td>
        <td style="padding:6px 10px;">
            <input type="text" id="note-${i}"
                   placeholder="Optional..."
                   style="width:100%;border:1px solid #e5e7eb;border-radius:7px;padding:7px 8px;font-size:13px;outline:none;box-sizing:border-box;">
        </td>
        <td style="padding:6px 10px;text-align:center;">
            <button type="button" onclick="removeRow(${i})"
                    style="color:#f87171;background:none;border:none;cursor:pointer;font-size:16px;">✕</button>
        </td>
    `;
            body.appendChild(tr);
            updateRowCount();
        }

        function removeRow(i) {
            document.getElementById(`row-${i}`)?.remove();
            updateRowCount();
            updateTotals();
        }

        function clearEmpty() {
            document.querySelectorAll('#entry-body tr').forEach(tr => {
                const id = tr.id.replace('row-', '');
                const amt = document.getElementById(`amt-${id}`)?.value;
                if (!amt || parseFloat(amt) <= 0) tr.remove();
            });
            updateRowCount();
        }

        function updateRowCount() {
            const count = document.getElementById('entry-body').children.length;
            document.getElementById('row-count-label').textContent = count + ' row' + (count !== 1 ? 's' : '');
            document.getElementById('total-rows').textContent = count;
        }

        // ── Calc GHS for a row ──────────────────────────────────
        function calcRow(i) {
            const amt  = parseFloat(document.getElementById(`amt-${i}`)?.value) || 0;
            const rate = parseFloat(document.getElementById('global-rate').value) || 1;
            const ghs  = document.getElementById(`ghs-${i}`);
            if (ghs) ghs.value = (amt * rate).toFixed(2);
        }

        function updateTotals() {
            let total = 0;
            document.querySelectorAll('#entry-body tr').forEach(tr => {
                const id  = tr.id.replace('row-', '');
                const ghs = parseFloat(document.getElementById(`ghs-${id}`)?.value) || 0;
                total += ghs;
            });
            document.getElementById('grand-total').textContent = total.toFixed(2);
        }

        // ── Apply global settings to all rows ──────────────────
        function applyGlobalSettings() {
            const category = document.getElementById('global-category').value;
            const method   = document.getElementById('global-method').value;
            const rate     = document.getElementById('global-rate').value;

            document.querySelectorAll('#entry-body tr').forEach(tr => {
                const id = tr.id.replace('row-', '');
                const catEl  = document.getElementById(`cat-${id}`);
                const mthEl  = document.getElementById(`mth-${id}`);
                if (catEl) catEl.value = category;
                if (mthEl) mthEl.value = method;
                calcRow(id);
            });
            updateTotals();
        }

        function updateAllRates() {
            const currency = document.getElementById('global-currency').value;
            document.getElementById('global-rate').value = rates[currency] || 1;
            document.querySelectorAll('#entry-body tr').forEach(tr => {
                calcRow(tr.id.replace('row-', ''));
            });
            updateTotals();
        }

        // ── Member search per row ───────────────────────────────
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
                    results.innerHTML = `<div style="padding:8px 12px;font-size:12px;color:#9ca3af;">No members found.</div>`;
                    results.style.display = 'block';
                    return;
                }

                results.innerHTML = data.map(m => `
            <div onclick="pickMember(${i},${m.id},'${m.first_name} ${m.last_name}','${m.member_id_card}')"
                 style="padding:8px 12px;cursor:pointer;border-bottom:1px solid #f3f4f6;"
                 onmouseenter="this.style.background='#eff6ff'"
                 onmouseleave="this.style.background=''">
                <span style="font-weight:500;color:#111827;font-size:13px;">${m.first_name} ${m.last_name}</span>
                <span style="color:#9ca3af;font-family:monospace;font-size:11px;margin-left:6px;">${m.member_id_card}</span>
            </div>
        `).join('');
                results.style.display = 'block';
            }, 250);
        }

        function pickMember(i, id, name, cardId) {
            document.getElementById(`mv-${i}`).value  = id;
            document.getElementById(`ms-${i}`).value  = `${name} (${cardId})`;
            document.getElementById(`ms-${i}`).style.borderColor = '#16a34a';
            document.getElementById(`mr-${i}`).style.display = 'none';
        }

        // ── Submit — build form data and post ───────────────────
        function submitAll() {
            const rows = document.querySelectorAll('#entry-body tr');

            if (rows.length === 0) {
                alert('Please add at least one row.');
                return;
            }

            // Validate — at least one row with an amount
            let valid = false;
            rows.forEach(tr => {
                const id  = tr.id.replace('row-', '');
                const amt = parseFloat(document.getElementById(`amt-${id}`)?.value) || 0;
                if (amt > 0) valid = true;
            });

            if (!valid) {
                alert('Please enter at least one amount.');
                return;
            }

            // Fill hidden fields
            document.getElementById('f-event-id').value      = document.getElementById('event-select').value;
            document.getElementById('f-payment-date').value  = document.getElementById('global-date').value;
            document.getElementById('f-currency').value      = document.getElementById('global-currency').value;
            document.getElementById('f-exchange-rate').value = document.getElementById('global-rate').value;
            document.getElementById('f-payment-method').value= document.getElementById('global-method').value;

            // Build entries
            const container = document.getElementById('f-entries');
            container.innerHTML = '';

            let idx = 0;
            rows.forEach(tr => {
                const id  = tr.id.replace('row-', '');
                const amt = parseFloat(document.getElementById(`amt-${id}`)?.value) || 0;
                if (amt <= 0) return;

                const memberId = document.getElementById(`mv-${id}`)?.value || '';
                const category = document.getElementById(`cat-${id}`)?.value || 'tithe';
                const notes    = document.getElementById(`note-${id}`)?.value || '';

                if (memberId) {
                    container.innerHTML += `<input type="hidden" name="entries[${idx}][member_id]" value="${memberId}">`;
                }
                container.innerHTML += `
            <input type="hidden" name="entries[${idx}][amount]"   value="${amt}">
            <input type="hidden" name="entries[${idx}][category]" value="${category}">
            <input type="hidden" name="entries[${idx}][notes]"    value="${notes}">
        `;
                idx++;
            });

            document.getElementById('master-form').submit();
        }

        // ── Auto-set date when event is selected ────────────────
        document.getElementById('event-select').addEventListener('change', function () {
            const opt  = this.options[this.selectedIndex];
            const date = opt.getAttribute('data-date');
            if (date) document.getElementById('global-date').value = date;
        });

        // ── Start with 10 empty rows ────────────────────────────
        window.addEventListener('DOMContentLoaded', () => addRows(10));
    </script>

@endsection
