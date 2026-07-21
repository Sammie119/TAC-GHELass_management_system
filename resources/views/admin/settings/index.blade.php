@extends('layouts.admin')
@section('page-title', 'Settings')
@section('content')

    <div style="margin-bottom:1.5rem;">
        <h2 style="font-size:18px;font-weight:600;color:#111827;">System Settings</h2>
        <p style="font-size:13px;color:#9ca3af;margin-top:2px;">
            Manage dropdown options and system-level settings
        </p>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Church Information --}}
    <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;margin-bottom:24px;">
        <h3 style="font-size:15px;font-weight:600;color:#111827;margin-bottom:4px;">Church Information</h3>
        <p style="font-size:12px;color:#9ca3af;margin-bottom:16px;">Shown in the sidebar, page titles, and finance documents</p>

        <form method="POST" action="{{ route('admin.settings.church-info') }}" enctype="multipart/form-data">
            @csrf

            <div style="display:grid;grid-template-columns:auto 1fr 1fr;gap:20px;align-items:start;">
                <style>@media(max-width:768px){.church-info-grid{grid-template-columns:1fr !important;}}</style>
                <div style="display:contents;" class="church-info-grid">

                    {{-- Logo preview + upload --}}
                    <div style="display:flex;flex-direction:column;align-items:center;gap:10px;">
                        <div style="width:80px;height:80px;border-radius:12px;background:#f9fafb;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                            @if($churchSetting->logo_path)
                                <img src="{{ Storage::url($churchSetting->logo_path) }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <svg style="width:32px;height:32px;color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 8h16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z"/>
                                </svg>
                            @endif
                        </div>
                        <label style="font-size:12px;color:#2563eb;cursor:pointer;">
                            Change logo
                            <input type="file" name="logo" accept="image/*" style="display:none;" onchange="this.form.querySelector('button[type=submit]').focus()">
                        </label>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Church name *</label>
                        <input type="text" name="name" value="{{ old('name', $churchSetting->name) }}" required
                               style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;box-sizing:border-box;">
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:5px;">Address</label>
                        <textarea name="address" rows="2"
                                  style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:14px;outline:none;resize:none;box-sizing:border-box;">{{ old('address', $churchSetting->address) }}</textarea>
                    </div>

                </div>
            </div>

            <button type="submit"
                    style="margin-top:16px;background:#2563eb;color:white;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                Save
            </button>
        </form>
    </div>

    <div style="display:grid;gap:24px;">
        <style>@media(min-width:1024px){.settings-grid{grid-template-columns:1fr 1fr !important;}}</style>
        <div style="display:grid;gap:24px;" class="settings-grid">

            {{-- Dropdown options --}}
            <div style="background:white;border-radius:14px;border:1px solid #e5e7eb;padding:24px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                    <div>
                        <h3 style="font-size:15px;font-weight:600;color:#111827;">Dropdown Options</h3>
                        <p style="font-size:12px;color:#9ca3af;margin-top:2px;">
                            Departments, income/expense categories, payment methods and currencies
                        </p>
                    </div>
                    <span style="background:#dbeafe;color:#2563eb;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:500;">
                        {{ $dropdownCount }} items
                    </span>
                </div>

                <a href="{{ route('admin.settings.dropdowns.index') }}"
                   style="display:block;text-align:center;width:100%;background:#2563eb;color:white;padding:10px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;box-sizing:border-box;">
                    Manage dropdowns →
                </a>
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
