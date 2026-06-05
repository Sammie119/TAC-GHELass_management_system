@extends('layouts.admin')
@section('page-title', 'Edit User')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Edit User</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">{{ $user->name }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}"
           style="border:1px solid #d1d5db;color:#374151;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
            ← Back to users
        </a>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:12px 16px;border-radius:8px;margin-bottom:1.5rem;font-size:14px;">
            <ul style="list-style:disc;padding-left:20px;">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div style="display:grid;gap:24px;">
        <style>@media(min-width:1024px){.user-edit-grid{grid-template-columns:2fr 1fr !important;}}</style>
        <div style="display:grid;gap:24px;" class="user-edit-grid">

            {{-- Left: Profile update --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                {{-- Update details form --}}
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf @method('PUT')

                    <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:24px;">
                        <h3 style="font-size:14px;font-weight:600;color:#374151;margin-bottom:20px;">Account details</h3>

                        <div style="margin-bottom:16px;">
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Full name *</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'"
                                   required>
                        </div>

                        <div style="margin-bottom:20px;">
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Email address *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'"
                                   required>
                        </div>

                        <h3 style="font-size:14px;font-weight:600;color:#374151;margin-bottom:14px;">Role</h3>
                        <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px;">
                            @foreach($roles as $role)
                                <label style="display:flex;align-items:flex-start;gap:10px;padding:12px;border-radius:8px;border:1px solid #e5e7eb;cursor:pointer;"
                                       onmouseenter="this.style.borderColor='#93c5fd'"
                                       onmouseleave="this.style.borderColor='#e5e7eb'">
                                    <input type="radio" name="role" value="{{ $role->name }}"
                                           {{ old('role', $user->roles->first()?->name) === $role->name ? 'checked' : '' }}
                                           style="margin-top:2px;accent-color:#2563eb;">
                                    <div>
                                        <p style="font-size:14px;font-weight:500;color:#111827;">{{ ucfirst($role->name) }}</p>
                                        <p style="font-size:12px;color:#9ca3af;margin-top:2px;">
                                            @if($role->name === 'admin') Full access to all modules
                                            @elseif($role->name === 'usher') Can manage check-ins and visitors
                                            @else Basic member access only
                                            @endif
                                        </p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <button type="submit"
                                style="width:100%;background:#2563eb;color:white;padding:11px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
                            Save changes
                        </button>
                    </div>
                </form>

                {{-- Reset password form --}}
                <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:24px;">
                    <h3 style="font-size:14px;font-weight:600;color:#374151;margin-bottom:16px;">Reset password</h3>

                    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                        @csrf

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                            <div>
                                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">New password *</label>
                                <input type="password" name="password"
                                       placeholder="Min. 8 characters"
                                       style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;box-sizing:border-box;"
                                       onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'"
                                       required>
                            </div>
                            <div>
                                <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Confirm password *</label>
                                <input type="password" name="password_confirmation"
                                       placeholder="Repeat password"
                                       style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;box-sizing:border-box;"
                                       onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'"
                                       required>
                            </div>
                        </div>

                        <button type="submit"
                                style="background:#f3f4f6;border:1px solid #d1d5db;color:#374151;padding:9px 20px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;">
                            Reset password
                        </button>
                    </form>
                </div>

            </div>

            {{-- Right: Info card --}}
            <div style="display:flex;flex-direction:column;gap:16px;">

                <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:24px;text-align:center;">
                    <div style="width:60px;height:60px;border-radius:50%;background:#2563eb;display:flex;align-items:center;justify-content:center;color:white;font-size:20px;font-weight:700;margin:0 auto 12px;">
                        {{ strtoupper(substr($user->name,0,2)) }}
                    </div>
                    <p style="font-size:16px;font-weight:600;color:#111827;">{{ $user->name }}</p>
                    <p style="font-size:13px;color:#9ca3af;margin-top:2px;">{{ $user->email }}</p>
                    @foreach($user->roles as $role)
                        <span style="display:inline-block;margin-top:8px;padding:3px 14px;border-radius:20px;font-size:12px;font-weight:500;
                {{ $role->name === 'admin'  ? 'background:#dbeafe;color:#1d4ed8;' : '' }}
                {{ $role->name === 'usher'  ? 'background:#dcfce7;color:#15803d;' : '' }}
                {{ $role->name === 'member' ? 'background:#fef3c7;color:#d97706;' : '' }}">
                {{ ucfirst($role->name) }}
            </span>
                    @endforeach
                </div>

                <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;padding:20px;">
                    <h3 style="font-size:12px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:14px;">Account info</h3>
                    <div style="display:flex;flex-direction:column;gap:10px;font-size:14px;">
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Joined</span>
                            <span style="font-weight:500;color:#111827;">{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#9ca3af;">Last login</span>
                            <span style="font-weight:500;color:#111827;">
                        {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Never' }}
                    </span>
                        </div>
                    </div>
                </div>

                @if($user->id !== auth()->id())
                    <div style="background:white;border-radius:12px;border:1px solid #fecaca;padding:20px;">
                        <p style="font-size:13px;font-weight:600;color:#dc2626;margin-bottom:8px;">Danger zone</p>
                        <p style="font-size:12px;color:#9ca3af;margin-bottom:12px;">
                            Deleting this user will remove their account permanently.
                        </p>
                        <button onclick="document.getElementById('delete-form').submit()"
                                style="width:100%;border:1px solid #fecaca;color:#ef4444;padding:9px;border-radius:8px;font-size:13px;background:none;cursor:pointer;">
                            Delete user
                        </button>
                    </div>
                @endif

            </div>

        </div>
    </div>

    <form id="delete-form" method="POST" action="{{ route('admin.users.destroy', $user) }}"
          onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
        @csrf @method('DELETE')
    </form>

@endsection
