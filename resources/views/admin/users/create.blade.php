@extends('layouts.admin')
@section('page-title', 'Add User')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Add New User</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">Create an admin or usher account</p>
        </div>
        <a href="{{ route('admin.users.index') }}"
           style="border:1px solid #d1d5db;color:#374151;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
            ← Back to users
        </a>
    </div>

    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;">
            <p style="font-weight:500;margin-bottom:4px;">Please fix the following:</p>
            <ul style="list-style:disc;padding-left:20px;">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div style="display:grid;gap:24px;">
            <style>@media(min-width:1024px){.user-create-grid{grid-template-columns:2fr 1fr !important;}}</style>
            <div style="display:grid;gap:24px;" class="user-create-grid">

                {{-- Main fields --}}
                <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:24px;">
                    <h3 style="font-size:14px;font-weight:600;color:#374151;margin-bottom:20px;">Account details</h3>

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                            Full name <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="e.g. John Mensah"
                               style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;box-sizing:border-box;"
                               onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px #bfdbfe'"
                               onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                               required>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                            Email address <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="e.g. john@church.com"
                               style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;box-sizing:border-box;"
                               onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px #bfdbfe'"
                               onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                               required>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                                Password <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="password" name="password"
                                   placeholder="Min. 8 characters"
                                   style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px #bfdbfe'"
                                   onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                                   required>
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                                Confirm password <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="password" name="password_confirmation"
                                   placeholder="Repeat password"
                                   style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px #bfdbfe'"
                                   onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                                   required>
                        </div>
                    </div>
                </div>

                {{-- Role sidebar --}}
                <div style="display:flex;flex-direction:column;gap:16px;">

                    <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:24px;">
                        <h3 style="font-size:14px;font-weight:600;color:#374151;margin-bottom:16px;">
                            Assign role <span style="color:#ef4444;">*</span>
                        </h3>

                        <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px;">
                            @foreach($roles as $role)
                                <label style="display:flex;align-items:flex-start;gap:10px;padding:12px;border-radius:8px;border:1px solid #e5e7eb;cursor:pointer;transition:border-color 0.15s;"
                                       onmouseenter="this.style.borderColor='#93c5fd'"
                                       onmouseleave="this.style.borderColor='#e5e7eb'">
                                    <input type="radio" name="role" value="{{ $role->name }}"
                                           {{ old('role') === $role->name ? 'checked' : '' }}
                                           style="margin-top:2px;accent-color:#2563eb;">
                                    <div>
                                        <p style="font-size:14px;font-weight:500;color:#111827;">{{ ucfirst($role->name) }}</p>
                                        <p style="font-size:12px;color:#9ca3af;margin-top:2px;">
                                            @if($role->name === 'admin')
                                                Full access to all modules
                                            @elseif($role->name === 'usher')
                                                Can manage check-ins and visitors
                                            @elseif($role->name === 'finance')
                                                Can manage all income and expences
                                            @elseif($role->name === 'membership')
                                                Can manage Membership and cell groups
                                            @else
                                                Basic member access only
                                            @endif
                                        </p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <button type="submit"
                                style="width:100%;background:#2563eb;color:white;padding:11px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                            Create user
                        </button>
                        <a href="{{ route('admin.users.index') }}"
                           style="display:block;text-align:center;margin-top:8px;font-size:13px;color:#9ca3af;text-decoration:none;">
                            Cancel
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </form>

@endsection
