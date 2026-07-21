@extends('layouts.admin')
@section('page-title', 'Bank Accounts')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Bank Accounts</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Register the church's bank accounts and link them to income/expense entries</p>
        </div>
        <button onclick="openAddForm()"
                style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-size:14px;border:none;cursor:pointer;">
            + Add bank account
        </button>
    </div>

    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px;border-radius:8px;margin-bottom:16px;font-size:14px;">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Add / edit form panel --}}
    <div id="account-form-panel" style="display:none;background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;margin-bottom:1.5rem;">
        <h3 id="account-form-title" style="font-size:15px;font-weight:600;color:#111827;margin-bottom:20px;">Add bank account</h3>

        <form id="account-form" method="POST" action="{{ route('admin.bank-accounts.store') }}">
            @csrf
            <input type="hidden" name="_method" id="account-form-method" value="">

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px;">

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Bank name *</label>
                    <input type="text" name="bank_name" id="field-bank-name" placeholder="e.g. GCB Bank" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Account name/label *</label>
                    <input type="text" name="name" id="field-name" placeholder="e.g. Main Church Account" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Account number *</label>
                    <input type="text" name="account_number" id="field-account-number" placeholder="e.g. 1234567890" required
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;font-family:monospace;">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Account type *</label>
                    <select name="account_type" id="field-account-type" required
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        <option value="savings">Savings</option>
                        <option value="current">Current</option>
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Currency *</label>
                    <select name="currency" id="field-currency" required
                            style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;">
                        @foreach(config('finance.currencies') as $code => $info)
                            <option value="{{ $code }}">{{ $code }} — {{ $info['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex;align-items:center;gap:8px;margin-top:24px;">
                    <input type="checkbox" name="is_active" id="field-active" value="1" checked style="width:16px;height:16px;">
                    <label for="field-active" style="font-size:13px;color:#374151;">Active</label>
                </div>

                <div style="grid-column:span 3;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Notes</label>
                    <input type="text" name="notes" id="field-notes" placeholder="Optional notes"
                           style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>

            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit"
                        style="background:#2563eb;color:white;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                    Save
                </button>
                <button type="button" onclick="closeAccountForm()"
                        style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:10px 20px;border-radius:8px;font-size:14px;cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    {{-- Search / filter --}}
    <form method="GET" style="display:flex;gap:10px;margin-bottom:1.5rem;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search bank, account name or number..."
               style="flex:1;min-width:220px;border:1px solid #d1d5db;border-radius:8px;padding:8px 14px;font-size:14px;outline:none;">
        <select name="status"
                style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
            <option value="">All statuses</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit"
                style="background:#f3f4f6;border:1px solid #d1d5db;padding:8px 16px;border-radius:8px;font-size:13px;cursor:pointer;">
            Search
        </button>
    </form>

    {{-- Table --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Bank</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Account name</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Account number</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Type</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Currency</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Status</th>
                <th style="padding:11px 16px;text-align:left;font-size:11px;color:#6b7280;font-weight:500;text-transform:uppercase;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($accounts as $account)
                <tr style="border-top:1px solid #f3f4f6;"
                    onmouseenter="this.style.background='#f9fafb'"
                    onmouseleave="this.style.background=''">
                    <td style="padding:12px 16px;font-weight:500;color:#111827;">{{ $account->bank_name }}</td>
                    <td style="padding:12px 16px;color:#374151;">{{ $account->name }}</td>
                    <td style="padding:12px 16px;color:#6b7280;font-family:monospace;">{{ $account->account_number }}</td>
                    <td style="padding:12px 16px;color:#6b7280;">{{ ucfirst($account->account_type) }}</td>
                    <td style="padding:12px 16px;color:#6b7280;">{{ $account->currency }}</td>
                    <td style="padding:12px 16px;">
                        @if($account->is_active)
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#dcfce7;color:#15803d;">Active</span>
                        @else
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:#f3f4f6;color:#6b7280;">Inactive</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex;gap:10px;align-items:center;">
                            <button type="button"
                                    onclick="openEditForm({{ $account->id }}, '{{ addslashes($account->bank_name) }}', '{{ addslashes($account->name) }}', '{{ addslashes($account->account_number) }}', '{{ $account->account_type }}', '{{ $account->currency }}', '{{ addslashes($account->notes ?? '') }}', {{ $account->is_active ? 'true' : 'false' }})"
                                    style="color:#2563eb;font-size:13px;font-weight:500;background:none;border:none;cursor:pointer;padding:0;">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('admin.bank-accounts.destroy', $account) }}"
                                  onsubmit="return confirm('Delete this bank account?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="color:#f87171;font-size:12px;background:none;border:none;cursor:pointer;">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding:48px;text-align:center;color:#9ca3af;font-size:14px;">
                        <div style="font-size:40px;margin-bottom:12px;">🏦</div>
                        No bank accounts registered yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $accounts->links() }}</div>

    <script>
        const bankAccountStoreUrl = '{{ route('admin.bank-accounts.store') }}';

        function updateBankAccountUrlFor(id) {
            return bankAccountStoreUrl.replace(/\/bank-accounts$/, '/bank-accounts/' + id);
        }

        function resetAccountForm() {
            document.getElementById('account-form').reset();
            document.getElementById('account-form-method').value = '';
            document.getElementById('account-form').action = bankAccountStoreUrl;
            document.getElementById('field-active').checked = true;
        }

        function openAddForm() {
            resetAccountForm();
            document.getElementById('account-form-title').innerText = 'Add bank account';
            document.getElementById('account-form-panel').style.display = 'block';
            document.getElementById('account-form-panel').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function openEditForm(id, bankName, name, accountNumber, accountType, currency, notes, isActive) {
            resetAccountForm();
            document.getElementById('account-form-method').value = 'PUT';
            document.getElementById('account-form').action = updateBankAccountUrlFor(id);
            document.getElementById('field-bank-name').value = bankName;
            document.getElementById('field-name').value = name;
            document.getElementById('field-account-number').value = accountNumber;
            document.getElementById('field-account-type').value = accountType;
            document.getElementById('field-currency').value = currency;
            document.getElementById('field-notes').value = notes;
            document.getElementById('field-active').checked = isActive;
            document.getElementById('account-form-title').innerText = 'Edit bank account';
            document.getElementById('account-form-panel').style.display = 'block';
            document.getElementById('account-form-panel').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function closeAccountForm() {
            document.getElementById('account-form-panel').style.display = 'none';
        }

        @if($errors->any())
            document.getElementById('account-form-panel').style.display = 'block';
        @endif
    </script>

@endsection
