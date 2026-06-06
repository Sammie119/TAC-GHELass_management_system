@extends('layouts.admin')
@section('page-title', 'Settings')
@section('content')

    <div style="margin-bottom:1.5rem;">
        <h2 style="font-size:18px;font-weight:600;color:#111827;">System Settings</h2>
        <p style="font-size:13px;color:#9ca3af;margin-top:2px;">
            Manage departments, income categories and expense categories
        </p>
    </div>

    <div style="display:grid;gap:24px;">
        <style>@media(min-width:1024px){.settings-grid{grid-template-columns:1fr 1fr !important;}}</style>
        <div style="display:grid;gap:24px;" class="settings-grid">

            {{-- Departments --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                    <div>
                        <h3 style="font-size:15px;font-weight:600;color:#111827;">Departments / Ministries</h3>
                        <p style="font-size:12px;color:#9ca3af;margin-top:2px;">Used in member profiles</p>
                    </div>
                    <span style="background:#dbeafe;color:#2563eb;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:500;">
                {{ count($departments) }} items
            </span>
                </div>

                <form method="POST" action="{{ route('admin.settings.departments') }}">
                    @csrf
                    <div id="dept-list" style="display:flex;flex-direction:column;gap:8px;margin-bottom:14px;">
                        @foreach($departments as $dept)
                            <div style="display:flex;gap:6px;align-items:center;" class="dept-row">
                                <input type="text" name="departments[]" value="{{ $dept }}"
                                       style="flex:1;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;outline:none;box-sizing:border-box;"
                                       onfocus="this.style.borderColor='#3b82f6'"
                                       onblur="this.style.borderColor='#d1d5db'"
                                       required>
                                <button type="button" onclick="removeRow(this)"
                                        style="color:#f87171;background:none;border:1px solid #fecaca;border-radius:6px;width:32px;height:34px;cursor:pointer;flex-shrink:0;">
                                    ✕
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div style="display:flex;gap:8px;margin-bottom:16px;">
                        <button type="button" onclick="addDeptRow()"
                                style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:8px 14px;border-radius:8px;font-size:13px;cursor:pointer;">
                            + Add department
                        </button>
                    </div>

                    <button type="submit"
                            style="width:100%;background:#2563eb;color:white;padding:10px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                        Save departments
                    </button>
                </form>
            </div>

            {{-- Income categories --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                    <div>
                        <h3 style="font-size:15px;font-weight:600;color:#111827;">Income Categories</h3>
                        <p style="font-size:12px;color:#9ca3af;margin-top:2px;">Used in finance income records</p>
                    </div>
                    <span style="background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:500;">
                        {{ count($incomeCategories) }} items
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.settings.income-categories') }}">
                    @csrf

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;margin-bottom:4px;padding:0 40px 0 0;">
                        <p style="font-size:11px;font-weight:500;color:#6b7280;text-transform:uppercase;">Key (no spaces)</p>
                        <p style="font-size:11px;font-weight:500;color:#6b7280;text-transform:uppercase;">Display label</p>
                    </div>

                    <div id="income-cat-list" style="display:flex;flex-direction:column;gap:6px;margin-bottom:14px;">
                        @foreach($incomeCategories as $key => $label)
                            <div style="display:flex;gap:6px;align-items:center;" class="cat-row">
                                <input type="text" name="keys[]" value="{{ $key }}"
                                       style="flex:1;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;font-family:monospace;"
                                       placeholder="key_name" required>
                                <input type="text" name="labels[]" value="{{ $label }}"
                                       style="flex:1;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"
                                       placeholder="Display Label" required>
                                <button type="button" onclick="removeRow(this)"
                                        style="color:#f87171;background:none;border:1px solid #fecaca;border-radius:6px;width:30px;height:32px;cursor:pointer;flex-shrink:0;font-size:12px;">
                                    ✕
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-bottom:14px;">
                        <button type="button" onclick="addCatRow('income-cat-list')"
                                style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:7px 14px;border-radius:8px;font-size:13px;cursor:pointer;">
                            + Add income category
                        </button>
                    </div>

                    <button type="submit"
                            style="width:100%;background:#16a34a;color:white;padding:10px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                        Save income categories
                    </button>
                </form>
            </div>

            {{-- Expense categories --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                    <div>
                        <h3 style="font-size:15px;font-weight:600;color:#111827;">Expense Categories</h3>
                        <p style="font-size:12px;color:#9ca3af;margin-top:2px;">Used in finance expense records</p>
                    </div>
                    <span style="background:#fee2e2;color:#dc2626;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:500;">
                        {{ count($expenseCategories) }} items
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.settings.expense-categories') }}">
                    @csrf

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;margin-bottom:4px;padding:0 40px 0 0;">
                        <p style="font-size:11px;font-weight:500;color:#6b7280;text-transform:uppercase;">Key (no spaces)</p>
                        <p style="font-size:11px;font-weight:500;color:#6b7280;text-transform:uppercase;">Display label</p>
                    </div>

                    <div id="expense-cat-list" style="display:flex;flex-direction:column;gap:6px;margin-bottom:14px;">
                        @foreach($expenseCategories as $key => $label)
                            <div style="display:flex;gap:6px;align-items:center;" class="cat-row">
                                <input type="text" name="keys[]" value="{{ $key }}"
                                       style="flex:1;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;font-family:monospace;"
                                       placeholder="key_name" required>
                                <input type="text" name="labels[]" value="{{ $label }}"
                                       style="flex:1;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"
                                       placeholder="Display Label" required>
                                <button type="button" onclick="removeRow(this)"
                                        style="color:#f87171;background:none;border:1px solid #fecaca;border-radius:6px;width:30px;height:32px;cursor:pointer;flex-shrink:0;font-size:12px;">
                                    ✕
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-bottom:14px;">
                        <button type="button" onclick="addCatRow('expense-cat-list')"
                                style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:7px 14px;border-radius:8px;font-size:13px;cursor:pointer;">
                            + Add expense category
                        </button>
                    </div>

                    <button type="submit"
                            style="width:100%;background:#dc2626;color:white;padding:10px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                        Save expense categories
                    </button>
                </form>
            </div>

            {{-- SMS Balance --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                    <div>
                        <h3 style="font-size:15px;font-weight:600;color:#111827;">SMS Balance</h3>
                        <p style="font-size:12px;color:#9ca3af;margin-top:2px;">Your Mnotify SMS credit balance</p>
                    </div>
                    <button type="button"
                            id="checkBalanceBtn"
                            style="background:#eff6ff;border:1px solid #bfdbfe;color:#2563eb;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:6px;">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Check balance
                    </button>
                </div>

                <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:16px;display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <p style="font-size:12px;color:#9ca3af;margin-bottom:4px;">Available credits</p>
                        <p id="sms_balance"
                           style="font-size:26px;font-weight:800;color:#111827;">
                            —
                        </p>
                    </div>
                    <div style="width:48px;height:48px;background:#dbeafe;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:24px;height:24px;color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                </div>

                <p style="font-size:11px;color:#9ca3af;margin-top:8px;">
                    Top up at <a href="https://portal.smsonlinegh.com/auth/login" target="_blank" style="color:#2563eb;">smsonlinegh.com</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function removeRow(btn) {
            const row = btn.closest('.dept-row, .cat-row');
            if (row) row.remove();
        }

        function addDeptRow() {
            const list = document.getElementById('dept-list');
            const div  = document.createElement('div');
            div.className = 'dept-row';
            div.style.cssText = 'display:flex;gap:6px;align-items:center;';
            div.innerHTML = `
                <input type="text" name="departments[]"
                       style="flex:1;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;outline:none;box-sizing:border-box;"
                       placeholder="Department name" required>
                <button type="button" onclick="removeRow(this)"
                        style="color:#f87171;background:none;border:1px solid #fecaca;border-radius:6px;width:32px;height:34px;cursor:pointer;flex-shrink:0;">
                    ✕
                </button>
            `;
            list.appendChild(div);
            div.querySelector('input').focus();
        }

        function addCatRow(listId) {
            const list = document.getElementById(listId);
            const div  = document.createElement('div');
            div.className = 'cat-row';
            div.style.cssText = 'display:flex;gap:6px;align-items:center;';
            div.innerHTML = `
                <input type="text" name="keys[]"
                       style="flex:1;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;font-family:monospace;"
                       placeholder="key_name" required>
                <input type="text" name="labels[]"
                       style="flex:1;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"
                       placeholder="Display Label" required>
                <button type="button" onclick="removeRow(this)"
                        style="color:#f87171;background:none;border:1px solid #fecaca;border-radius:6px;width:30px;height:32px;cursor:pointer;flex-shrink:0;font-size:12px;">
                    ✕
                </button>
                `;
            list.appendChild(div);
            div.querySelector('input').focus();
        }

        document.getElementById('checkBalanceBtn').addEventListener('click', function () {
            const btn     = this;
            const display = document.getElementById('sms_balance');

            btn.disabled  = true;
            btn.innerText = 'Checking...';

            fetch('/admin/settings/check-sms-balance', {
                method:  'GET',
                cache:   'no-store',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                },
            })
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        display.textContent = data.balance + ' SMS credits';
                        display.style.color = '#16a34a';
                    } else {
                        display.textContent = 'Error: ' . data.balance;
                        display.style.color = '#dc2626';
                    }
                })
                .catch(error => {
                    console.error(error);
                    display.textContent = 'Unable to retrieve balance';
                    display.style.color = '#dc2626';
                })
                .finally(() => {
                    btn.disabled  = false;
                    btn.innerText = 'Check';
                });
        });
    </script>

@endsection
