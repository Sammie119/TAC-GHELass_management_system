@extends('layouts.admin')
@section('page-title', 'Users & Roles')
@section('content')

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#111827;">Users & Roles</h2>
            <p style="font-size:13px;color:#9ca3af;margin-top:2px;">{{ $users->total() }} system users</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           style="background:#2563eb;color:white;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
            + Add User
        </a>
    </div>

    {{-- Role legend --}}
    <div style="display:flex;gap:10px;margin-bottom:1.5rem;flex-wrap:wrap;">
        @foreach($roles as $role)
            <span style="padding:4px 14px;border-radius:20px;font-size:12px;font-weight:500;
            {{ $role->name === 'admin'  ? 'background:#dbeafe;color:#1d4ed8;' : '' }}
            {{ $role->name === 'usher'  ? 'background:#dcfce7;color:#15803d;' : '' }}
            {{ $role->name === 'membership' ? 'background:#fef3c7;color:#d97706;' : '' }}
            {{ $role->name === 'finance'  ? 'background:#fef2f2;color:#7f1d1d;' : '' }}">
        {{ ucfirst($role->name) }}
    </span>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" style="display:flex;gap:12px;margin-bottom:1.5rem;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name or email..."
               style="flex:1;min-width:200px;border:1px solid #d1d5db;border-radius:8px;padding:8px 14px;font-size:14px;outline:none;">
        <select name="role"
                style="border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:14px;outline:none;">
            <option value="">All roles</option>
            @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
        <button type="submit"
                style="background:#f3f4f6;border:1px solid #d1d5db;padding:8px 16px;border-radius:8px;font-size:14px;cursor:pointer;">
            Filter
        </button>
        @if(request('search') || request('role'))
            <a href="{{ route('admin.users.index') }}"
               style="border:1px solid #d1d5db;color:#6b7280;padding:8px 16px;border-radius:8px;font-size:14px;text-decoration:none;">
                Clear
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div style="background:white;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
            <thead style="background:#f9fafb;">
            <tr>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">User</th>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Role</th>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Last login</th>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Joined</th>
                <th style="padding:12px 20px;text-align:left;font-size:12px;color:#6b7280;font-weight:500;text-transform:uppercase;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                @if($user->id !== 1)
                    <tr style="border-top:1px solid #f3f4f6;"
                        onmouseenter="this.style.background='#f9fafb'"
                        onmouseleave="this.style.background=''">
                        <td style="padding:14px 20px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:50%;background:#2563eb;display:flex;align-items:center;justify-content:center;color:white;font-size:13px;font-weight:600;flex-shrink:0;">
                                    {{ strtoupper(substr($user->name,0,2)) }}
                                </div>
                                <div>
                                    <p style="font-weight:500;color:#111827;">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span style="font-size:11px;background:#eff6ff;color:#2563eb;padding:2px 8px;border-radius:10px;margin-left:6px;">You</span>
                                        @endif
                                    </p>
                                    <p style="font-size:12px;color:#9ca3af;">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td style="padding:14px 20px;">
                            @foreach($user->roles as $role)
                                <span style="padding:3px 12px;border-radius:20px;font-size:12px;font-weight:500;
                            {{ $role->name === 'admin'  ? 'background:#dbeafe;color:#1d4ed8;' : '' }}
                            {{ $role->name === 'usher'  ? 'background:#dcfce7;color:#15803d;' : '' }}
                            {{ $role->name === 'membership' ? 'background:#fef3c7;color:#d97706;' : '' }}
                            {{ $role->name === 'finance'  ? 'background:#fef2f2;color:#7f1d1d;' : '' }}">
                            {{ ucfirst($role->name) }}
                        </span>
                            @endforeach
                        </td>
                        <td style="padding:14px 20px;color:#6b7280;font-size:13px;">
                            {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Never' }}
                        </td>
                        <td style="padding:14px 20px;color:#6b7280;font-size:13px;">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td style="padding:14px 20px;">
                            <div style="display:flex;gap:12px;align-items:center;">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   style="color:#2563eb;font-size:13px;text-decoration:none;">Edit</a>

                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Delete {{ $user->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                style="color:#f87171;font-size:13px;background:none;border:none;cursor:pointer;padding:0;">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="5" style="padding:48px 20px;text-align:center;color:#9ca3af;font-size:14px;">
                        No users found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">{{ $users->links() }}</div>

@endsection
